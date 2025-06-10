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
 * Rich content library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_ally;

use cache;
use moodle_url;
use stdClass;
use tool_ally\componentsupport\component_base;
use tool_ally\componentsupport\interfaces\annotation_map;
use tool_ally\componentsupport\interfaces\html_content;
use tool_ally\exceptions\component_validation_exception;
use tool_ally\logging\logger;
use tool_ally\models\component;
use tool_ally\models\component_content;

use DOMDocument;

/**
 * Rich content library.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_content {

    /**
     * @param string $component
     * @return component_base|bool;
     */
    public static function component_instance($component) {
        $componentclassname = local::get_component_class($component);
        if (!class_exists($componentclassname)) {
            return false;
        }
        return new $componentclassname();
    }

    /**
     * Get supports html content.
     *
     * @param string $component
     * @return bool
     */
    public static function component_supports_html_content($component) {
        $component = self::component_instance($component);
        if (!$component) {
            return false;
        }
        if (!$component->module_installed()) {
            return false;
        }
        return method_exists($component, 'get_course_html_content_items');
    }

    /**
     * List copmonents which support html content replacements.
     * @return string[]
     */
    public static function list_html_content_supported_components() {
        global $CFG;
        $componentsupportpath = $CFG->dirroot . '/admin/tool/ally/classes/componentsupport';
        $dir = new \DirectoryIterator($componentsupportpath);

        $components = [];

        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot()) {
                continue;
            }

            $regex = '/(.*)(?:_component.php)$/';

            $matches = [];

            $iscomponentsupportfile = preg_match($regex, $fileinfo->getBasename(), $matches);

            if (empty($matches[1]) || !$iscomponentsupportfile) {
                continue;
            }

            $component = $matches[1];

            if (self::component_supports_html_content($component)) {
                $type = local::get_component_support_type($component);
                if ($type === component_base::TYPE_MOD) {
                    $fullcomponent = $type . '_' . $component;
                } else {
                    $fullcomponent = $component;
                }
                $components[] = $fullcomponent;
            }

        }

        return $components;
    }

    /**
     * @param $courseid
     * @return array;
     */
    public static function annotation_maps($courseid) {
        $cache = cache::make('tool_ally', 'annotationmaps');
        $maps = [];
        $blocksonly = false;
        if (($result = $cache->get($courseid)) !== false) {
            $maps = $result;
            $blocksonly = true;
        }

        $components = self::list_html_content_supported_components();
        foreach ($components as $component) {
            $instance = self::component_instance($component);
            // We are now working with a component instance located in admin/tool/ally/classes/componentsupport.
            if ($instance instanceof annotation_map) {
                // If we pulled data up from the cache - we only need to load data for blocks.
                // This is because changes to blocks don't trigger any events that can be
                // used to purge the cache.
                if ($blocksonly && $instance->component_type() != component_base::TYPE_BLOCK && $component != "mod_forum") {
                    continue;
                }
                try {
                    $maps = array_merge($maps, [$component => $instance->get_annotation_maps($courseid)]);
                } catch (\moodle_exception $ex) {
                    // Component not identified correctly.
                    $msg = 'Component: '.$component.', Course ID: '.$courseid;
                    logger::get()->info('logger:annotationmoderror', [
                        'content' => $msg,
                        '_explanation' => 'logger:annotationmoderror_exp',
                        '_exception' => $ex
                    ]);
                }
            }
        }
        $cache->set($courseid, $maps);
        return $maps;
    }

    /**
     * Get course html content details for specific component and course.
     * @param string $component
     * @param int $courseid
     * @return component[]
     */
    public static function get_course_html_content_items($component, $courseid) {
        $component = self::component_instance($component);
        return $component->get_course_html_content_items($courseid);
    }

    /**
     * Get html content by an entityid.
     * @param string $entityid
     * @return bool|component_content
     * @throws \coding_exception
     */
    public static function get_html_content_by_entity_id($entityid) {
        $parts = explode(':', $entityid);
        if (count($parts) < 4) {
            throw new \coding_exception('Entitiy id does not have enough parts - '.$entityid);
        }

        $args = [$parts[3], $parts[0], $parts[1], $parts[2]];
        if (isset($parts[4])) {
            $args[] = $parts[4];
        }
        return call_user_func_array(['self', 'get_html_content'], $args);
    }

    /**
     * Builds a DOMDocument from html string.
     * @param string $html
     * @return bool|DOMDocument
     */
    public static function build_dom_doc($html) {
        if (empty($html)) {
            return false;
        }
        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // Required for HTML5.
        if (!$doc->loadHTML('<?xml encoding="utf-8" ?>' . $html)) {
            return false;
        };
        libxml_clear_errors(); // Required for HTML5.
        return $doc;
    }

    /**
     * @param component_content|null $content
     * @return component_content|null
     */
    protected static function apply_embedded_file_map(?component_content $content) {
        if (is_null($content)) {
            return $content;
        }

        $component = local::get_component_instance($content->component);
        if (method_exists($component, 'apply_embedded_file_map')) {
            $content = $component->apply_embedded_file_map($content);
        }

        return $content;
    }

    /**
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     * @param int $courseid
     * @param bool $includeembeddedfiles
     * @return component_content|null
     * @throws component_validation_exception
     */
    public static function get_html_content($id, $component, $table, $field,
                                            $courseid = null, $includeembeddedfiles = false) : ?component_content {
        /** @var html_content $componentinstance */
        $componentinstance = self::component_instance($component);
        if (empty($component) || $componentinstance === false) {
            throw new component_validation_exception('Component '.$component.' does not exist');
        }
        /** @var component_content $content */
        $content = $componentinstance->get_html_content($id, $table, $field, $courseid);
        if ($includeembeddedfiles && !empty($content)) {
            $content = self::apply_embedded_file_map($content);
        }
        return $content;
    }

    /**
     * Create url param identifier for component, table field and id
     * @param string $component
     * @param string $table
     * @param string $field
     * @param string $id
     * @return string
     */
    public static function urlident($component, $table, $field, $id) {
        return 'component='.$component.'&table='.$table.'&field='.$field.'&id='.$id;
    }

    /**
     * Return a content model for a deleted content item.
     * @param int $id
     * @param string $table
     * @param string $field
     * @param null|int $courseid
     * @param null|int $timemodified
     * @return component_content|bool
     */
    public static function get_html_content_deleted($id, $component, $table, $field,
                                                    $courseid = null, $timemodified = null) {
        $component = self::component_instance($component);
        if (empty($component)) {
            return false;
        }
        return $component->get_html_content_deleted($id, $table, $field, $courseid, $timemodified);
    }

    /**
     * @param int $id
     * @param string $component
     * @return bool|component_content[]
     */
    public static function get_all_html_content($id, $component, $includeembeddedfiles = false) {
        $component = self::component_instance($component);
        if (empty($component)) {
            return false;
        }
        if (!$component instanceof html_content) {
            return false;
        }
        $contents = $component->get_all_html_content($id);
        if ($includeembeddedfiles) {
            foreach ($contents as &$content) {
                $content = self::apply_embedded_file_map($content);
            }
        }
        return $contents;
    }

    /**
     * @param int $id
     * @param string $component
     * @param string $table
     * @param string $field
     * @param string $content
     * @return bool|string
     */
    public static function replace_html_content($id, $component, $table, $field, $content) {
        $component = self::component_instance($component);
        return $component->replace_html_content($id, $table, $field, $content);
    }

    /**
     * @param \context $context
     * @return string
     */
    public static function get_annotation($context) {
        if ($context->contextlevel === CONTEXT_MODULE) {
            try {
                list($course, $cm) = get_course_and_cm_from_cmid($context->instanceid);
                unset($course);
                $component = self::component_instance($cm->modname);
                if ($component && method_exists($component, 'get_annotation')) {
                    return $component->get_annotation($cm->instance);
                }
            } catch (\moodle_exception $ex) {
                // Component not identified correctly.
                $msg = 'Context: '.$context->path.', Instance ID: '.$context->instanceid;
                logger::get()->info('logger:annotationmoderror', [
                    'content' => $msg,
                    '_explanation' => 'logger:annotationmoderror_exp',
                    '_exception' => $ex
                ]);
                return '';
            }
        }
        return '';
    }

    /**
     * A message to send to Ally about a content being updated, created, etc.
     *
     * Warning: be very careful about editing this message.  It's used
     * for webservices and for pushed updates.
     *
     * @param component_content $componentcontent
     * @return array
     */
    public static function to_crud($componentcontent, $eventname) {
        return [
            'entity_id'    => $componentcontent->entity_id(),
            'context_id'   => (string) $componentcontent->get_courseid(),
            'event_name'   => $eventname,
            'event_time'   => local::iso_8601($componentcontent->timemodified),
            'content_hash' => $componentcontent->contenthash
        ];
    }

    /**
     * @param int $courseid
     * @param int $id
     * @param string $component
     * @throws \dml_exception
     * @return bool|int
     */
    public static function queue_delete($courseid, $id, $component, $table, $field) {
        global $DB;

        return $DB->insert_record_raw('tool_ally_deleted_content', [
            'comprowid'   => $id,
            'courseid'    => $courseid,
            'component'   => $component,
            'comptable'   => $table,
            'compfield'   => $field,
            'timedeleted' => time(),
        ], false);
    }

    /**
     * Return an array of descriptors for links to local files in the provided html.
     * Decoding those into file resources is left to the caller.
     *
     * @param string $html
     * @return array
     */
    public static function get_pluginfiles_in_html(string $html): ?array {
        $cache = cache::make('tool_ally', 'pluginfilesinhtml');

        // Use a hash of the html to fingerprint the content for caching.
        $sha = sha1($html);
        $results = $cache->get($sha);
        if ($results !== false) {
            return $results;
        }

        // Use a DOM object of the provided HTML.
        $doc = static::build_dom_doc($html);
        if (!$doc) {
            return null;
        }
        $results = [];

        // Check any a tags.
        $anchorresults = $doc->getElementsByTagName('a');
        foreach ($anchorresults as $anchorresult) {
            if (!is_object($anchorresult->attributes) || !is_object($anchorresult->attributes->getNamedItem('href'))) {
                continue;
            }

            $file = new stdClass();
            $file->src = $anchorresult->attributes->getNamedItem('href')->nodeValue;
            $file->tagname = $anchorresult->tagName;
            $results[] = $file;
        }

        // Check any img tags.
        $imgresults = $doc->getElementsByTagName('img');
        foreach ($imgresults as $imgresult) {
            if (!is_object($imgresult->attributes) || !is_object($imgresult->attributes->getNamedItem('src'))) {
                continue;
            }

            $file = new stdClass();
            $file->src = $imgresult->attributes->getNamedItem('src')->nodeValue;
            $file->tagname = $imgresult->tagName;
            $results[] = $file;
        }

        // Now filter out external links.
        $baseurl = new moodle_url('/pluginfile.php');
        $baseurl = $baseurl->out(false);
        $drafturl = new moodle_url('/draftfile.php');
        $drafturl = $drafturl->out(false);
        foreach ($results as $key => $result) {
            if (strpos($result->src, $baseurl) !== false || strpos($result->src, $drafturl) !== false) {
                // In this case it is a full pluginfile path, or a full draftfile path.
                $result->type = 'fullurl';
                continue;
            }
            if (strpos($result->src, '@@PLUGINFILE@@') !== false) {
                // This is a url with just the pluginfile tag, so we only have the path/name of the file.
                $result->type = 'pathonly';
                $filename = str_replace('@@PLUGINFILE@@', '', $result->src);
                $filename = urldecode($filename);
                $result->src = ltrim($filename, '/');
                continue;
            }
            // This means we don't have a recognized local URL, so we can remove it.
            unset($results[$key]);
        }

        // Save the results for later.
        $cache->set($sha, $results);

        return $results;
    }
}
