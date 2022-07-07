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

/**
 * This script fixes orphaned question categories.
 *
 * Orphaned question categories have had their associated context deleted
 * but the category itself remains in the database with an invalid context.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2013 Tyler Bannister (tyler.bannister@remote-learner.net)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/questionlib.php');

$long = array('fix'  => false, 'help' => false);
$short = array('f' => 'fix', 'h' => 'help');

// Now get cli options.
list($options, $unrecognized) = cli_get_params($long, $short);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    $help =
        "Fix orphaned question categories.

        This scripts detects question categories that have had their
        context deleted, thus severing them from their original purpose.

        This script will find the orphaned categories and delete the unused
        questions in each category found.  Used questions will not be
        deleted, instead they will be moved to a rescue question category.

        Options:
        -h, --help            Print out this help
        -f, --fix             Fix the orphaned question categories in the DB.
                              If not specified only check and report problems to STDERR.
        Example:
        \$sudo -u www-data /usr/bin/php admin/cli/fix_orphaned_question_categories.php
        \$sudo -u www-data /usr/bin/php admin/cli/fix_orphaned_question_categories.php -f
        ";

    echo $help;
    die;
}

cli_heading('Checking for orphaned categories');


$sql = 'SELECT qc.id, qc.contextid, qc.name
          FROM {question_categories} qc
     LEFT JOIN {context} c ON qc.contextid = c.id
         WHERE c.id IS NULL';
$categories = $DB->get_recordset_sql($sql);

$i = 0;
foreach ($categories as $category) {
    $i += 1;
    echo "Found orphaned category: {$category->name}\n";
    if (!empty($options['fix'])) {
        echo "Cleaning...";
        // One transaction per category.
        $transaction = $DB->start_delegated_transaction();
        question_category_delete_safe($category);
        $transaction->allow_commit();
        echo "  Done!\n";
    }
}

if (($i > 0) && !empty($options['fix'])) {
    echo "Found and removed {$i} orphaned question categories\n";
} else if ($i > 0) {
    echo "Found {$i} orphaned question categories. To fix, run:\n";
    echo "\$sudo -u www-data /usr/bin/php admin/cli/fix_orphaned_question_categories.php --fix\n";
} else {
    echo "No orphaned question categories found.\n";
}


$categories->close();
