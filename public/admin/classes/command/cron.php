<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace core_admin\command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command to run the Moodle cron task.
 *
 * @package    core_admin
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[AsCommand(
    name: 'admin:cron',
    description: 'Run the Moodle cron task',
    aliases: ['cron', 'core:cron'],
)]
class cron extends Command {
    /**
     * Constructor for the cron command.
     *
     * @param \core\task\manager $taskmanager
     * @param \core\cron $cronmanager
     * @param \core\clock $clock
     */
    public function __construct(
        /** @var \core\task\manager The task manager */
        private \core\task\manager $taskmanager,
        /** @var \core\cron The cron manager */
        private \core\cron $cronmanager,
        /** @var \core\clock The time manager */
        private \core\clock $clock,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void {
        $this
            ->addOption(
                'stop',
                's',
                InputOption::VALUE_NONE,
                'Notify all other running cron processes to stop after the current task',
            )
            ->addOption(
                'list',
                'l',
                InputOption::VALUE_NONE,
                'Show the list of currently running tasks and how long they have been running',
            )
            ->addOption(
                'enable',
                'e',
                InputOption::VALUE_NONE,
                'Enable the cron runner',
            )
            ->addOption(
                'disable',
                'd',
                InputOption::VALUE_NONE,
                'Disable the cron runner',
            )
            ->addOption(
                'disable-wait',
                'w',
                InputOption::VALUE_OPTIONAL,
                'Disable the cron runner and do not wait for it to finish after [n] seconds',
                null,
            )
            ->addOption(
                'keep-alive',
                'k',
                InputOption::VALUE_OPTIONAL,
                'Keep the cron runner alive for [n] seconds after it has finished running',
                null,
            )
            ->addOption(
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the cron runner to run even if it is disabled',
            )
            ->addUsage('--list');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int {
        if ($input->getOption('stop')) {
            // By clearing the caches this signals to other running processes
            // to exit after finishing the current task.
            $this->taskmanager->clear_static_caches();

            return 0;
        }

        if ($input->getOption('enable')) {
            // Enable the cron runner.
            $this->taskmanager->enable_cron();
            $output->writeln('<info>Cron runner enabled.</info>');
            return 0;
        }

        if ($input->getOption('disable')) {
            // Disable the cron runner.
            $this->taskmanager->disable_cron();
            $output->writeln('<info>Cron runner disabled.</info>');
            return 0;
        }

        if ($input->getOption('list')) {
            return $this->list_running_tasks($output);
        }

        if ($input->getOption('disable-wait') !== null) {
            return $this->disable_after_wait($input, $output);
        }

        if (!$this->taskmanager->is_cron_enabled() && !$input->getOption('force')) {
            $output->writeln(
                '<error>Cron is currently disabled. Please enable it to run tasks, or pass the --force option</error>',
            );
            return 1;
        }

        // Actually run cron.
        \core\local\cli\shutdown::script_supports_graceful_exit();
        $this->cronmanager->run_main_process($input->getOption('keep-alive'));

        return 0;
    }

    /**
     * List currently running tasks.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    private function list_running_tasks(OutputInterface $output): int {
        // List currently running tasks.
        $time = $this->clock->time();
        $runningtasks = $this->taskmanager->get_running_tasks();
        if (empty($runningtasks)) {
            $output->writeln('<info>No tasks are currently running.</info>');
        } else {
            $table = new Table($output);
            $table->setHeaders([
                'Unique ID',
                'PID',
                'Hostname',
                'Type',
                'Running for',
                'Task',
            ]);
            $table->setRows(array_map(fn ($result) => [
                $result->uniqueid,
                $result->pid,
                $result->hostname,
                $result->type,
                $this->getHelper('formatter')->formatTime($time - $result->timestarted, 4),
                $result->classname,
            ], $runningtasks));
            $table->render();
            $output->writeln(sprintf('<info>%s tasks are currently running.</info>', count($runningtasks)));
        }
        return 0;
    }

    /**
     * Disable the cron runner and wait for tasks to finish.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    private function disable_after_wait(InputInterface $input, OutputInterface $output): int {
        $wait = $input->getOption('disable-wait');
        if ($wait === true) {
            // Default waiting time.
            $waitsec = 600;
        } else {
            $waitsec = (int)$wait;
        }

        $this->taskmanager->disable_cron();
        $tasks = $this->taskmanager->get_running_tasks();

        $output->writeln('<info>Cron has been disabled for the site.</info>');

        if (count($tasks) > 0) {
            $output->writeln('<info>There are currently ' . count($tasks) . ' task(s) running.</info>');
            $output->writeln('<info>Allocating ' . format_time($waitsec) . ' for the tasks to finish.</info>');
        } else {
            $output->writeln('<info>No tasks are currently running.</info>');
            return 0;
        }

        $lastcount = 0;
        $endtime = time() + $waitsec;
        $progressbar = new ProgressBar($output, $waitsec);
        $progressbar->start();

        while (true) {
            $tasks = $this->taskmanager->get_running_tasks();

            if (count($tasks) == 0) {
                $progressbar->finish();
                $output->writeln('');
                $output->writeln('<info>All scheduled and adhoc tasks finished.</info>');
                return 0;
            }

            if (time() >= $endtime) {
                $progressbar->finish();
                $output->writeln('');
                $output->writeln(
                    '<error>Wait time (' . format_time($waitsec) . ') elapsed, but ' .
                    count($tasks) . ' task(s) still running.</error>',
                );
                $this->list_running_tasks($output);
                return 1;
            }

            if (count($tasks) !== $lastcount) {
                $lastcount = count($tasks);
            } else {
                $progressbar->advance();
            }

            sleep(1);
        }
    }
}
