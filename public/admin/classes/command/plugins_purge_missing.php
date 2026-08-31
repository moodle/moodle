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

use core\output\progress_trace\progress_trace_buffer;
use core\output\progress_trace\text_progress_trace;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Uninstall all plugins that are missing from disk.
 *
 * @package    core_admin
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[AsCommand(
    name: 'admin:plugins:purge-missing',
    description: 'Uninstall all plugins whose source directory is missing from disk',
)]
class plugins_purge_missing extends Command {
    /**
     * Constructor.
     *
     * @param \core\plugin_manager $pluginmanager The plugin manager.
     */
    public function __construct(
        /** @var \core\plugin_manager The plugin manager. */
        protected \core\plugin_manager $pluginmanager,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void {
        $this
            ->setHelp(<<<'EOT'
                The <info>admin:plugins:purge-missing</info> command uninstalls every plugin whose
                source directory no longer exists on disk.

                The command lists all missing plugins that will be purged and then proceeds immediately.

                Purge all missing plugins:
                  <info>php bin/moodle admin:plugins:purge-missing</info>
                EOT);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $pluginman = $this->pluginmanager;
        $missingplugins = [];
        $exitcode = Command::SUCCESS;

        foreach ($pluginman->get_plugins() as $type => $plugins) {
            foreach ($plugins as $name => $plugin) {
                if ($plugin->get_status() !== \core\plugin_manager::PLUGIN_STATUS_MISSING) {
                    continue;
                }

                $missingplugins[$plugin->component] = $plugin;
            }
        }

        if (!$missingplugins) {
            $output->writeln('<info>No missing plugins found.</info>');
            return Command::SUCCESS;
        }

        $output->writeln('<comment>The following missing plugins will be purged:</comment>');
        foreach ($missingplugins as $component => $plugin) {
            $output->writeln(sprintf('  - %s (%s)', $component, $plugin->displayname));
        }

        foreach ($missingplugins as $plugin) {
            if (!$pluginman->can_uninstall_plugin($plugin->component)) {
                $output->writeln(sprintf(
                    '<error>Cannot uninstall: %s (%s)</error>',
                    $plugin->component,
                    $plugin->displayname,
                ));
                $exitcode = Command::FAILURE;
                continue;
            }

            $output->writeln(sprintf(
                '<info>Uninstalling: %s (%s)</info>',
                $plugin->component,
                $plugin->displayname,
            ));
            $progress = $this->make_progress_trace($output);
            $result = $pluginman->uninstall_plugin($plugin->component, $progress);
            if (!$result) {
                $output->writeln(sprintf(
                    '<error>Failed to uninstall: %s (%s)</error>',
                    $plugin->component,
                    $plugin->displayname,
                ));
                $exitcode = Command::FAILURE;
            }
            $progress->finished();
            $output->write($progress->get_buffer());
        }

        return $exitcode;
    }

    /**
     * Build a buffered progress trace that forwards output to the Symfony output interface.
     *
     * @param OutputInterface $output
     * @return progress_trace_buffer
     */
    protected function make_progress_trace(OutputInterface $output): progress_trace_buffer {
        return new progress_trace_buffer(new text_progress_trace(), false);
    }
}
