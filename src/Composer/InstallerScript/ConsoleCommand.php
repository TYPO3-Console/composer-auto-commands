<?php
declare(strict_types=1);
namespace Typo3Console\ComposerAutoCommands\Composer\InstallerScript;

/*
 * This file is part of the TYPO3 project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Composer\Script\Event;
use Helhum\Typo3Console\Error\ExceptionRenderer;
use Helhum\Typo3Console\Mvc\Cli\CommandDispatcher;
use Helhum\Typo3Console\Mvc\Cli\FailedSubProcessCommandException;
use TYPO3\CMS\Composer\Plugin\Core\InstallerScript;
use Typo3Console\ComposerAutoCommands\Composer\ConsoleIo;

class ConsoleCommand implements InstallerScript
{
    /**
     * @var string
     */
    private $command;

    /**
     * @var array
     */
    private $arguments;

    /**
     * @var string
     */
    private $message;

    /**
     * @var callable
     */
    private $shouldRun;

    /**
     * @var bool
     */
    private $allowFailure;

    private static $verbosityHint = true;

    public function __construct(
        string $command,
        array $arguments = [],
        string $message = '',
        callable $shouldRun = null,
        bool $allowFailure = true
    ) {
        $this->command = $command;
        $this->arguments = $arguments;
        $this->message = $message;
        $this->shouldRun = $shouldRun ?? function () {
            return true;
        };
        $this->allowFailure = $allowFailure;
    }

    public function run(Event $event): bool
    {
        if (!($this->shouldRun)()) {
            return true;
        }
        $io = new ConsoleIo($event->getIO());
        if ($this->message) {
            $io->writeError(sprintf('<info>%s</info>', $this->message));
        }

        $commandDispatcher = CommandDispatcher::createFromComposerRun($event);
        try {
            $output = $commandDispatcher->executeCommand($this->command, $this->arguments);
            $io->writeError($output, true, $io::VERBOSE);
        } catch (FailedSubProcessCommandException $e) {
            if (!$this->allowFailure) {
                throw $e;
            }
            if (!$this->allowFailure || $io->getOutput()->isVerbose()) {
                (new ExceptionRenderer())->render($e, $io->getOutput());
            } else {
                $messages[] = sprintf(
                    '<error>Executing TYPO3 Console command "%s" failed.</error>',
                    $e->getCommand()
                );
                if (self::$verbosityHint) {
                    $messages[] = sprintf(
                        '<info>For details re-run Composer command with increased verbosity (-vvv).</info>'
                    );
                    self::$verbosityHint = false;
                }
                $io->writeError($messages);
            }
        }

        return true;
    }
}
