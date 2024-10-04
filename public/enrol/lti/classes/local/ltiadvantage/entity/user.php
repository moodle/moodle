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

namespace enrol_lti\local\ltiadvantage\entity;

/**
 * Class user, instances of which represent a specific lti user in the tool.
 *
 * A user is always associated with a resource, as lti users cannot exist without a tool-published-resource. A user will
 * always come from either:
 * - a resource link launch or
 * - a membership sync
 * Both of which required a published resource.
 *
 * Additionally, a user may be associated with a given resource_link instance, to signify that the user originated from
 * that resource_link. If a user is not associated with a resource_link, such as when creating a user during a member
 * sync, that param is nullable. This can be achieved via the factory method user::create_from_resource_link() or set
 * after instantiation via the user::set_resource_link_id() method.
 *
 * @package    enrol_lti
 * @copyright  2021 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later */
class user {

    /** @var int the id of the published resource to which this user belongs. */
    private $resourceid;

    /** @var int the local id of the deployment instance to which this user belongs. */
    private $deploymentid;

    /** @var string the id of the user in the platform site. */
    private $sourceid;

    /** @var int|null the id of this user instance, or null if not stored yet. */
    private $id;

    /** @var int|null the id of the user in the tool site, or null if the instance hasn't been stored yet. */
    private $localid;

    /** @var string city of the user. */
    private $city;

    /** @var string country of the user. */
    private $country;

    /** @var string institution of the user.*/
    private $institution;

    /** @var string timezone of the user. */
    private $timezone;

    /** @var int maildisplay of the user. */
    private $maildisplay;

    /** @var string language code of the user. */
    private $lang;

    /** @var float the user's last grade value. */
    private $lastgrade;

    /** @var int|null the user's last access unix timestamp, or null if they have not accessed the resource yet.*/
    private $lastaccess;

    /** @var int|null the id of the resource_link instance, or null if the user doesn't originate from one. */
    private $resourcelinkid;

    /**
     * Private constructor.
     *
     * @param int $resourceid the id of the published resource to which this user belongs.
     * @param int $userid the id of the Moodle user to which this LTI user relates.
     * @param int $deploymentid the local id of the deployment instance to which this user belongs.
     * @param string $sourceid the id of the user in the platform site.
     * @param string $lang the user's language code.
     * @param string $city the user's city.
     * @param string $country the user's country.
     * @param string $institution the user's institution.
     * @param string $timezone the user's timezone.
     * @param int|null $maildisplay the user's maildisplay, or null to select defaults.
     * @param float|null $lastgrade the user's last grade value.
     * @param int|null $lastaccess the user's last access time, or null if they haven't accessed the resource.
     * @param int|null $resourcelinkid the id of the resource link to link to the user, or null if not applicable.
     * @param int|null $id the id of this object instance, or null if it's a not-yet-persisted object.
     * @throws \coding_exception
     */
    private function __construct(int $resourceid, int $userid, int $deploymentid, string $sourceid,
            string $lang, string $city, string $country,
            string $institution, string $timezone, ?int $maildisplay, ?float $lastgrade, ?int $lastaccess,
            ?int $resourcelinkid = null, ?int $id = null) {

        global $CFG;
        $this->resourceid = $resourceid;
        $this->localid = $userid;
        $this->deploymentid = $deploymentid;
        if (empty($sourceid)) {
            throw new \coding_exception('Invalid sourceid value. Cannot be an empty string.');
        }
        $this->sourceid = $sourceid;
        $this->set_lang($lang);
        $this->set_city($city);
        $this->set_country($country);
        $this->set_institution($institution);
        $this->set_timezone($timezone);
        $maildisplay = $maildisplay ?? ($CFG->defaultpreference_maildisplay ?? 2);
        $this->set_maildisplay($maildisplay);
        $this->lastgrade = $lastgrade ?? 0.0;
        $this->lastaccess = $lastaccess;
        $this->resourcelinkid = $resourcelinkid;
        $this->id = $id;
    }

    /**
     * Factory method for creating a user instance associated with a given resource_link instance.
     *
     * @param int $resourcelinkid the local id of the resource link instance to link to the user.
     * @param int $resourceid the id of the published resource to which this user belongs.
     * @param int $userid the id of the Moodle user to which this LTI user relates.
     * @param int $deploymentid the local id of the deployment instance to which this user belongs.
     * @param string $sourceid the id of the user in the platform site.
     * @param string $lang the user's language code.
     * @param string $timezone the user's timezone.
     * @param string $city the user's city.
     * @param string $country the user's country.
     * @param string $institution the user's institution.
     * @param int|null $maildisplay the user's maildisplay, or null to select defaults.
     * @return user the user instance.
     */
    public static function create_from_resource_link(int $resourcelinkid, int $resourceid, int $userid,
            int $deploymentid, string $sourceid, string $lang, string $timezone,
            string $city = '', string $country = '', string $institution = '',
            ?int $maildisplay = null): user {

        return new self($resourceid, $userid, $deploymentid, $sourceid, $lang, $city,
            $country, $institution, $timezone, $maildisplay, null, null, $resourcelinkid);
    }

    /**
     * Factory method for creating a user.
     *
     * @param int $resourceid the id of the published resource to which this user belongs.
     * @param int $userid the id of the Moodle user to which this LTI user relates.
     * @param int $deploymentid the local id of the deployment instance to which this user belongs.
     * @param string $sourceid the id of the user in the platform site.
     * @param string $lang the user's language code.
     * @param string $timezone the user's timezone.
     * @param string $city the user's city.
     * @param string $country the user's country.
     * @param string $institution the user's institution.
     * @param int|null $maildisplay the user's maildisplay, or null to select defaults.
     * @param float|null $lastgrade the user's last grade value.
     * @param int|null $lastaccess the user's last access time, or null if they haven't accessed the resource.
     * @param int|null $resourcelinkid the local id of the resource link instance associated with the user.
     * @param int|null $id the id of this lti user instance, or null if it's a not-yet-persisted object.
     * @return user the user instance.
     */
    public static function create(int $resourceid, int $userid, int $deploymentid, string $sourceid,
            string $lang, string $timezone, string $city = '',
            string $country = '', string $institution = '', ?int $maildisplay = null, ?float $lastgrade = null,
            ?int $lastaccess = null, ?int $resourcelinkid = null, ?int $id = null): user {

        return new self($resourceid, $userid, $deploymentid, $sourceid, $lang, $city,
            $country, $institution, $timezone, $maildisplay, $lastgrade, $lastaccess, $resourcelinkid, $id);
    }

    /**
     * Get the id of this user instance.
     *
     * @return int|null the object id, or null if not yet persisted.
     */
    public function get_id(): ?int {
        return $this->id;
    }

    /**
     * Get the id of the resource_link instance to which this user is associated.
     *
     * @return int|null the object id, or null if the user isn't associated with one.
     */
    public function get_resourcelinkid(): ?int {
        return $this->resourcelinkid;
    }

    /**
     * Associate this user with the given resource_link instance, denoting that this user launched from the instance.
     *
     * @param int $resourcelinkid the id of the resource_link instance.
     */
    public function set_resourcelinkid(int $resourcelinkid): void {
        if ($resourcelinkid <= 0) {
            throw new \coding_exception("Invalid resourcelinkid '$resourcelinkid' provided. Must be > 0.");
        }
        $this->resourcelinkid = $resourcelinkid;
    }

    /**
     * Get the id of the published resource to which this user is associated.
     *
     * @return int the resource id.
     */
    public function get_resourceid(): int {
        return $this->resourceid;
    }

    /**
     * Get the id of the deployment instance to which this user belongs.
     *
     * @return int id of the deployment instance.
     */
    public function get_deploymentid(): int {
        return $this->deploymentid;
    }

    /**
     * Get the id of the user in the platform.
     *
     * @return string the source id.
     */
    public function get_sourceid(): string {
        return $this->sourceid;
    }

    /**
     * Get the id of the user in the tool.
     *
     * @return int|null the id, or null if the object instance hasn't yet been persisted.
     */
    public function get_localid(): ?int {
        return $this->localid;
    }

    /**
     * Get the user's city.
     *
     * @return string the city.
     */
    public function get_city(): string {
        return $this->city;
    }

    /**
     * Set the user's city.
     *
     * @param string $city the city string.
     */
    public function set_city(string $city): void {
        $this->city = $city;
    }

    /**
     * Get the user's country code.
     *
     * @return string the country code.
     */
    public function get_country(): string {
        return $this->country;
    }

    /**
     * Set the user's country.
     *
     * @param string $countrycode the 2 digit country code representing the country, or '' to denote none.
     */
    public function set_country(string $countrycode): void {
        global $CFG;
        require_once($CFG->libdir . '/moodlelib.php');
        $validcountrycodes = array_merge([''], array_keys(get_string_manager()->get_list_of_countries(true)));
        if (!in_array($countrycode, $validcountrycodes)) {
            throw new \coding_exception("Invalid country code '$countrycode'.");
        }
        $this->country = $countrycode;
    }

    /**
     * Get the instituation of the user.
     *
     * @return string the institution.
     */
    public function get_institution(): string {
        return $this->institution;
    }

    /**
     * Set the user's institution.
     *
     * @param string $institution the name of the institution.
     */
    public function set_institution(string $institution): void {
        $this->institution = $institution;
    }

    /**
     * Get the timezone of the user.
     *
     * @return string the user timezone.
     */
    public function get_timezone(): string {
        return $this->timezone;
    }

    /**
     * Set the user's timezone, or set '99' to specify server timezone.
     *
     * @param string $timezone the timezone string, or '99' to use server timezone.
     */
    public function set_timezone(string $timezone): void {
        if (empty($timezone)) {
            throw new \coding_exception('Invalid timezone value. Cannot be an empty string.');
        }
        $validtimezones = array_keys(\core_date::get_list_of_timezones(null, true));
        if (!in_array($timezone, $validtimezones)) {
            throw new \coding_exception("Invalid timezone '$timezone' provided.");
        }
        $this->timezone = $timezone;
    }

    /**
     * Get the maildisplay of the user.
     *
     * @return int the maildisplay.
     */
    public function get_maildisplay(): int {
        return $this->maildisplay;
    }

    /**
     * Set the user's mail display preference from a range of supported options.
     *
     * 0 - hide from non privileged users
     * 1 - allow everyone to see
     * 2 - allow only course participants to see
     *
     * @param int $maildisplay the maildisplay preference to set.
     */
    public function set_maildisplay(int $maildisplay): void {
        if (!in_array($maildisplay, range(0, 2))) {
            throw new \coding_exception("Invalid maildisplay value '$maildisplay'. Must be in the range {0..2}.");
        }
        $this->maildisplay = $maildisplay;
    }

    /**
     * Get the lang code of the user.
     *
     * @return string the user's language code.
     */
    public function get_lang(): string {
        return $this->lang;
    }

    /**
     * Set the user's language.
     *
     * @param string $langcode the language code representing the user's language.
     */
    public function set_lang(string $langcode): void {
        if (empty($langcode)) {
            throw new \coding_exception('Invalid lang value. Cannot be an empty string.');
        }
        $validlangcodes = array_keys(get_string_manager()->get_list_of_translations());
        if (!in_array($langcode, $validlangcodes)) {
            throw new \coding_exception("Invalid lang '$langcode' provided.");
        }
        $this->lang = $langcode;
    }

    /**
     * Get the last grade value for this user.
     *
     * @return float the float grade.
     */
    public function get_lastgrade(): float {
        return $this->lastgrade;
    }

    /**
     * Set the last grade for the user.
     *
     * @param float $lastgrade the last grade the user received.
     */
    public function set_lastgrade(float $lastgrade): void {
        global $CFG;
        require_once($CFG->libdir . '/gradelib.php');
        $this->lastgrade = grade_floatval($lastgrade);
    }

    /**
     * Get the last access timestamp for this user.
     *
     * @return int|null the last access timestampt, or null if the user hasn't accessed the resource.
     */
    public function get_lastaccess(): ?int {
        return $this->lastaccess;
    }

    /**
     * Set the last access time for the user.
     *
     * @param int $time unix timestamp representing the last time the user accessed the published resource.
     * @throws \coding_exception if $time is not a positive int.
     */
    public function set_lastaccess(int $time): void {
        if ($time < 0) {
            throw new \coding_exception('Cannot set negative access time');
        }
        $this->lastaccess = $time;
    }
}
