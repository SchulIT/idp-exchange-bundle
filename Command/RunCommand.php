<?php

namespace SchoolIT\IdpExchangeBundle\Command;

use SchoolIT\IdpExchangeBundle\Service\SyncException;
use SchoolIT\IdpExchangeBundle\Service\SynchronizationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class RunCommand extends Command {

    private $manager;

    public function __construct(SynchronizationManager $manager, ?string $name = null) {
        parent::__construct($name);

        $this->manager = $manager;
    }

    public function configure() {
        $this
            ->setName('idpexchange:run')
            ->setDescription('Updates the next user in the queue.');
    }

    public function run(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        try {
            $result = $this->manager->updateNextUserInQueue();
            $io->success(sprintf('Updated %d user', $result === true ? 1 : 0));
        } catch (SyncException $e) {
            $this->getApplication()->renderThrowable($e->getPrevious(), $output);
            return 1;
        }

        return 0;
    }
}