<?php

namespace SchulIT\IdpExchangeBundle\Service;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use SchulIT\IdpExchange\Client;
use SchulIT\IdpExchange\ClientException;
use SchulIT\IdpExchangeBundle\Entity\UpdateUser;
use SchulIT\IdpExchangeBundle\Repository\UpdateUserRepositoryInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\User\UserInterface;
use Webmozart\KeyValueStore\Api\KeyValueStore;

class SynchronizationManager {

    private const KEY_LAST_SYNC = 'last_sync';
    private const KEY_LAST_OFFSET = 'last_offset';

    private $userLimit;
    private $client;
    private $userRepository;
    private $keyValueStore;
    private $userLoader;
    private $userUpdater;
    private $logger;

    public function __construct(int $userLimit, Client $client, UpdateUserRepositoryInterface $userRepository, KeyValueStore $keyValueStore,
                                UserLoaderInterface $userLoader, UserUpdaterInterface $userUpdater, LoggerInterface $logger = null) {
        $this->userLimit = $userLimit;
        $this->client = $client;
        $this->userRepository = $userRepository;
        $this->keyValueStore = $keyValueStore;
        $this->userLoader = $userLoader;
        $this->userUpdater = $userUpdater;
        $this->logger = $logger ?? new NullLogger();
    }

    public function getLastSync() {
        return $this->keyValueStore->get(static::KEY_LAST_SYNC);
    }

    public function getCurrentOffset() {
        return $this->keyValueStore->get(static::KEY_LAST_OFFSET);
    }

    public function getUserLimit() {
        return $this->userLimit;
    }

    public function reset() {
        $this->userRepository
            ->removeAll();

        $this->keyValueStore->set(static::KEY_LAST_SYNC, null);
        $this->keyValueStore->set(static::KEY_LAST_OFFSET, null);
    }

    public function countEnqueuedUsers() {
        return $this->userRepository
            ->count();
    }

    /**
     * @return int
     * @throws SyncException
     */
    public function enqueueUsers() {
        $lastSync = $this->keyValueStore->get(static::KEY_LAST_SYNC);
        $currentOffset = $this->keyValueStore->get(static::KEY_LAST_OFFSET);

        if($currentOffset === null) {
            $currentOffset = 0;
        }

        if($lastSync === null) {
            $lastSync = new \DateTime('2018-01-01');
        }

        $users = $this->userLoader->getUsers($this->userLimit, $currentOffset);

        if(count($users) === 0) {
            $this->keyValueStore->set(static::KEY_LAST_SYNC, new \DateTime());
            $this->keyValueStore->set(static::KEY_LAST_OFFSET, 0);
            return 0;
        }

        try {
            $count = 0;

            $usernames = array_map(function(UserInterface $user) {
                return $user->getUsername();
            }, $users);
            $response = $this->client->getUpdatedUsers($usernames, $lastSync);

            foreach($response->users as $userUpdateInfo) {
                $userInQueue = $this->userRepository->findOneByUsername($userUpdateInfo->username);

                if($userInQueue !== null) {
                    $this->logger
                        ->debug(sprintf('User "%s" requested for update, but already in queue. Skip.', $userUpdateInfo->username));

                    continue;
                }

                $updateUser = (new UpdateUser())
                    ->setUsername($userUpdateInfo->username);

                $this->userRepository->persist($updateUser);
                $count++;
            }

            $this->logger
                ->info(sprintf('Enqueued %d user(s) for update', $count));

            if(count($users) <= $this->userLimit) {
                $this->keyValueStore->set(static::KEY_LAST_SYNC, new \DateTime());
                $this->keyValueStore->set(static::KEY_LAST_OFFSET, null);
            } else {
                $this->keyValueStore->set(static::KEY_LAST_OFFSET, $currentOffset + $this->userLimit);
            }

            return $count;
        } catch (\Exception $e) {
            throw new SyncException($e);
        }
    }

    /**
     * @return bool
     * @throws SyncException
     */
    public function updateNextUserInQueue() {
        $nextUser = $this->userRepository
            ->getNextUser();

        if($nextUser === null) {
            $this->logger
                ->debug('No user update requested. Do nothing.');
            return false;
        }

        $this->userRepository
            ->remove($nextUser);

        $username = $nextUser->getUsername();

        try {
            $response = $this->client->getUser($username);
            $this->userUpdater->updateUser($response);

            $this->logger
                ->debug(sprintf('Successfully fetched user updates for user "%s"', $username));

            return true;
        } catch(\Exception $e) {
            $this->logger
                ->error(sprintf('Error fetching user updates for user "%s" (reenqueued user for update)', $username), [
                    'exception' => $e
                ]);

            $updateUser = (new UpdateUser())
                ->setUsername($nextUser->getUsername());

            $this->userRepository->persist($updateUser);

            throw new SyncException($e);
        }
    }
}