<?php
declare(strict_types=1);
namespace Flowd\SchedulerForker\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Core\Bootstrap;
use TYPO3\CMS\Core\Utility\CommandUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Scheduler;

/**
 * CLI command for the 'scheduler_forker' extension which executes
 */
class SchedulerCommand extends Command
{
    /**
     * Configure the command by defining the name, options and arguments
     */
    public function configure(): void
    {
        $this->setDescription('Start the TYPO3 Scheduler from the command line and run each task in a separate process.');
    }

    /**
     * Execute scheduler tasks
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // Make sure the _cli_ user is loaded
        Bootstrap::initializeBackendAuthentication();

        $scheduler = GeneralUtility::makeInstance(Scheduler::class);

        $returnCode = 0;

        do {
            // Try getting the next task and execute it
            // If there are no more tasks to execute, an exception is thrown by \TYPO3\CMS\Scheduler\Scheduler::fetchTask()
            try {
                $task = $scheduler->fetchTask();
                try {
                    $command = sprintf(
                        '%s %s scheduler:run --task %d 2>&1',
                        PHP_BINARY,
                        $_SERVER['argv'][0],
                        $task->getTaskUid()
                    );
                    $commandResult = [];
                    $commandReturnCode = 0;
                    CommandUtility::exec($command, $commandResult, $commandReturnCode);

                    if ($commandReturnCode !== 0) {
                        $returnCode = 1;
                        $output->writeln(sprintf('Command failed with exit code %d: %s', $commandReturnCode, $command));
                    }
                    if ($commandResult !== null && count($commandResult) !== 0) {
                        $output->writeln($commandResult);
                    }
                } catch (\Exception $e) {
                    // We ignore any exception that may have been thrown during execution,
                    // as this is a background process.
                    // The exception message has been recorded to the database anyway
                    continue;
                }
            } catch (\OutOfBoundsException $e) {
                break;
            } catch (\UnexpectedValueException $e) {
                continue;
            }
        } while (true);

        return $returnCode;
    }
}
