<?php

namespace SchulIT\IdpExchangeBundle\Repository;

use SchulIT\IdpExchangeBundle\Entity\UpdateUser;

interface UpdateUserRepositoryInterface {
    public function findOneByUsername(string $username): ?UpdateUser;

    public function getNextUser(): ?UpdateUser;

    public function removeAll();

    public function count(): int;

    public function persist(UpdateUser $user);

    public function remove(UpdateUser $user);
}