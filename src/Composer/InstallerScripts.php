<?php
declare(strict_types=1);
namespace Typo3Console\ComposerAutoCommands\Composer;

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

use Composer\Script\Event;
use Typo3Console\ComposerAutoCommands\Composer\InstallerScript\ConsoleCommand;
use TYPO3\CMS\Composer\Plugin\Core\InstallerScriptsRegistration;
use TYPO3\CMS\Composer\Plugin\Core\ScriptDispatcher;

class InstallerScripts implements InstallerScriptsRegistration
{
    public static function register(Event $event, ScriptDispatcher $scriptDispatcher)
    {
        $scriptDispatcher->addInstallerScript(
            new ConsoleCommand('install:generatepackagestates'),
            20
        );
        $scriptDispatcher->addInstallerScript(
            new ConsoleCommand('install:fixfolderstructure'),
            20
        );
        $typo3IsSetUp = getenv('TYPO3_IS_SET_UP') || file_exists(getenv('TYPO3_PATH_ROOT') . '/typo3conf/LocalConfiguration.php');
        if ($typo3IsSetUp && $event->isDevMode()) {
            $scriptDispatcher->addInstallerScript(
                new ConsoleCommand(
                    'database:updateschema',
                    [],
                    'Setting up TYPO3 environment and extensions.'
                ),
                20
            );
            $scriptDispatcher->addInstallerScript(
                new ConsoleCommand('cache:flush'),
                20
            );
            $scriptDispatcher->addInstallerScript(
                new ConsoleCommand('extension:setupactive'),
                20
            );
        }
    }
}
