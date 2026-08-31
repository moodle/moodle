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
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class session_destroy_all
 *
 * @package    core_admin
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
#[AsCommand(
    name: 'admin:session:destroy:all',
    description: 'Destroy all user sessions without confirmation',
)]
class session_destroy_all extends Command {
    #[\Override()]
    public function execute(InputInterface $input, OutputInterface $output): int {
        $output->writeln('<info>Destroying all user sessions...</info>');
        $result = \core\di::get(\core\session\manager::class)->destroy_all();

        if ($result) {
            $output->writeln('<info>All user sessions have been successfully destroyed.</info>');
            return Command::SUCCESS;
        } else {
            $output->writeln('<error>Failed to destroy all user sessions.</error>');
            return Command::FAILURE;
        }
    }
}
