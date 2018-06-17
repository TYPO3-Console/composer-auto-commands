# Execute TYPO3 Console commands in composer build process

This is a composer package that aims to simplify running [TYPO3 Console](https://github.com/TYPO3-Console/TYPO3-Console)
every time composer dumps autoload information, e.g. during a `composer install` run.

The following TYPO3 Console commands are executed: 
* `install:generatepackagesates`
* `install:fixfolderstructure`
* `install:extensionsetupifpossible`

The last one is skipped for `--no-dev` installs and when TYPO3 appears to not be set up (`LocalConfiguration.php` file is missing).

See the [command reference](https://github.com/TYPO3-Console/TYPO3-Console/blob/master/Documentation/CommandReference/Index.rst)
for details on these commands.

The benefits of using this package over just specifying the console commands in your `composer.json`
scripts sections are:

* Works in diverse environments (OSX, Linux, Windows) and always uses the PHP binary that is used for executing composer
* Can be used as dependency in any package, not only your root package 

## Installation

`composer require typo3-console/composer-auto-commands`
