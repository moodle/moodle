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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

/**
 * Uninstall one or more Moodle plugins.
 *
 * @package    core_admin
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[AsCommand(
    name: 'admin:plugins:uninstall',
    description: 'Uninstall one or more Moodle plugins',
)]
class plugins_uninstall extends Command {
    #[\Override]
    protected function configure(): void {
        $this
            ->addArgument(
                'plugin',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Plugin component name(s) to uninstall (e.g. mod_assign local_myplugin)',
            )
            ->addOption(
                'yes',
                'y',
                InputOption::VALUE_NONE,
                'Assume yes to the confirmation prompt when uninstalling multiple plugins',
            )
            ->setHelp(<<<'EOT'
                The <info>admin:plugins:uninstall</info> command uninstalls one or more Moodle plugins.

                When a single plugin is specified, the command validates it and uninstalls it immediately.
                When multiple plugins are specified, the command validates all plugins, shows the list to be
                uninstalled, and asks for confirmation before continuing.

                Use <info>--yes</info> to skip the confirmation prompt for multi-plugin uninstalls. This is
                required in non-interactive mode.

                Uninstall a single plugin:
                  <info>php bin/moodle admin:plugins:uninstall local_myplugin</info>

                Uninstall multiple plugins with confirmation:
                  <info>php bin/moodle admin:plugins:uninstall mod_assign local_myplugin</info>

                Uninstall multiple plugins without prompting:
                  <info>php bin/moodle admin:plugins:uninstall mod_assign local_myplugin --yes</info>
                EOT);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $assumeyes = $input->getOption('yes');
        $components = $input->getArgument('plugin');
        $pluginman = $this->get_plugin_manager();
        $plugins = [];

        foreach ($components as $component) {
            $plugin = $pluginman->get_plugin_info($component);

            if ($plugin === null) {
                $output->writeln(sprintf('<error>Unknown plugin: %s</error>', $component));
                return Command::FAILURE;
            }

            if (!$pluginman->can_uninstall_plugin($component)) {
                $output->writeln(sprintf(
                    '<error>Cannot uninstall: %s (%s)</error>',
                    $component,
                    $plugin->displayname,
                ));
                return Command::FAILURE;
            }

            $plugins[$component] = $plugin;
        }

        if (count($plugins) > 1) {
            $output->writeln('<comment>The following plugins will be uninstalled:</comment>');
            foreach ($plugins as $component => $plugin) {
                $output->writeln(sprintf('  - %s (%s)', $component, $plugin->displayname));
            }

            if (!$assumeyes) {
                if (!$input->isInteractive()) {
                    $output->writeln(
                        '<error>Confirmation is required when uninstalling multiple plugins in non-interactive mode. Re-run with --yes.</error>',
                    );
                    return Command::FAILURE;
                }

                $question = new ConfirmationQuestion('<question>Continue with uninstall? [y/N]</question> ', false);
                if (!$this->getHelper('question')->ask($input, $output, $question)) {
                    $output->writeln('<info>Aborted. No plugins were uninstalled.</info>');
                    return Command::SUCCESS;
                }
            }
        }

        $exitcode = Command::SUCCESS;
        foreach ($plugins as $component => $plugin) {
            $output->writeln(sprintf(
                '<info>Uninstalling: %s (%s)</info>',
                $component,
                $plugin->displayname,
            ));
            $progress = $this->make_progress_trace($output);
            $result = $pluginman->uninstall_plugin($component, $progress);
            if (!$result) {
                $output->writeln(sprintf(
                    '<error>Failed to uninstall: %s (%s)</error>',
                    $component,
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
     * Return the plugin manager instance.
     *
     * Overridable in tests to inject a mock.
     *
     * @return \core\plugin_manager
     */
    protected function get_plugin_manager(): \core\plugin_manager {
        return \core\plugin_manager::instance();
    }

    /**
     * Build a buffered progress trace that forwards output to the Symfony output interface.
     *
     * Using a buffer (passthrough=false) ensures that uninstall progress lines are routed
     * through the Symfony OutputInterface rather than directly to PHP stdout.
     *
     * @param OutputInterface $output
     * @return progress_trace_buffer
     */
    protected function make_progress_trace(OutputInterface $output): progress_trace_buffer {
        return new progress_trace_buffer(new text_progress_trace(), false);
    }
}
