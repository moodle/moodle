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
 * The MongoDB plugin form for adding an instance.
 *
 * The following settings are provided:
 *      - server
 *      - username
 *      - password
 *      - database
 *      - replicaset
 *      - usesafe
 *      - extendedmode
 *
 * @package    cachestore_mongodb
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include the necessary evils.
require_once($CFG->dirroot.'/cache/forms.php');

/**
 * The form to add an instance of the MongoDB store to the system.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_mongodb_addinstance_form extends cachestore_addinstance_form {

    /**
     * The forms custom definitions.
     */
    protected function configuration_definition() {
        global $OUTPUT;
        $form = $this->_form;

        if (!class_exists('MongoClient')) {
            $form->addElement('html', $OUTPUT->notification(get_string('pleaseupgrademongo', 'cachestore_mongodb')));
        }

        $form->addElement('text', 'server', get_string('server', 'cachestore_mongodb'), array('size' => 72));
        $form->addHelpButton('server', 'server', 'cachestore_mongodb');
        $form->addRule('server', get_string('required'), 'required');
        $form->setDefault('server', 'mongodb://127.0.0.1:27017');
        $form->setType('server', PARAM_RAW);

        $form->addElement('text', 'database', get_string('database', 'cachestore_mongodb'));
        $form->addHelpButton('database', 'database', 'cachestore_mongodb');
        $form->addRule('database', get_string('required'), 'required');
        $form->setType('database', PARAM_ALPHANUMEXT);
        $form->setDefault('database', 'mcache');

        $form->addElement('text', 'username', get_string('username', 'cachestore_mongodb'));
        $form->addHelpButton('username', 'username', 'cachestore_mongodb');
        $form->setType('username', PARAM_ALPHANUMEXT);

        $form->addElement('text', 'password', get_string('password', 'cachestore_mongodb'));
        $form->addHelpButton('password', 'password', 'cachestore_mongodb');
        $form->setType('password', PARAM_TEXT);

        $form->addElement('text', 'replicaset', get_string('replicaset', 'cachestore_mongodb'));
        $form->addHelpButton('replicaset', 'replicaset', 'cachestore_mongodb');
        $form->setType('replicaset', PARAM_ALPHANUMEXT);
        $form->setAdvanced('replicaset');

        $form->addElement('checkbox', 'usesafe', get_string('usesafe', 'cachestore_mongodb'));
        $form->addHelpButton('usesafe', 'usesafe', 'cachestore_mongodb');
        $form->setDefault('usesafe', 1);
        $form->setAdvanced('usesafe');
        $form->setType('usesafe', PARAM_BOOL);

        $form->addElement('text', 'usesafevalue', get_string('usesafevalue', 'cachestore_mongodb'));
        $form->addHelpButton('usesafevalue', 'usesafevalue', 'cachestore_mongodb');
        $form->disabledIf('usesafevalue', 'usesafe', 'notchecked');
        $form->setType('usesafevalue', PARAM_INT);
        $form->setAdvanced('usesafevalue');

        $form->addElement('checkbox', 'extendedmode', get_string('extendedmode', 'cachestore_mongodb'));
        $form->addHelpButton('extendedmode', 'extendedmode', 'cachestore_mongodb');
        $form->setDefault('extendedmode', 0);
        $form->setAdvanced('extendedmode');
        $form->setType('extendedmode', PARAM_BOOL);
    }
}