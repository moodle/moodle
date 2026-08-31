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
            ->addArgument(
                'plugin',
                InputArgument::IS_ARRAY | InputArgument::REQUIRED,
                'Plugin component name(s) to uninstall (e.g. mod_assign local_myplugin)',
            )
            ->addOption(
                'assume-yes',
                'y',
                InputOption::VALUE_NONE,
                'Assume yes to the confirmation prompt and uninstall without asking',
            )
            ->setHelp(<<<'EOT'
                The <info>admin:plugins:uninstall</info> command uninstalls one or more Moodle plugins.

                All requested plugins are validated first. If any plugin is unknown or cannot be uninstalled,
                the command fails and no plugin is uninstalled. Once validated, the command always shows the
                list of plugins to be uninstalled and asks for confirmation before continuing, whether one or
                several plugins were requested.

                Use <info>--assume-yes</info> to skip the confirmation prompt. This is required in
                non-interactive mode, where the command fails without making changes if it is not supplied.

                Uninstall a single plugin with confirmation:
                  <info>php bin/moodle admin:plugins:uninstall local_myplugin</info>

                Uninstall multiple plugins with confirmation:
                  <info>php bin/moodle admin:plugins:uninstall mod_assign local_myplugin</info>

                Uninstall without prompting:
                  <info>php bin/moodle admin:plugins:uninstall mod_assign local_myplugin --assume-yes</info>
                EOT);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $assumeyes = $input->getOption('assume-yes');
        $components = $input->getArgument('plugin');
        $pluginman = $this->pluginmanager;
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

        $output->writeln('<comment>The following plugins will be uninstalled:</comment>');
        foreach ($plugins as $component => $plugin) {
            $output->writeln(sprintf('  - %s (%s)', $component, $plugin->displayname));
        }

        if (!$assumeyes) {
            if (!$input->isInteractive()) {
                $output->writeln(
                    '<error>Confirmation is required to uninstall plugins in non-interactive mode. ' .
                    'Re-run with --assume-yes.</error>',
                );
                return Command::FAILURE;
            }

            $question = new ConfirmationQuestion('<question>Continue with uninstall? [y/N]</question> ', false);
            if (!$this->getHelper('question')->ask($input, $output, $question)) {
                $output->writeln('<info>Aborted. No plugins were uninstalled.</info>');
                return Command::SUCCESS;
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
