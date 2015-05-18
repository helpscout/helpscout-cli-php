<?php

$app = require __DIR__ . '/../vendor/autoload.php';
$app->add('Console', __DIR__ . '/../src');

use Console\Cli\HelpScout;
use HelpScout\ApiClient;

class WorkingTest extends \PHPUnit_Framework_TestCase
{
    public $console;

    public function setUp()
    {
        $templatePath = realpath(__DIR__ . '/../templates/');
        $configPath   = realpath(__DIR__ . '/.testingConfig.yml');
        $project      = json_decode(file_get_contents(__DIR__ . '/../composer.json'));
        $this->console = new HelpScout();
        $this->console->initialize($configPath, $templatePath, $project);

        $this->console->container['api_key'] = 'invalid_testing_token';
        $mock = $this->getMock('ApiClient');
        $this->console->container['helpscout'] = function ($c) use ($mock) {
            return $mock;
        };
    }
}

/* End of file bootstrap.php */
