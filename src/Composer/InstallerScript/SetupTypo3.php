<?php
declare(strict_types=1);
namespace Typo3Console\ComposerAutoSetup\Composer\InstallerScript;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2017 Helmut Hummel <info@helhum.io>
 *  All rights reserved
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use Composer\Autoload\ClassLoader;
use Composer\Script\Event as ScriptEvent;
use Helhum\Typo3Console\Core\Kernel;
use TYPO3\CMS\Core\Core\Bootstrap;
use Typo3Console\ComposerAutoSetup\Composer\ConsoleIo;
use Helhum\Typo3Console\Core\Booting\RunLevel;
use Helhum\Typo3Console\Install\CliSetupRequestHandler;
use Helhum\Typo3Console\Mvc\Cli\CommandDispatcher;
use Helhum\Typo3Console\Mvc\Cli\ConsoleOutput;
use Symfony\Component\Dotenv\Dotenv;
use TYPO3\CMS\Composer\Plugin\Core\InstallerScript;

class SetupTypo3 implements InstallerScript
{
    private static $envVarNames = [
        'TYPO3_INSTALL_DB_USER',
        'TYPO3_INSTALL_DB_PASSWORD',
        'TYPO3_INSTALL_DB_HOST',
        'TYPO3_INSTALL_DB_PORT',
        'TYPO3_INSTALL_DB_UNIX_SOCKET',
        'TYPO3_INSTALL_DB_USE_EXISTING',
        'TYPO3_INSTALL_DB_DBNAME',
        'TYPO3_INSTALL_ADMIN_USER',
        'TYPO3_INSTALL_ADMIN_PASSWORD',
        'TYPO3_INSTALL_SITE_NAME',
        'TYPO3_INSTALL_SITE_SETUP_TYPE',
    ];

    /**
     * @var string
     */
    private $installedFile;

    public function __construct()
    {
        if (class_exists(Dotenv::class)) {
            $this->installedFile = getenv('TYPO3_PATH_COMPOSER_ROOT') . '/.env';
        } else {
            $this->installedFile = getenv('TYPO3_PATH_COMPOSER_ROOT') . '/.installed';
        }
    }

    /**
     * @param ScriptEvent $event
     * @return bool
     */
    private function shouldRun(ScriptEvent $event): bool
    {
        return $event->isDevMode()
            && !file_exists($this->installedFile);
    }

    /**
     * Call the TYPO3 setup
     *
     * @param ScriptEvent $event
     * @throws \RuntimeException
     * @return bool
     * @internal
     */
    public function run(ScriptEvent $event): bool
    {
        if (!$this->shouldRun($event)) {
            return true;
        }
        $io = $event->getIO();
        $io->writeError('');
        $io->writeError('<info>Setting up TYPO3</info>');

        $consoleIO = new ConsoleIo($event->getIO());
        $this->ensureTypo3Booted();
        $commandDispatcher = CommandDispatcher::createFromComposerRun($event);
        $setup = new CliSetupRequestHandler(
            new ConsoleOutput($consoleIO->getOutput(), $consoleIO->getInput()),
            $commandDispatcher
        );
        $setup->setup($consoleIO->isInteractive(), $this->populateCommandArgumentsFromEnvironment());
        putenv('TYPO3_IS_SET_UP=1');
        file_put_contents($this->installedFile, '');

        return true;
    }

    /**
     * @return array
     */
    protected function populateCommandArgumentsFromEnvironment()
    {
        $envValues = [];
        if (class_exists(Dotenv::class) && file_exists($envInstallFile = getenv('TYPO3_PATH_COMPOSER_ROOT') . '/.env.install')) {
            $envValues = (new Dotenv())->parse(file_get_contents($envInstallFile), $envInstallFile);
        }

        $arguments = [];
        foreach (self::$envVarNames as $varName) {
            if (getenv($varName) !== false) {
                $arguments[$varName] = getenv($varName);
            } elseif (isset($envValues[$varName])) {
                $arguments[$varName] = $envValues[$varName];
            }
        }

        return $arguments;
    }

    /**
     * @return bool
     */
    private function hasTypo3Booted()
    {
        // Since this code is executed in composer runtime,
        // we can safely assume that TYPO3 has not been bootstrapped
        // until this API has been initialized to return true
        return Bootstrap::usesComposerClassLoading();
    }

    private function ensureTypo3Booted()
    {
        if (!$this->hasTypo3Booted()) {
            if (file_exists($autoloadFile = __DIR__ . '/../../../../../autoload.php')) {
                $classLoader = require $autoloadFile;
            } else {
                $classLoader = new ClassLoader();
            }
            $kernel = new Kernel($classLoader);
            $kernel->initialize(RunLevel::LEVEL_COMPILE);
        }
    }
}
