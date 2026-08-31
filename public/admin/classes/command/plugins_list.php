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
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List installed Moodle plugins.
 *
 * @package    core_admin
 * @copyright  2026 Adrian Greeve <adrian@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[AsCommand(
    name: 'admin:plugins:list',
    description: 'List installed Moodle plugins',
)]
class plugins_list extends Command {
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
            ->addOption(
                'contrib',
                null,
                InputOption::VALUE_NONE,
                'List only third-party (contributed) plugins',
            )
            ->addOption(
                'missing',
                null,
                InputOption::VALUE_NONE,
                'List only plugins missing from disk',
            )
            ->setHelp(<<<'EOT'
                The <info>admin:plugins:list</info> command lists installed Moodle plugins.

                Without options all plugins are listed:
                  <info>php bin/moodle admin:plugins:list</info>

                List only third-party (contributed) plugins:
                  <info>php bin/moodle admin:plugins:list --contrib</info>

                List only plugins whose source directory is missing from disk:
                  <info>php bin/moodle admin:plugins:list --missing</info>

                Options can be combined to show contributed plugins that are also missing:
                  <info>php bin/moodle admin:plugins:list --contrib --missing</info>
                EOT);
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int {
        $contribonly = $input->getOption('contrib');
        $missingonly = $input->getOption('missing');

        $pluginman = $this->pluginmanager;

        $rows = [];
        foreach ($pluginman->get_plugins() as $type => $plugins) {
            foreach ($plugins as $name => $plugin) {
                if ($contribonly && $plugin->is_standard()) {
                    continue;
                }

                $status = $plugin->get_status();

                if ($missingonly && $status !== \core\plugin_manager::PLUGIN_STATUS_MISSING) {
                    continue;
                }

                $rows[] = [$plugin->component, $plugin->displayname, $status];
            }
        }

        if (empty($rows)) {
            $output->writeln('<comment>No plugins match the given criteria.</comment>');
            return Command::SUCCESS;
        }

        $table = new Table($output);
        $table->setHeaders(['Component', 'Name', 'Status']);
        $table->setRows($rows);
        $table->render();

        $output->writeln(sprintf('<info>%d plugin(s) listed.</info>', count($rows)));

        return Command::SUCCESS;
    }
}
