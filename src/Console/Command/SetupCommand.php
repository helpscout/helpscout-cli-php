<?php

namespace Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Yaml\Yaml;

class SetupCommand extends AbstractCommand
{
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Configure this Help Scout client')
            ->requireAuth(false);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->app = $this->getApplication();
        $this->dialog = $this->getHelper('question');
        $this->config = $this->app->getConfig();

        // Show the banner
        $output->writeln($this->getBannerMessage(), $this->app->outputFormat);

        // Prompt the values in the config file and update $this->config
        $this
            ->askColorOutput($input, $output)
            ->askApiKey($input, $output)
            ->setCurrentVersion();

        // Add this config to the app, and if chained, the target command will
        // run with this config - for instance if the user issues a command
        // and no config file was found.
        $this->app->config = $this->config;
        $this->app->container['api_key'] = $this->config['ApiKey'];

        // Write this to disk for later use
        $this->app->saveConfig();

        // TODO: Display a confirmation and print path
    }

    private function askColorOutput($input, $output)
    {
        $prompt = 'Enable color output (';
        $prompt .= !empty($this->config['UseColor']) && $this->config['UseColor'] ? 'yes' : 'no';
        $prompt .= '): ';
        $question = new Question($prompt, $this->config['UseColor']);
        $useColor = $this->dialog->ask($input, $output, $question);
        $this->config['UseColor'] = (strtolower($useColor[0]) == 'y');

        return $this;
    }

    private function askApiKey($input, $output)
    {
        $question = new Question('Help Scout API Key: '.$this->config['ApiKey']);
        $this->config['ApiKey'] = $this->dialog->ask($input, $output, $question);

        return $this;
    }

    private function setCurrentVersion()
    {
        // We can use this config to know if we need to make changes in setup.
        // A major version change will trigger the setup command to run again.
        $this->config['ConfigVersion'] = $this->app->project->version;

        return $this;
    }

    private function getBannerMessage()
    {
        return sprintf(
            "%s\n<info>%s</info>\n%s\n Config Path: %s\n",
            str_repeat('—', 80),
            str_pad('Help Scout Client Setup', 80, ' ', STR_PAD_BOTH),
            str_repeat('—', 80),
            $this->app->configFile
        );
    }
}

/* End of file SetupCommand.php */
