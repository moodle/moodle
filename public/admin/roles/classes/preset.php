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
 * New role XML processing.
 *
 * @package    core_role
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * XML role file manipulation class.
 *
 * @package    core_role
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_role_preset {

    /**
     * Send role export xml file to browser.
     *
     * @param int $roleid
     * @return void does not return, send the file to output
     */
    public static function send_export_xml($roleid) {
        global $CFG, $DB;
        require_once($CFG->libdir . '/filelib.php');

        $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);

        if ($role->shortname) {
            $filename = $role->shortname.'.xml';
        } else {
            $filename = 'role.xml';
        }
        $xml = self::get_export_xml($roleid);
        send_file($xml, $filename, 0, false, true, true);
        die();
    }

    /**
     * Generate role export xml file.
     *
     * @param $roleid
     * @return string
     */
    public static function get_export_xml($roleid) {
        global $DB;

        $role = $DB->get_record('role', array('id'=>$roleid), '*', MUST_EXIST);

        $dom = new DOMDocument('1.0', 'UTF-8');
        $top = $dom->createElement('role');
        $dom->appendChild($top);

        $top->appendChild($dom->createElement('shortname', $role->shortname));
        $top->appendChild($dom->createElement('name', htmlspecialchars($role->name, ENT_COMPAT | ENT_HTML401, 'UTF-8')));
        $top->appendChild($dom->createElement('description', htmlspecialchars($role->description, ENT_COMPAT | ENT_HTML401,
                'UTF-8')));
        $top->appendChild($dom->createElement('archetype', $role->archetype));

        $contextlevels = $dom->createElement('contextlevels');
        $top->appendChild($contextlevels);
        foreach (get_role_contextlevels($roleid) as $level) {
            $name = context_helper::get_class_for_level($level);
            if (strpos($name, 'core\\context\\') === 0) {
                // Use short names of standard contexts for backwards compatibility.
                $value = preg_replace('/^core\\\\context\\\\/', '', $name);
            } else {
                // Must be a custom plugin level, use numbers to work around
                // potential duplicate short names of contexts in add-ons.
                $value = $level;
            }
            $contextlevels->appendChild($dom->createElement('level', $value));
        }

        foreach (array('assign', 'override', 'switch', 'view') as $type) {
            $allows = $dom->createElement('allow'.$type);
            $top->appendChild($allows);
            $records = $DB->get_records('role_allow_'.$type, array('roleid'=>$roleid), "allow$type ASC");
            foreach ($records as $record) {
                if (!$ar = $DB->get_record('role', array('id'=>$record->{'allow'.$type}))) {
                    continue;
                }
                $allows->appendChild($dom->createElement('shortname', $ar->shortname));
            }
        }

        $permissions = $dom->createElement('permissions');
        $top->appendChild($permissions);

        $capabilities = $DB->get_records_sql_menu(
            "SELECT capability, permission
               FROM {role_capabilities}
              WHERE contextid = :syscontext AND roleid = :roleid
           ORDER BY capability ASC",
            array('syscontext'=>context_system::instance()->id, 'roleid'=>$roleid));

        $allcapabilities = $DB->get_records('capabilities', array(), 'name ASC');
        foreach ($allcapabilities as $cap) {
            if (!isset($capabilities[$cap->name])) {
                $permissions->appendChild($dom->createElement('inherit', $cap->name));
            }
        }

        foreach ($capabilities as $capability => $permission) {
            if ($permission == CAP_ALLOW) {
                $permissions->appendChild($dom->createElement('allow', $capability));
            }
        }
        foreach ($capabilities as $capability => $permission) {
            if ($permission == CAP_PREVENT) {
                $permissions->appendChild($dom->createElement('prevent', $capability));
            }
        }
        foreach ($capabilities as $capability => $permission) {
            if ($permission == CAP_PROHIBIT) {
                $permissions->appendChild($dom->createElement('prohibit', $capability));
            }
        }

        return $dom->saveXML();
    }

    /**
     * Is this XML valid role preset?
     *
     * @param string $xml
     * @return bool
     */
    public static function is_valid_preset($xml) {
        $dom = new DOMDocument();
        if (!$dom->loadXML($xml)) {
            return false;
        } else {
            $val = @$dom->schemaValidate(__DIR__.'/../role_schema.xml');
            if (!$val) {
                return false;
            }
        }
        return true;
    }

    /**
     * Parse role preset xml file.
     *
     * @param string $xml
     * @return array role info, null on error
     */
    public static function parse_preset($xml) {
        global $DB;

        $info = array();

        if (!self::is_valid_preset($xml)) {
            return null;
        }

        $dom = new DOMDocument();
        $dom->loadXML($xml);

        $info['shortname'] = self::get_node_value($dom, '/role/shortname');
        if (isset($info['shortname'])) {
            $info['shortname'] = strtolower(clean_param($info['shortname'], PARAM_ALPHANUMEXT));
        }

        $info['name'] = self::get_node_value($dom, '/role/name');
        if (isset($value)) {
            $info['name'] = clean_param($info['name'], PARAM_TEXT);
        }

        $info['description'] = self::get_node_value($dom, '/role/description');
        if (isset($value)) {
            $info['description'] = clean_param($info['description'], PARAM_CLEANHTML);
        }

        $info['archetype'] = self::get_node_value($dom, '/role/archetype');
        if (isset($value)) {
            $archetypes = get_role_archetypes();
            if (!isset($archetypes[$info['archetype']])) {
                $info['archetype'] = null;
            }
        }

        $values = self::get_node_children_values($dom, '/role/contextlevels', 'level');
        if (isset($values)) {
            $info['contextlevels'] = array();
            $levelmap = array_flip(context_helper::get_all_levels());
            foreach ($values as $value) {
                // Numbers and short names are supported since Moodle 4.2.
                $classname = \core\context_helper::parse_external_level($value);
                if ($classname) {
                    $cl = $classname::LEVEL;
                    $info['contextlevels'][$cl] = $cl;
                }
            }
        }

        foreach (array('assign', 'override', 'switch', 'view') as $type) {
            $values = self::get_node_children_values($dom, '/role/allow'.$type, 'shortname');
            if (!isset($values)) {
                $info['allow'.$type] = null;
                continue;
            }
            $info['allow'.$type] = array();
            foreach ($values as $value) {
                if ($value === $info['shortname']) {
                    array_unshift($info['allow'.$type], -1); // Means self.
                }
                if ($role = $DB->get_record('role', array('shortname'=>$value))) {
                    $info['allow'.$type][] = $role->id;
                    continue;
                }
            }
        }

        $info['permissions'] = array();
        $values = self::get_node_children_values($dom, '/role/permissions', 'inherit');
        if (isset($values)) {
            foreach ($values as $value) {
                if ($value = clean_param($value, PARAM_CAPABILITY)) {
                    $info['permissions'][$value] = CAP_INHERIT;
                }
            }
        }
        $values = self::get_node_children_values($dom, '/role/permissions', 'allow');
        if (isset($values)) {
            foreach ($values as $value) {
                if ($value = clean_param($value, PARAM_CAPABILITY)) {
                    $info['permissions'][$value] = CAP_ALLOW;
                }
            }
        }
        $values = self::get_node_children_values($dom, '/role/permissions', 'prevent');
        if (isset($values)) {
            foreach ($values as $value) {
                if ($value = clean_param($value, PARAM_CAPABILITY)) {
                    $info['permissions'][$value] = CAP_PREVENT;
                }
            }
        }
        $values = self::get_node_children_values($dom, '/role/permissions', 'prohibit');
        if (isset($values)) {
            foreach ($values as $value) {
                if ($value = clean_param($value, PARAM_CAPABILITY)) {
                    $info['permissions'][$value] = CAP_PROHIBIT;
                }
            }
        }

        return $info;
    }

    protected static function get_node(DOMDocument $dom, $path) {
        $parts = explode('/', $path);
        $elname = end($parts);

        $nodes = $dom->getElementsByTagName($elname);

        if ($nodes->length == 0) {
            return null;
        }

        foreach ($nodes as $node) {
            if ($node->getNodePath() === $path) {
                return $node;
            }
        }

        return null;
    }

    protected static function get_node_value(DOMDocument $dom, $path) {
        if (!$node = self::get_node($dom, $path)) {
            return null;
        }
        return $node->nodeValue;
    }

    protected static function get_node_children(DOMDocument $dom, $path, $tagname) {
        if (!$node = self::get_node($dom, $path)) {
            return null;
        }

        $return = array();
        foreach ($node->childNodes as $child) {
            if ($child->nodeName === $tagname) {
                $return[] = $child;
            }
        }
        return $return;
    }

    protected static function get_node_children_values(DOMDocument $dom, $path, $tagname) {
        $children = self::get_node_children($dom, $path, $tagname);

        if ($children === null) {
            return null;
        }
        $return = array();
        foreach ($children as $child) {
            $return[] = $child->nodeValue;
        }
        return $return;
    }
}
