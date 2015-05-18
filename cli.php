<?php

error_reporting(E_ALL | E_STRICT);

// If the dependencies aren't installed, we have to bail and offer some help.
if (!file_exists(__DIR__.'/vendor/autoload.php')) {
    exit("\nPlease run `composer install` to install dependencies.\n\n");
}

// Bootstrap our console application with the Composer autoloader
$app = require __DIR__.'/vendor/autoload.php';

// Setup the namespace for our own namespace
$app->add('Console', __DIR__.'/src');

// Instantiate our Console application
$console = new Console\Cli\HelpScout();

// Config path can be set with a an ENV var
$configFile = getenv('HELPSCOUT_CONFIG')
    ? getenv('HELPSCOUT_CONFIG')
    : getenv('HOME').'/.helpscout.yml';

// We use Twig templates for much of the console output formatting
$templatePath = __DIR__.'/templates';

// The composer file for showing info and version
$project = json_decode(file_get_contents(__DIR__.'/composer.json'));

// Init the app with these params
$console->initialize($configFile, $templatePath, $project);

// Execute the console app.
$console->run();

/* End of cli.php */
