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
 * myprofile renderer.
 *
 * @package    theme_adaptable
 * @copyright  2015-2019 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2019 Fernando Acedo (3-bits.com)
 * @copyright  2017-2019 Manoj Solanki (Coventry University)
 * @copyright  2019 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output\core_user\myprofile;

defined('MOODLE_INTERNAL') || die;

use core_user\output\myprofile\category;
use core_user\output\myprofile\node;
use core_user\output\myprofile\tree;

require_once($CFG->dirroot . '/user/lib.php');

/**
 * myprofile renderer.
 */
class renderer extends \core_user\output\myprofile\renderer {
    /** @var object $localmyprofilerenderer */
    private $localmyprofilerenderer = null;

    /**
     * Constructor for class.
     *
     * @param moodle_page $page
     * @param string $target
     *
     * @return Obj
     */
    public function __construct(\moodle_page $page, $target) {
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            $this->localmyprofilerenderer = $localtoolbox->get_userprofile_renderer($page, $target);
        }
        parent::__construct($page, $target);
    }

    /**
     * Render the whole tree.
     *
     * @param tree $tree
     *
     * @return string
     */
    public function render_tree(tree $tree) {
        if (is_null($this->localmyprofilerenderer)) {
            return parent::render_tree($tree);
        } else {
            return $this->localmyprofilerenderer->render_tree($tree);
        }
    }

    /**
     * Render a category.
     *
     * @param category $category
     *
     * @return string
     */
    public function render_category(category $category) {
        if (is_null($this->localmyprofilerenderer)) {
            return parent::render_category($category);
        } else {
            return $this->localmyprofilerenderer->render_category($category);
        }
    }

    /**
     * Render a node.
     *
     * @param node $node
     *
     * @return string
     */
    public function render_node(node $node) {
        if (is_null($this->localmyprofilerenderer)) {
            return parent::render_node($node);
        } else {
            return $this->localmyprofilerenderer->render_node($node);
        }
    }
}
