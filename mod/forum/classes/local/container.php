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
 * Container class.
 *
 * @package    mod_forum
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\factories\renderer as renderer_factory;
use mod_forum\local\factories\legacy_data_mapper as legacy_data_mapper_factory;
use mod_forum\local\factories\entity as entity_factory;
use mod_forum\local\factories\exporter as exporter_factory;
use mod_forum\local\factories\manager as manager_factory;
use mod_forum\local\factories\vault as vault_factory;
use mod_forum\local\factories\builder as builder_factory;
use mod_forum\local\factories\url as url_factory;

/**
 * Container class.
 *
 * This class provides helper methods with static configurations to get any
 * of the factories from the "local" namespace.
 *
 * @copyright  2018 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class container {
    /**
     * Create the renderer factory.
     *
     * @return renderer_factory
     */
    public static function get_renderer_factory(): renderer_factory {
        global $PAGE;

        return new renderer_factory(
            self::get_legacy_data_mapper_factory(),
            self::get_exporter_factory(),
            self::get_vault_factory(),
            self::get_manager_factory(),
            self::get_entity_factory(),
            self::get_builder_factory(),
            self::get_url_factory(),
            $PAGE
        );
    }

    /**
     * Create the legacy data mapper factory.
     *
     * @return legacy_data_mapper_factory
     */
    public static function get_legacy_data_mapper_factory(): legacy_data_mapper_factory {
        return new legacy_data_mapper_factory();
    }

    /**
     * Create the exporter factory.
     *
     * @return exporter_factory
     */
    public static function get_exporter_factory(): exporter_factory {
        return new exporter_factory(
            self::get_legacy_data_mapper_factory(),
            self::get_manager_factory(),
            self::get_url_factory(),
            self::get_vault_factory()
        );
    }

    /**
     * Create the vault factory.
     *
     * @return vault_factory
     */
    public static function get_vault_factory(): vault_factory {
        global $DB;

        return new vault_factory(
            $DB,
            self::get_entity_factory(),
            get_file_storage(),
            self::get_legacy_data_mapper_factory()
        );
    }

    /**
     * Create the manager factory.
     *
     * @return manager_factory
     */
    public static function get_manager_factory(): manager_factory {
        return new manager_factory(
            self::get_legacy_data_mapper_factory()
        );
    }

    /**
     * Create the entity factory.
     *
     * @return entity_factory
     */
    public static function get_entity_factory(): entity_factory {
        return new entity_factory();
    }

    /**
     * Create the builder factory.
     *
     * @return builder_factory
     */
    public static function get_builder_factory(): builder_factory {
        global $PAGE;

        return new builder_factory(
            self::get_legacy_data_mapper_factory(),
            self::get_exporter_factory(),
            self::get_vault_factory(),
            self::get_manager_factory(),
            $PAGE->get_renderer('mod_forum')
        );
    }

    /**
     * Create the URL factory.
     *
     * @return url_factory
     */
    public static function get_url_factory(): url_factory {
        return new url_factory(
            self::get_legacy_data_mapper_factory()
        );
    }
}
