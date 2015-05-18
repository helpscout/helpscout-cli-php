<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VersionCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('version')
            ->setDescription('Show version information')
            ->requireAuth(false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $output->writeln($this->app->project->version, $this->app->outputFormat);
    }
}

/* End of file VersionCommand.php */
