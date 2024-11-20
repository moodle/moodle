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

namespace core_block\output;

/**
 * This class represents how a block appears on a page.
 *
 * During output, each block instance is asked to return a block_contents object,
 * those are then passed to the $OUTPUT->block function for display.
 *
 * contents should probably be generated using a moodle_block_..._renderer.
 *
 * Other block-like things that need to appear on the page, for example the
 * add new block UI, are also represented as block_contents objects.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core_block
 * @category output
 */
class block_contents {
    /** Used when the block cannot be collapsed **/
    const NOT_HIDEABLE = 0;

    /** Used when the block can be collapsed but currently is not **/
    const VISIBLE = 1;

    /** Used when the block has been collapsed **/
    const HIDDEN = 2;

    /**
     * @var int Used to set $skipid.
     */
    protected static $idcounter = 1;

    /**
     * @var int All the blocks (or things that look like blocks) printed on
     * a page are given a unique number that can be used to construct id="" attributes.
     * This is set automatically be the {@see prepare()} method.
     * Do not try to set it manually.
     */
    public $skipid;

    /**
     * @var int If this is the contents of a real block, this should be set
     * to the block_instance.id. Otherwise this should be set to 0.
     */
    public $blockinstanceid = 0;

    /**
     * @var int If this is a real block instance, and there is a corresponding
     * block_position.id for the block on this page, this should be set to that id.
     * Otherwise it should be 0.
     */
    public $blockpositionid = 0;

    /**
     * @var array An array of attribute => value pairs that are put on the outer div of this
     * block. {@see $id} and {@see $classes} attributes should be set separately.
     */
    public $attributes;

    /**
     * @var string The title of this block. If this came from user input, it should already
     * have had format_string() processing done on it. This will be output inside
     * <h2> tags. Please do not cause invalid XHTML.
     */
    public $title = '';

    /**
     * @var string The label to use when the block does not, or will not have a visible title.
     * You should never set this as well as title... it will just be ignored.
     */
    public $arialabel = '';

    /**
     * @var string HTML for the content
     */
    public $content = '';

    /**
     * @var array An alternative to $content, it you want a list of things with optional icons.
     */
    public $footer = '';

    /**
     * @var string Any small print that should appear under the block to explain
     * to the teacher about the block, for example 'This is a sticky block that was
     * added in the system context.'
     */
    public $annotation = '';

    /**
     * @var int One of the constants NOT_HIDEABLE, VISIBLE, HIDDEN. Whether
     * the user can toggle whether this block is visible.
     */
    public $collapsible = self::NOT_HIDEABLE;

    /**
     * Set this to true if the block is dockable.
     * @var bool
     */
    public $dockable = false;

    /**
     * @var array A (possibly empty) array of editing controls. Each element of
     * this array should be an array('url' => $url, 'icon' => $icon, 'caption' => $caption).
     * $icon is the icon name. Fed to $OUTPUT->image_url.
     */
    public $controls = [];


    /**
     * Create new instance of block content.
     *
     * @param null|array $attributes
     */
    public function __construct(?array $attributes = null) {
        $this->skipid = self::$idcounter;
        self::$idcounter += 1;

        if ($attributes) {
            // Standard block.
            $this->attributes = $attributes;
        } else {
            // Simple "fake" blocks used in some modules and "Add new block" block.
            $this->attributes = ['class' => 'block'];
        }
    }

    /**
     * Add html class to block
     *
     * @param string $class
     */
    public function add_class($class) {
        $this->attributes['class'] .= ' ' . $class;
    }

    /**
     * Check if the block is a fake block.
     *
     * @return boolean
     */
    public function is_fake() {
        return isset($this->attributes['data-block']) && $this->attributes['data-block'] == '_fake';
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(block_contents::class, \block_contents::class);
