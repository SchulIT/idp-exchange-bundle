<?php

namespace SchulIT\IdpExchangeBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use SchulIT\IdpExchangeBundle\Entity\UpdateUser;

class UpdateUserRepository implements UpdateUserRepositoryInterface {

    private $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function getNextUser(): ?UpdateUser {
        $qb = $this->em->createQueryBuilder()
            ->select('u')
            ->from(UpdateUser::class, 'u')
            ->orderBy('u.id', 'asc');

        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function removeAll() {
        $qb = $this->em->createQueryBuilder()
            ->delete(UpdateUser::class, 'u');

        $qb->getQuery()->execute();
    }

    public function count(): int {
        $qb = $this->em->createQueryBuilder()
            ->select('COUNT(1)')
            ->from(UpdateUser::class, 'u');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function persist(UpdateUser $user) {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(UpdateUser $user) {
        $this->em->remove($user);
        $this->em->flush();
    }

    public function findOneByUsername(string $username): ?UpdateUser {
        return $this->em->getRepository(UpdateUser::class)
            ->findOneBy(['username' => $username ]);
    }
}