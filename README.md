# typo3-console/composer-auto-setup

This is a composer package that aims to automate TYPO3 install steps
when installing TYPO3 with composer with the help of [TYPO3 Console](https://github.com/TYPO3-Console/TYPO3-Console).

When doing a `composer install --dev` (`--dev` is default when omitted) command
and neither a `.env` nor `.installed` file is present next to the root composer.json,
a TYPO3 setup is performed from command line. With `composer install --no-dev` this
action is never executed, despite the absence of these files. 

If the above action is not performed, the following TYPO3 Console commands are executed,
when doing `composer install`: 
`install:generatepackagesates`, `install:fixfolderstructure`, `install:extensionsetupifpossible`
The last one is also only executed for dev installs.

See the [command reference](https://github.com/TYPO3-Console/TYPO3-Console/blob/master/Documentation/CommandReference/Index.rst)
for details on these commands.

## Installation

`composer require typo3-console/composer-auto-setup`
