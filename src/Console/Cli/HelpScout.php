<?php

namespace Console\Cli;

use HelpScout\ApiClient;
use Pimple\Container;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class HelpScout extends Application
{
    public $recentCaseLimit = 10;

    public $configFile;

    public function initialize($configFile, $templatePath, $project)
    {
        $runSetup = false;
        $this->configFile = $configFile;

        // Add the composer information for use in version info and such.
        $this->project = $project;

        // Load our application config information
        if (file_exists($configFile)) {
            $this->config = Yaml::parse(file_get_contents($configFile));
        } else {
            $runSetup = true;
            $this->config = $this->getDefaultConfig();
        }


        // Add our dependency injection container
        $this->container = new Container();

        // Setup our Help Scout API client
        $this->container['api_key'] = $this->config['ApiKey'];
        $this->container['helpscout'] = $this->container->factory(function ($c) {
            $helpscout = ApiClient::getInstance();
            $helpscout->setKey($c['api_key']);
            return $helpscout;
        });

        // https://github.com/symfony/Console/blob/master/Output/Output.php
        $this->outputFormat
            = $this->config['UseColor']
            ? OutputInterface::OUTPUT_NORMAL
            : OutputInterface::OUTPUT_PLAIN;

        // We do this now because we've loaded the project info from the composer file
        $this->setName($this->project->description);
        $this->setVersion($this->project->version);

        // Load our commands into the application
        $this->add(new \Console\Command\MailboxesCommand());
        $this->add(new \Console\Command\SetupCommand());
        $this->add(new \Console\Command\VersionCommand());
        $this->add(new \Console\Command\ZenCommand());

        // We'll use [Twig](http://twig.sensiolabs.org/) for template output
        $loader = new \Twig_Loader_Filesystem($templatePath);
        $this->twig = new \Twig_Environment(
            $loader,
            array(
                'cache'            => false,
                'autoescape'       => false,
                'strict_variables' => true, // SET TO TRUE WHILE DEBUGGING TO
                                            // SHOW ALL ERRORS AND INVALID VARS.
            )
        );

        // These are helpers that we use to format output on the cli: styling and padding and such
        $this->twig->addFilter('pad', new \Twig_Filter_Function('Console\Cli\TwigFormatters::strpad'));
        $this->twig->addFilter('style', new \Twig_Filter_Function('Console\Cli\TwigFormatters::style'));
        $this->twig->addFilter('repeat', new \Twig_Filter_Function('str_repeat'));
        $this->twig->addFilter('wrap', new \Twig_Filter_Function('wordwrap'));

        // If the config file is empty, run the setup script here
        // If the config file version is a different major number, run the setup script here
        $currentVersion = explode('.', $this->project->version);
        $configVersion = explode('.', $this->config['ConfigVersion']);
        $majorVersionChange = $currentVersion[0] != $configVersion[0];

        // We need to be able to skip setup for the list and help
        $helpRequested = (
            empty($_SERVER['argv'][1]) || // help is the default command
            in_array($_SERVER['argv'][1], ['list', 'help'])
        );

        if (($runSetup || $majorVersionChange) && !$helpRequested) {
            $command = $this->find('setup');
            $arguments = array(
                'command' => 'setup',
            );
            $input = new ArrayInput($arguments);
            $command->run($input, new ConsoleOutput());
        }
    }

    public function getLongVersion()
    {
        return parent::getLongVersion().' by <comment>Help Scout</comment>';
    }

    public function getDefaultConfig()
    {
        return array(
            'ConfigVersion' => '0.0.1',
            'UseColor'      => true,
            'ApiKey'        => '',
        );
    }

    public function saveConfig()
    {
        // the second param is the depth for starting yaml inline formatting
        $yaml = Yaml::dump($this->config, 2);

        return file_put_contents($this->configFile, $yaml);
    }

    public function registerStyles(&$output)
    {
        // https://github.com/symfony/Console/blob/master/Formatter/OutputFormatterStyle.php
        // http://symfony.com/doc/2.0/components/console/introduction.html#coloring-the-output
        //
        // * <info></info> green
        // * <comment></comment> yellow
        // * <question></question> black text on a cyan background
        // * <alert></alert> yellow
        // * <error></error> white text on a red background
        // * <fire></fire> red text on a yellow background
        // * <notice></notice> blue
        // * <heading></heading> black on white

        $style = new OutputFormatterStyle('red', 'yellow', array('bold'));
        $output->getFormatter()->setStyle('fire', $style);

        $style = new OutputFormatterStyle('blue', 'black', array());
        $output->getFormatter()->setStyle('notice', $style);

        $style = new OutputFormatterStyle('red', 'black', array('bold'));
        $output->getFormatter()->setStyle('alert', $style);

        $style = new OutputFormatterStyle('white', 'black', array('bold'));
        $output->getFormatter()->setStyle('bold', $style);

        $style = new OutputFormatterStyle('black', 'white', array());
        $output->getFormatter()->setStyle('heading', $style);

        $style = new OutputFormatterStyle('blue', 'black', array('bold'));
        $output->getFormatter()->setStyle('logo', $style);

        return $output;
    }

    public function statusStyle($status)
    {
        switch (true) {
            case (strpos(strtolower($status), 'closed') === 0):
                return 'alert';
            case (strpos(strtolower($status), 'open') === 0):
            case (strpos(strtolower($status), 'active') === 0):
                return 'logo';
            // fallthrough to final return
        }

        return 'info';
    }

    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }

        if (null === $output) {
            $output = new ConsoleOutput();
        }

        $this->registerStyles($output);

        // Did they supply a command name?
        $name = $this->getCommandName($input);
        if ($name) {
            // Does the command exist and is not ambiguous?
            try {
                $command = $this->find($name);
            } catch (\Exception $e) {
                exit($e->getMessage()."\n");
            }
        }

        return parent::run($input, $output);
    }
}

/* End of file HelpScout.php */
