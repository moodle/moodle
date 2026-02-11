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
namespace mod_hvp;

use Exception;
use moodle_url;

defined('MOODLE_INTERNAL') || die();

/**
 * Service for communicating with the content hub
 *
 * @package    mod_hvp
 * @copyright  2020 Joubel AS
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class content_hub_service {

    /**
     * Get settings for content hub registration UI
     *
     * @return array
     * @throws Exception
     */
    public static function get_registration_ui_settings() {
        $core               = framework::instance();
        $registrationurl    = new moodle_url('/mod/hvp/ajax.php', [
            'action' => 'contenthubregistration',
        ]);
        $accountsettingsurl = new moodle_url('/admin/settings.php?section=modsettinghvp');

        return [
            'registrationURL'             => $registrationurl->out(true),
            'accountSettingsUrl'          => $accountsettingsurl->out(true),
            'token'                       => $core::createToken('contentHubRegistration'),
            'l10n'                        => $core->getLocalization(),
            'licenseAgreementTitle'       => get_string('contenthub:licenseagreementtitle', 'hvp'),
            'licenseAgreementDescription' => get_string('contenthub:licenseagreementdescription', 'hvp'),
            'licenseAgreementMainText'    => get_string('contenthub:licenseagreementmaintext', 'hvp'),
            'accountInfo'                 => $core->hubAccountInfo(),
        ];
    }
}
