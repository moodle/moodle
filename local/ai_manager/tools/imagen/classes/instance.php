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

namespace aitool_imagen;

use local_ai_manager\base_instance;
use local_ai_manager\local\aitool_option_vertexai;
use stdClass;

/**
 * Instance class for the connector instance of aitool_imagen.
 *
 * @package    aitool_imagen
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class instance extends base_instance {

    #[\Override]
    protected function extend_form_definition(\MoodleQuickForm $mform): void {
        aitool_option_vertexai::extend_form_definition($mform);
        // Condition is always true, but there does not seem to be an easy way to always hide an element.
        $mform->hideIf('apikey', 'connector', 'imagen');
    }

    #[\Override]
    protected function get_extended_formdata(): stdClass {
        return aitool_option_vertexai::add_vertexai_to_form_data($this->get_customfield1());
    }

    #[\Override]
    protected function extend_store_formdata(stdClass $data): void {

        [$serviceaccountjson, $baseendpoint] = aitool_option_vertexai::extract_vertexai_to_store($data);

        $this->set_customfield1($serviceaccountjson);
        $this->set_endpoint($baseendpoint . ':predict');
    }

    #[\Override]
    protected function extend_validation(array $data, array $files): array {
        return aitool_option_vertexai::validate_vertexai($data);
    }
}
