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

namespace core\tests\phpunit;

use PHPUnit\Event\Test\DataProviderMethodFinished;
use PHPUnit\Event\Test\DataProviderMethodFinishedSubscriber;

/**
 * PHPUnit Event Subscriber for DataProviderMethodFinished event.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class data_provider_finished_subscriber implements DataProviderMethodFinishedSubscriber {
    /** @var ?int The number of DB writes */
    private static ?int $dbwrites = null;

    #[\Override]
    public function notify(DataProviderMethodFinished $event): void {
        $resetall = false;

        // Note: All of these checks must be extremely lightweight.
        // This method is called after every single Data Provider.
        if ($this->is_page_set()) {
            $this->trigger_notice($event, "has set the theme");
            $resetall = true;
        }

        if ($this->is_db_written()) {
            $this->trigger_notice($event, "has written to the database");
            $resetall = true;
        }

        if ($resetall) {
            \phpunit_util::reset_all_data();
            $this->update_db_writes();
        }
    }

    /**
     * Check whether the page/theme has been initialised.
     *
     * Data Providers should not rely on the page/theme being set.
     *
     * @return bool
     */
    private function is_page_set(): bool {
        global $PAGE;

        return (new \ReflectionProperty(\moodle_page::class, '_theme'))->getValue($PAGE) !== null;
    }

    /**
     * Check whether the database has been written to.
     *
     * Data Providers should not rely on the database being written to.
     *
     * @return bool
     */
    private function is_db_written(): bool {
        global $DB;

        if (!$DB) {
            // DB not initialised yet.
            return false;
        }

        if (self::$dbwrites === null) {
            self::$dbwrites = $DB->perf_get_writes();
        }

        return $DB->perf_get_writes() > self::$dbwrites;
    }

    /**
     * Update the database write count.
     */
    private function update_db_writes(): void {
        global $DB;

        self::$dbwrites = $DB->perf_get_writes();
    }

    /**
     * Trigger a warning on the CLI.
     *
     * Note: PHPUnit does not let us actually emit a notice or warning for the DataProviderFinished event.
     *
     * @param DataProviderMethodFinished $event
     * @param string $message
     */
    private function trigger_notice(DataProviderMethodFinished $event, string $message): void {
        printf(
            "Warning: Data provider for %s::%s %s%s",
            $event->testMethod()->className(),
            $event->testMethod()->methodName(),
            $message,
            PHP_EOL,
        );
    }
}
