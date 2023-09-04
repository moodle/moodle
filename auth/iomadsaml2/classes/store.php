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
 * A pure Moodle DB based store for SimpleSAMLPHP
 *
 * @package    auth_iomadsaml2
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

/**
 * A moodle DB datastore
 *
 * This is essentially a clone of /.extlib/simplesamlphp/lib/SimpleSAML/Store/SQL.php
 * but with the SQL rewritten to use the moodle api $DB->blah() instead of PDO;
 *
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class store extends \SimpleSAML\Store {
    /**
     * Retrieve a value from the datastore.
     *
     * @param string $type  The datatype.
     * @param string $key  The key.
     * @return mixed|NULL  The value.
     */
    public function get($type, $key) {
        global $DB;

        assert(is_string($type));
        assert(is_string($key));

        if (strlen($key) > 50) {
            $key = sha1($key);
        }

        $query = '
            SELECT id, value
              FROM {auth_iomadsaml2_kvstore}
             WHERE type = :type
               AND k = :k
               AND (expire IS NULL
                   OR expire > :now
                   )';
        $params = array(
            'type' => $type,
            'k' => $key,
            'now' => time(),
        );

        $rows = $DB->get_records_sql($query, $params);
        if (empty($rows)) {
            return null;
        }
        $row = reset($rows);
        $value = $row->value;
        $value = urldecode($value);
        $value = unserialize($value);

        if ($value === false) {
            return null;
        }
        return $value;
    }

    /**
     * Save a value to the datastore.
     *
     * @param string   $type   The datatype.
     * @param string   $key    The key.
     * @param mixed    $value  The value.
     * @param int|null $expire The expiration time (unix timestamp), or NULL if it never expires.
     */
    public function set($type, $key, $value, $expire = null) {
        global $DB;

        assert(is_string($type));
        assert(is_string($key));
        assert(is_null($expire) || (is_int($expire) && $expire > 2592000));

        if (rand(0, 1000) < 10) {
            $this->delete_expired(); // TODO convert to task.
        }

        if (strlen($key) > 50) {
            $key = sha1($key);
        }

        $value = serialize($value);
        $value = rawurlencode($value);

        $data = array(
            'type' => $type,
            'k' => $key,
            'value' => $value,
            'expire' => $expire,
        );

        $find = array(
            'type' => $type,
            'k' => $key,
        );

        $record = $DB->get_record('auth_iomadsaml2_kvstore', $find);
        if ($record) {
            $data['id'] = $record->id;
            $DB->update_record('auth_iomadsaml2_kvstore', $data);
        } else {
            $DB->insert_record('auth_iomadsaml2_kvstore', $data);
        }
    }

    /**
     * Delete a value from the datastore.
     *
     * @param string $type The datatype.
     * @param string $key  The key.
     */
    public function delete($type, $key) {
        global $DB;

        assert(is_string($type));
        assert(is_string($key));

        if (strlen($key) > 50) {
            $key = sha1($key);
        }

        $data = array(
            'type' => $type,
            'k' => $key,
        );

        $DB->delete_records('auth_iomadsaml2_kvstore', $data);
    }

    /**
     * Clean the key-value table of expired entries.
     */
    public function delete_expired() {
        global $DB;
        $sql = 'DELETE FROM {auth_iomadsaml2_kvstore}
                 WHERE expire < :now';
        $params = array('now' => time());

        $DB->execute($sql, $params);
    }

}

