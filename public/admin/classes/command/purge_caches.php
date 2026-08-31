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
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class purge_caches
 *
 * @package    core_admin
 * @copyright  2025 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[AsCommand(
    name: 'admin:purge-caches',
    description: 'Purge Moodle caches without confirmation',
    aliases: ['purge-caches', 'core:purge-caches'],
)]
class purge_caches extends Command {
    #[\Override]
    protected function configure(): void {
        $this
            ->addOption('muc', null, null, 'Purge all MUC caches (includes lang cache)')
            ->addOption(
                'courses',
                null,
                InputOption::VALUE_OPTIONAL,
                'Purge all course caches (or only those specified by a comma-separated list). For example --courses=4,67,145',
            )
            ->addOption('theme', null, null, 'Purge all theme caches')
            ->addOption('lang', null, null, 'Purge all language caches')
            ->addOption('js', null, null, 'Purge all JavaScript caches')
            ->addOption('filter', null, null, 'Purge all filter caches')
            ->addOption(
                'other',
                null,
                null,
                'Purge all file caches and other miscellaneous caches (may include MUC if using cachestore_file)',
            );
    }

    #[\Override]
    public function execute(InputInterface $input, OutputInterface $output): int {
        $options = [
            'muc' => $input->getOption('muc'),
            'courses' => $input->getOption('courses'),
            'theme' => $input->getOption('theme'),
            'lang' => $input->getOption('lang'),
            'js' => $input->getOption('js'),
            'filter' => $input->getOption('filter'),
            'other' => $input->getOption('other'),
        ];

        $filteredoptions = array_filter($options);
        if (empty($filteredoptions)) {
            $output->writeln('Purging all caches');
        } else {
            $output->writeln('Purging caches with the following options:');
            foreach ($options as $option => $value) {
                $output->writeln(" - $option: " . ($value ? 'enabled' : 'disabled'));
            }
        }
        purge_caches(array_filter($options));

        // At the moment we do not get any status from the purge_caches function,
        // so we cannot return a non-zero exit code.
        // This may change in the future.
        return 0;
    }
}
