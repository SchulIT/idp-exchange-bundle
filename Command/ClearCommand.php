<?php

namespace SchoolIT\IdpExchangeBundle\Command;

use SchoolIT\IdpExchangeBundle\Service\SynchronizationManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ClearCommand extends Command {

    private $manager;

    public function __construct(SynchronizationManager $manager, ?string $name = null) {
        parent::__construct($name);

        $this->manager = $manager;
    }

    public function configure() {
        $this
            ->setName('idpexchange:clear')
            ->setDescription('Clears the queue of user which will be updated.');
    }

    public function execute(InputInterface $input, OutputInterface $output) {
        $io = new SymfonyStyle($input, $output);

        if(!$io->confirm('Do you really want to clear the user update queue?')) {
            return;
        }

        $this->manager->reset();

        $io->success('Queue cleared');
    }
}