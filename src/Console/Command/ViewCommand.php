<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ViewCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('view')
            ->setDescription('View a conversation')
            ->addArgument(
                'conversation',
                InputArgument::OPTIONAL,
                'Conversation number'
            )
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
    }
}

/* End of file ViewCommand.php */
