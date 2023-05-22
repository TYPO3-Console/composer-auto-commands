# Execute TYPO3 Console commands in Composer build process

This is a composer package that aims to simplify running [TYPO3 Console](https://github.com/TYPO3-Console/TYPO3-Console)
every time composer dumps autoload information, e.g. during a `composer install` run.

The following TYPO3 Console commands are executed: 

* `install:generatepackagestates` (only TYPO3 Console lower than 7.0)
* `install:fixfolderstructure`

And in the case TYPO3 appears to be setup properly (`typo3conf/LocalConfiguration.php` or
`config/system/settings.php` file is not missing) and running composer in dev mode
(without `--no-dev`) these commands are also executed:

* `extension:setup` (or `extension:setupactive` in TYPO3 Console versions lower than 7.0)

See the [command reference](https://docs.typo3.org/p/helhum/typo3-console/master/en-us/CommandReference/Index.html)
for details on these commands.

The benefits of using this package over just specifying the console commands in your `composer.json`
scripts sections are:

* Works in diverse environments (OSX, Linux, Windows) and always uses the PHP binary that is used for executing composer
* Can be used as dependency in any package, not only your root package 

## Installation

`composer require typo3-console/composer-auto-commands`
