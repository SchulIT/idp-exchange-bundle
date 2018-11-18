<?php

namespace SchoolIT\IdpExchangeBundle\Command;

use SchoolIT\IdpExchangeBundle\Service\SynchronizationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class StatusCommand extends Command {

    private $manager;

    public function __construct(SynchronizationManager $manager, ?string $name = null) {
        parent::__construct($name);

        $this->manager = $manager;
    }

    public function configure() {
        $this
            ->setName('idpexchange:status')
            ->setDescription('Show the current status of the IdP Exchange');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        $io->listing([
            sprintf('Configured user limit: %d', $this->manager->getUserLimit()),
            sprintf('Last successful sync: %s', $this->manager->getLastSync() !== null ? $this->manager->getLastSync()->format('d.m.Y H:i:s') : 'never'),
            sprintf('Currently enqueued users: %d', $this->manager->countEnqueuedUsers()),
            sprintf('Current user offset: %d', $this->manager->getCurrentOffset()),
        ]);
    }
}