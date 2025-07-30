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

namespace core\navigation;

use core\output\action_link;
use core\output\pix_icon;
use core\url;

/**
 * Simple class used to output a navigation branch in XML
 *
 * @package   core
 * @category  navigation
 * @copyright 2009 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_json {
    /** @var array An array of different node types */
    protected $nodetype = ['node', 'branch'];
    /** @var array An array of node keys and types */
    protected $expandable = [];
    /**
     * Turns a branch and all of its children into XML
     *
     * @param navigation_node $branch
     * @return string XML string
     */
    public function convert($branch) {
        $xml = $this->convert_child($branch);
        return $xml;
    }
    /**
     * Set the expandable items in the array so that we have enough information
     * to attach AJAX events
     * @param array $expandable
     */
    public function set_expandable($expandable) {
        foreach ($expandable as $node) {
            $this->expandable[$node['key'] . ':' . $node['type']] = $node;
        }
    }
    /**
     * Recusively converts a child node and its children to XML for output
     *
     * @param navigation_node $child The child to convert
     * @param int $depth Pointlessly used to track the depth of the XML structure
     * @return string JSON
     */
    protected function convert_child($child, $depth = 1) {
        if (!$child->display) {
            return '';
        }
        $attributes = [];
        $attributes['id'] = $child->id;
        $attributes['name'] = (string)$child->text; // This can be lang_string object so typecast it.
        $attributes['type'] = $child->type;
        $attributes['key'] = $child->key;
        $attributes['class'] = $child->get_css_type();
        $attributes['requiresajaxloading'] = $child->requiresajaxloading;

        if ($child->icon instanceof pix_icon) {
            $attributes['icon'] = [
                'component' => $child->icon->component,
                'pix' => $child->icon->pix,
            ];
            foreach ($child->icon->attributes as $key => $value) {
                if ($key == 'class') {
                    $attributes['icon']['classes'] = explode(' ', $value);
                } else if (!array_key_exists($key, $attributes['icon'])) {
                    $attributes['icon'][$key] = $value;
                }
            }
        } else if (!empty($child->icon)) {
            $attributes['icon'] = (string)$child->icon;
        }

        if ($child->forcetitle || $child->title !== $child->text) {
            $attributes['title'] = htmlentities($child->title ?? '', ENT_QUOTES, 'UTF-8');
        }
        if (array_key_exists($child->key . ':' . $child->type, $this->expandable)) {
            $attributes['expandable'] = $child->key;
            $child->add_class($this->expandable[$child->key . ':' . $child->type]['id']);
        }

        if (count($child->classes) > 0) {
            $attributes['class'] .= ' ' . join(' ', $child->classes);
        }
        if (is_string($child->action)) {
            $attributes['link'] = $child->action;
        } else if ($child->action instanceof url) {
            $attributes['link'] = $child->action->out();
        } else if ($child->action instanceof action_link) {
            $attributes['link'] = $child->action->url->out();
        }
        $attributes['hidden'] = ($child->hidden);
        $attributes['haschildren'] = ($child->children->count() > 0 || $child->type == navigation_node::TYPE_CATEGORY);
        $attributes['haschildren'] = $attributes['haschildren'] || $child->type == navigation_node::TYPE_MY_CATEGORY;

        if ($child->children->count() > 0) {
            $attributes['children'] = [];
            foreach ($child->children as $subchild) {
                $attributes['children'][] = $this->convert_child($subchild, $depth + 1);
            }
        }

        if ($depth > 1) {
            return $attributes;
        } else {
            return json_encode($attributes);
        }
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(navigation_json::class, \navigation_json::class);
