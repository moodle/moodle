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
 * Vault factory.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\factories;

defined('MOODLE_INTERNAL') || die();

use mod_forum\local\factories\entity as entity_factory;
use mod_forum\local\vaults\author as author_vault;
use mod_forum\local\vaults\discussion as discussion_vault;
use mod_forum\local\vaults\discussion_list as discussion_list_vault;
use mod_forum\local\vaults\forum as forum_vault;
use mod_forum\local\vaults\post as post_vault;
use mod_forum\local\vaults\post_attachment as post_attachment_vault;
use mod_forum\local\vaults\post_read_receipt_collection as post_read_receipt_collection_vault;
use file_storage;
use moodle_database;

/**
 * Vault factory.
 *
 * See:
 * https://designpatternsphp.readthedocs.io/en/latest/Creational/SimpleFactory/README.html
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class vault {
    /** @var entity_factory $entityfactory Entity factory */
    private $entityfactory;
    /** @var legacy_data_mapper $legacymapper Entity factory */
    private $legacymapper;
    /** @var moodle_database $db A moodle database */
    private $db;
    /** @var file_storage $filestorage A file storage instance */
    private $filestorage;

    /**
     * Constructor.
     *
     * @param moodle_database $db A moodle database
     * @param entity_factory $entityfactory Entity factory
     * @param file_storage $filestorage A file storage instance
     * @param legacy_data_mapper $legacyfactory Datamapper
     */
    public function __construct(moodle_database $db, entity_factory $entityfactory,
        file_storage $filestorage, legacy_data_mapper $legacyfactory) {
        $this->db = $db;
        $this->entityfactory = $entityfactory;
        $this->filestorage = $filestorage;
        $this->legacymapper = $legacyfactory;
    }

    /**
     * Create a forum vault.
     *
     * @return forum_vault
     */
    public function get_forum_vault(): forum_vault {
        return new forum_vault(
            $this->db,
            $this->entityfactory,
            $this->legacymapper->get_legacy_data_mapper_for_vault('forum')
        );
    }

    /**
     * Create a discussion vault.
     *
     * @return discussion_vault
     */
    public function get_discussion_vault(): discussion_vault {
        return new discussion_vault(
            $this->db,
            $this->entityfactory,
            $this->legacymapper->get_legacy_data_mapper_for_vault('discussion')
        );
    }

    /**
     * Create a discussion list vault.
     *
     * @return discussion_list_vault
     */
    public function get_discussions_in_forum_vault(): discussion_list_vault {
        return new discussion_list_vault(
            $this->db,
            $this->entityfactory,
            $this->legacymapper->get_legacy_data_mapper_for_vault('discussion')
        );
    }

    /**
     * Create a post vault.
     *
     * @return post_vault
     */
    public function get_post_vault(): post_vault {
        return new post_vault(
            $this->db,
            $this->entityfactory,
            $this->legacymapper->get_legacy_data_mapper_for_vault('post')
        );
    }

    /**
     * Create an author vault.
     *
     * @return author_vault
     */
    public function get_author_vault(): author_vault {
        return new author_vault(
            $this->db,
            $this->entityfactory,
            $this->legacymapper->get_legacy_data_mapper_for_vault('author')
        );
    }

    /**
     * Create a post read receipt collection vault.
     *
     * @return post_read_receipt_collection_vault
     */
    public function get_post_read_receipt_collection_vault(): post_read_receipt_collection_vault {
        return new post_read_receipt_collection_vault(
            $this->db,
            $this->entityfactory,
            $this->legacymapper->get_legacy_data_mapper_for_vault('post')
        );
    }

    /**
     * Create a post attachment vault.
     *
     * @return post_attachment_vault
     */
    public function get_post_attachment_vault(): post_attachment_vault {
        return new post_attachment_vault(
            $this->filestorage
        );
    }
}
