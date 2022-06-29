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
 * Builder factory.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\factories;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\builders\exported_posts as exported_posts_builder;
use mod_forum\local\builders\exported_discussion_summaries as exported_discussion_summaries_builder;
use mod_forum\local\builders\exported_discussion as exported_discussion_builder;
use mod_forum\local\factories\vault as vault_factory;
use mod_forum\local\factories\legacy_data_mapper as legacy_data_mapper_factory;
use mod_forum\local\factories\exporter as exporter_factory;
use mod_forum\local\factories\manager as manager_factory;
use renderer_base;

/**
 * Builder factory to construct any builders for forum.
 *
 * See:
 * https://designpatternsphp.readthedocs.io/en/latest/Creational/SimpleFactory/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class builder {
    /** @var legacy_data_mapper_factory $legacydatamapperfactory Legacy data mapper factory */
    private $legacydatamapperfactory;
    /** @var exporter_factory $exporterfactory Exporter factory */
    private $exporterfactory;
    /** @var vault_factory $vaultfactory Vault factory */
    private $vaultfactory;
    /** @var manager_factory $managerfactory Manager factory */
    private $managerfactory;
    /** @var renderer_base $rendererbase Renderer base */
    private $rendererbase;

    /**
     * Constructor.
     *
     * @param legacy_data_mapper_factory $legacydatamapperfactory Legacy data mapper factory
     * @param exporter_factory $exporterfactory Exporter factory
     * @param vault_factory $vaultfactory Vault factory
     * @param manager_factory $managerfactory Manager factory
     * @param renderer_base $rendererbase Renderer base
     */
    public function __construct(
        legacy_data_mapper_factory $legacydatamapperfactory,
        exporter_factory $exporterfactory,
        vault_factory $vaultfactory,
        manager_factory $managerfactory,
        renderer_base $rendererbase
    ) {
        $this->legacydatamapperfactory = $legacydatamapperfactory;
        $this->exporterfactory = $exporterfactory;
        $this->vaultfactory = $vaultfactory;
        $this->managerfactory = $managerfactory;
        $this->rendererbase = $rendererbase;
    }


    /**
     * Get an instance of the exported posts builder.
     *
     * @return exported_posts_builder
     */
    public function get_exported_posts_builder() : exported_posts_builder {
        return new exported_posts_builder(
            $this->rendererbase,
            $this->legacydatamapperfactory,
            $this->exporterfactory,
            $this->vaultfactory,
            $this->managerfactory
        );
    }

    /**
     * Get an instance of the exported discussion summaries builder.
     *
     * @return exported_discussion_summaries_builder
     */
    public function get_exported_discussion_summaries_builder() : exported_discussion_summaries_builder {
        return new exported_discussion_summaries_builder(
            $this->rendererbase,
            $this->legacydatamapperfactory,
            $this->exporterfactory,
            $this->vaultfactory,
            $this->managerfactory
        );
    }

    /**
     * Get an instance of the exported discussion builder.
     *
     * @return exported_discussion_summaries_builder
     */
    public function get_exported_discussion_builder() : exported_discussion_builder {
        return new exported_discussion_builder(
            $this->rendererbase,
            $this->legacydatamapperfactory,
            $this->exporterfactory,
            $this->vaultfactory,
            $this->managerfactory->get_rating_manager()
        );
    }
}
