<?php

namespace SchoolIT\IdpExchangeBundle\Command;

use SchoolIT\IdpExchangeBundle\Service\SyncException;
use SchoolIT\IdpExchangeBundle\Service\SynchronizationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class EnqueueCommand extends Command {
    private $manager;

    public function __construct(SynchronizationManager $synchronizationManager, ?string $name = null) {
        parent::__construct($name);

        $this->manager = $synchronizationManager;
    }

    public function configure() {
        $this
            ->setName('idpexchange:enqueue')
            ->setDescription('Enqueues users which need an update');
    }

    public function run(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        try {
            $count = $this->manager->enqueueUsers();

            $io->success(sprintf('Successfully updated %d user(s)', $count));
        } catch (SyncException $e) {
            $this->getApplication()->renderThrowable($e->getPrevious(), $output);
        }

    }
}