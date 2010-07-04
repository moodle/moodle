<?php

//TODO: this is very wrong way to do admin settings - this has to be rewritten!!

/**
 * TinyMCE editor settings moodle form class.
 *
 * @package    editor_tinymce
 * @copyright  2010 Dongsheng Cai
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editor_settings_tinymce extends editor_settings_form {
    public function definition() {
        $mform = $this->_form;
        $options = array(
            'PSpell'=>'PSpell',
            'GoogleSpell'=>'Google Spell',
            'PSpellShell'=>'PSpellShell');
        // options must be started with editor_ to get stored
        $mform->addElement('select', 'editor_tinymce_spellengine',  get_string('spellengine', 'admin'), $options);
        $mform->addElement('hidden', 'editor', 'tinymce');

        parent::definition();
    }

    static public function option_names() {
        return array('editor_tinymce_spellengine');
    }
}
