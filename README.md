Help Scout Command Line Tools [![Build Status](https://travis-ci.org/helpscout/helpscout-cli-php.svg)](https://travis-ci.org/helpscout/helpscout-cli-php) [![Code Climate](https://codeclimate.com/repos/555a0ee76956806a8300328e/badges/5256fafc5283b70fc667/gpa.svg)](https://codeclimate.com/repos/555a0ee76956806a8300328e/feed)
================================================================================
> Command line interface for Help Scout :: API example application

A simple command line client application that can be used with Help Scout. This
is built using the [Symfony Console][symfony] and the [PHP API Client][phpapi].

This is an example project used to demonstrate the Help Scout API. It's much
more common to use the API to build:

* Custom Integrations with your other tools
* A synchronization tool
* An intranet dashboard interface
* Or a million other things!

But, we love programmers, and there's something magic about having Help Scout on
the command line too!

## Quick Start

Run the `bin/helpscout` file from the command line. It will prompt you for your
API key. It will store an API token in a `~/.helpscout.yml` file.

## Help
```
Help Scout Command Line Client version 0.0.1 by Help Scout

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  customer   Show customer details
  help       Displays help for a command
  list       Lists commands
  mailbox    Show mailbox details
  mailboxes  List all mailboxes
  setup      Configure this Help Scout client
  version    Show version information
  zen        Display a Zen koan, used as an API heartbeat
```
## Examples

## Dependencies

This app requires PHP 5.5 and Composer.

[symfony]: http://symfony.com/doc/current/components/console.html
[phpapi]: https://github.com/helpscout/helpscout-api-php
