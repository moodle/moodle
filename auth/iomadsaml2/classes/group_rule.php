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
 * Group access rule.
 *
 * @package    auth_iomadsaml2
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace auth_iomadsaml2;

/**
 * Class group_rule.
 */
class group_rule {

    /**
     * Allow status.
     */
    const STATUS_ALLOW = 'allow';

    /**
     *  Deny status.
     */
    const STATUS_DENY = 'deny';

    /**
     * Status of the rule.
     * @var string
     */
    protected $status;

    /**
     * Attribute name.
     * @var string
     */
    protected $attribute;

    /**
     * Group name.
     * @var string
     */
    protected $group;

    /**
     * A factory method to build a list of all correctly configured rules from the config settings.
     *
     * @param string $config Rules config string.
     * @return \auth_iomadsaml2\group_rule[]
     */
    public static function get_list(string  $config) {
        $rules = [];

        $items = explode("\n", str_replace("\r\n", "\n", $config));

        foreach ($items as $item) {
            $data = explode(' ', $item);
            if (!empty($data[0]) && !empty($data[1])) {
                $status = trim($data[0]);

                if (!in_array($status, [self::STATUS_ALLOW, self::STATUS_DENY])) {
                    continue;
                }

                $group = explode('=', $data[1]);
                if (!empty($group[0]) && !empty($group[1])) {
                    $attribute = trim($group[0]);
                    $group = trim($group[1]);
                    $rules[] = new self($attribute, $group, $status);
                }
            }
        }

        return $rules;
    }

    /**
     * Protected constructor. Rules should be built through self::get_list method.
     *
     * @param string $attribute Group attribute name.
     * @param string $group Group value.
     * @param string $status Status for the group (allow or deny)/
     */
    protected function __construct(string $attribute, string $group, string $status) {
        $this->attribute = $attribute;
        $this->group = $group;
        $this->status = $status;
    }

    /**
     * Return group attribute.
     *
     * @return string
     */
    public function get_attribute() {
        return $this->attribute;
    }

    /**
     * Return group name.
     *
     * @return string
     */
    public function get_group() {
        return $this->group;
    }

    /**
     * Does this rule allow or deny access.
     *
     * @return bool
     */
    public function is_allowed() {
        return $this->status == self::STATUS_ALLOW;
    }
}
