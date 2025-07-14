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
 * H5P factory class.
 * This class is used to decouple the construction of H5P related objects.
 *
 * @package    core_h5p
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_h5p;

defined('MOODLE_INTERNAL') || die();

use core_h5p\local\library\autoloader;
use Moodle\H5PContentValidator as content_validator;
use Moodle\H5peditor;
use Moodle\H5PStorage as storage;
use Moodle\H5PValidator as validator;

/**
 * H5P factory class.
 * This class is used to decouple the construction of H5P related objects.
 *
 * @package    core_h5p
 * @copyright  2019 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class factory {

    /** @var \core_h5p\local\library\autoloader The autoloader */
    protected $autoloader;

    /** @var \core_h5p\core The Moodle H5PCore implementation */
    protected $core;

    /** @var \core_h5p\framework The Moodle H5PFramework implementation */
    protected $framework;

    /** @var \core_h5p\file_storage The Moodle H5PStorage implementation */
    protected $storage;

    /** @var validator The Moodle H5PValidator implementation */
    protected $validator;

    /** @var content_validator The Moodle H5PContentValidator implementation */
    protected $content_validator;

    /** @var editor_framework The Moodle H5peditorStorage implementation */
    protected $editorframework;

    /** @var H5peditor */
    protected $editor;

    /** @var editor_ajax The Moodle H5PEditorAjaxInterface implementation */
    protected $editorajaxinterface;

    /**
     * factory constructor.
     */
    public function __construct() {
        // Loading classes we need from H5P third party library.
        $this->autoloader = new autoloader();
        autoloader::register();
    }

    /**
     * Returns an instance of the \core_h5p\local\library\autoloader class.
     *
     * @return \core_h5p\local\library\autoloader
     */
    public function get_autoloader(): autoloader {
        return $this->autoloader;
    }

    /**
     * Returns an instance of the \core_h5p\framework class.
     *
     * @return \core_h5p\framework
     */
    public function get_framework(): framework {
        if (null === $this->framework) {
            $this->framework = new framework();
        }

        return $this->framework;
    }

    /**
     * Returns an instance of the \core_h5p\core class.
     *
     * @return \core_h5p\core
     */
    public function get_core(): core {
        if (null === $this->core) {
            $fs = new \core_h5p\file_storage();
            $language = \core_h5p\framework::get_language();
            $context = \context_system::instance();

            $url = \moodle_url::make_pluginfile_url($context->id, 'core_h5p', '', null,
                '', '')->out();

            $this->core = new core($this->get_framework(), $fs, $url, $language, true);
        }

        return $this->core;
    }

    /**
     * Returns an instance of the H5PStorage class.
     *
     * @return \Moodle\H5PStorage
     */
    public function get_storage(): storage {
        if (null === $this->storage) {
            $this->storage = new storage($this->get_framework(), $this->get_core());
        }

        return $this->storage;
    }

    /**
     * Returns an instance of the H5PValidator class.
     *
     * @return \Moodle\H5PValidator
     */
    public function get_validator(): validator {
        if (null === $this->validator) {
            $this->validator = new validator($this->get_framework(), $this->get_core());
        }

        return $this->validator;
    }

    /**
     * Returns an instance of the H5PContentValidator class.
     *
     * @return Moodle\H5PContentValidator
     */
    public function get_content_validator(): content_validator {
        if (null === $this->content_validator) {
            $this->content_validator = new content_validator($this->get_framework(), $this->get_core());
        }

        return $this->content_validator;
    }

    /**
     * Returns an instance of H5Peditor class.
     *
     * @return H5peditor
     */
    public function get_editor(): H5peditor {
        if (null === $this->editor) {
            if (empty($this->editorframework)) {
                $this->editorframework = new editor_framework();
            }

            if (empty($this->editorajaxinterface)) {
                $this->editorajaxinterface = new editor_ajax();
            }

            if (empty($this->editor)) {
                $this->editor = new H5peditor($this->get_core(), $this->editorframework, $this->editorajaxinterface);
            }
        }

        return $this->editor;
    }
}
