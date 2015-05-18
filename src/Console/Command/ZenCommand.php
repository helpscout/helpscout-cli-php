<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ZenCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('zen')
            ->setDescription('Display a Zen koan, used as an API heartbeat')
            ->requireAuth(true);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $curl = new \Curl();
        $response = $curl->get('https://api.github.com/zen');
        if ($response->headers['Status-Code'] != 200) {
            $output->writeln('GitHub has failed with :'.$response->headers['Status-Code']);
        }
        $output->writeln($response->body);
    }
}

/* End of file ZenCommand.php */
