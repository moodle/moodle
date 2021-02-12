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
 * @package   block_iomad_commerce
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../lib.php');

class payment_provider {

    // Constructor throws an error if the plugin is disabled.
    public function __construct() {
        if (!$this->enabled()) {
            throw new Exception(get_string('payment_provider_disabled',
                                           'block_iomad_commerce',
                                            get_payment_provider_displayname($this->name())));
        }
    }

    // Name of the plugin.
    public function name() {
        return get_class($this);
    }

    // Gets html to display on the basket page - note this does not include the actually basket.
    public function get_basketpage_html() {
        return '';
    }

    // Check whether the plugin is enabled using the settings.
    public function enabled() {
        return payment_provider_enabled($this->name());
    }

    // Start payment process.
    public function init() {
        global $DB;
        $DB->set_field('invoice', 'checkout_method', $this->name(), array('id' => get_basket_id()));
    }

    // Used to create the html for the page where the user has a last chance to confirm the order.
    public function get_order_review_html() {
    }

    // Called when the user has clicked the confirm button.
    public function confirm() {
    }

    // Used to create the html for the confirmation page.
    public function get_confirmation_html($invoice) {
    }

}
