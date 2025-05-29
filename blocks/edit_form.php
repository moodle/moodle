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
 * Defines the base class form used by blocks/edit.php to edit block instance configuration.
 *
 * It works with the {@see block_edit_form} class, or rather the particular
 * subclass defined by this block, to do the editing.
 *
 * @package    core_block
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir . '/blocklib.php');

/**
 * The base class form used by blocks/edit.php to edit block instance configuration.
 *
 * @property-read block_base $block
 * @property-read moodle_page $page
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_edit_form extends \core_form\dynamic_form {
    /**
     * The block instance we are editing.
     * @var block_base
     */
    private $_block;
    /**
     * The page we are editing this block in association with.
     * @var moodle_page
     */
    private $_page;

    /**
     * Defaults set in set_data() that need to be returned in get_data() if form elements were not created
     * @var array
     */
    protected $defaults = [];

    /**
     * Magic getter for backward compatibility
     *
     * @param string $name
     * @return block_base|moodle_page
     */
    public function __get(string $name) {
        if ($name === 'page') {
            return $this->get_page();
        } else if ($name === 'block') {
            return $this->get_block();
        } else {
            throw new coding_exception('Property '.$name.' does not exist');
        }
    }

    /**
     * Page where we are adding or editing the block
     *
     * To access you can also use magic property $this->page
     *
     * @return moodle_page
     * @throws moodle_exception
     */
    protected function get_page(): moodle_page {
        if (!$this->_page && !empty($this->_customdata['page'])) {
            $this->_page = $this->_customdata['page'];
        } else if (!$this->_page) {
            if (!$pagehash = $this->optional_param('pagehash', '', PARAM_ALPHANUMEXT)) {
                throw new \moodle_exception('missingparam', '', '', 'pagehash');
            }
            $this->_page = moodle_page::retrieve_edited_page($pagehash, MUST_EXIST);
            $this->_page->blocks->load_blocks();
        }
        return $this->_page;
    }

    /**
     * Instance of the block that is being added or edited
     *
     * To access you can also use magic property $this->block
     *
     * If {{@see self::display_form_when_adding()}} returns true and the configuration
     * form is displayed when adding block, the $this->block->id will be null.
     *
     * @return block_base
     * @throws block_not_on_page_exception
     * @throws moodle_exception
     */
    protected function get_block(): block_base {
        if (!$this->_block && !empty($this->_customdata['block'])) {
            $this->_block = $this->_customdata['block'];
        } else if (!$this->_block) {
            $blockid = $this->optional_param('blockid', null, PARAM_INT);
            $blockname = $this->optional_param('blockname', null, PARAM_PLUGIN);
            if ($blockname && !$blockid) {
                $this->_block = block_instance($blockname);
                $this->_block->page = $this->page;
                $this->_block->context = $this->page->context;
                $this->_block->instance = (object)['parentcontextid' => $this->page->context->id, 'id' => null];
            } else {
                $this->_block = $this->page->blocks->find_instance($blockid);
            }
        }
        return $this->_block;
    }

    /**
     * Form definition
     */
    function definition() {
        $mform =& $this->_form;

        $mform->addElement('hidden', 'blockid', $this->block->instance->id);
        $mform->setType('blockid', PARAM_INT);
        $mform->addElement('hidden', 'blockname', $this->optional_param('blockname', null, PARAM_PLUGIN));
        $mform->setType('blockname', PARAM_PLUGIN);
        $mform->addElement('hidden', 'blockregion', $this->optional_param('blockregion', null, PARAM_TEXT));
        $mform->setType('blockregion', PARAM_TEXT);
        $mform->addElement('hidden', 'pagehash', $this->optional_param('pagehash', null, PARAM_ALPHANUMEXT));
        $mform->setType('pagehash', PARAM_ALPHANUMEXT);

        // First show fields specific to this type of block.
        $this->specific_definition($mform);

        if (!$this->block->instance->id) {
            return;
        }

        // Then show the fields about where this block appears.
        $mform->addElement('header', 'whereheader', get_string('wherethisblockappears', 'block'));

        // If the current weight of the block is out-of-range, add that option in.
        $blockweight = $this->block->instance->weight;
        $weightoptions = array();
        if ($blockweight < -block_manager::MAX_WEIGHT) {
            $weightoptions[$blockweight] = $blockweight;
        }
        for ($i = -block_manager::MAX_WEIGHT; $i <= block_manager::MAX_WEIGHT; $i++) {
            $weightoptions[$i] = $i;
        }
        if ($blockweight > block_manager::MAX_WEIGHT) {
            $weightoptions[$blockweight] = $blockweight;
        }
        $first = reset($weightoptions);
        $weightoptions[$first] = get_string('bracketfirst', 'block', $first);
        $last = end($weightoptions);
        $weightoptions[$last] = get_string('bracketlast', 'block', $last);

        $regionoptions = $this->page->theme->get_all_block_regions();
        foreach ($this->page->blocks->get_regions() as $region) {
            // Make sure to add all custom regions of this particular page too.
            if (!isset($regionoptions[$region])) {
                $regionoptions[$region] = $region;
            }
        }

        $parentcontext = context::instance_by_id($this->block->instance->parentcontextid);
        $mform->addElement('static', 'bui_homecontext', get_string('createdat', 'block'), $parentcontext->get_context_name());
        $mform->addHelpButton('bui_homecontext', 'createdat', 'block');

        // For pre-calculated (fixed) pagetype lists
        $pagetypelist = array();

        // parse pagetype patterns
        $bits = explode('-', $this->page->pagetype);

        // First of all, check if we are editing blocks @ front-page or no and
        // make some dark magic if so (MDL-30340) because each page context
        // implies one (and only one) harcoded page-type that will be set later
        // when processing the form data at {@see block_manager::process_url_edit()}.

        // Front page, show the page-contexts element and set $pagetypelist to 'any page' (*)
        // as unique option. Processign the form will do any change if needed.
        if ($this->is_editing_the_frontpage()) {
            $contextoptions = array();
            $contextoptions[BUI_CONTEXTS_FRONTPAGE_ONLY] = get_string('showonfrontpageonly', 'block');
            $contextoptions[BUI_CONTEXTS_FRONTPAGE_SUBS] = get_string('showonfrontpageandsubs', 'block');
            $contextoptions[BUI_CONTEXTS_ENTIRE_SITE]    = get_string('showonentiresite', 'block');
            $mform->addElement('select', 'bui_contexts', get_string('contexts', 'block'), $contextoptions);
            $mform->addHelpButton('bui_contexts', 'contexts', 'block');
            $pagetypelist['*'] = '*'; // This is not going to be shown ever, it's an unique option

        // Any other system context block, hide the page-contexts element,
        // it's always system-wide BUI_CONTEXTS_ENTIRE_SITE
        } else if ($parentcontext->contextlevel == CONTEXT_SYSTEM) {

        } else if ($parentcontext->contextlevel == CONTEXT_COURSE) {
            // 0 means display on current context only, not child contexts
            // but if course managers select mod-* as pagetype patterns, block system will overwrite this option
            // to 1 (display on current context and child contexts)
        } else if ($parentcontext->contextlevel == CONTEXT_MODULE or $parentcontext->contextlevel == CONTEXT_USER) {
            // module context doesn't have child contexts, so display in current context only
        } else {
            $parentcontextname = $parentcontext->get_context_name();
            $contextoptions[BUI_CONTEXTS_CURRENT]      = get_string('showoncontextonly', 'block', $parentcontextname);
            $contextoptions[BUI_CONTEXTS_CURRENT_SUBS] = get_string('showoncontextandsubs', 'block', $parentcontextname);
            $mform->addElement('select', 'bui_contexts', get_string('contexts', 'block'), $contextoptions);
        }
        $mform->setType('bui_contexts', PARAM_INT);

        // Generate pagetype patterns by callbacks if necessary (has not been set specifically)
        if (empty($pagetypelist)) {
            $pagetypelist = generate_page_type_patterns($this->page->pagetype, $parentcontext, $this->page->context);
            $displaypagetypewarning = false;
            if (!array_key_exists($this->block->instance->pagetypepattern, $pagetypelist)) {
                // Pushing block's existing page type pattern
                $pagetypestringname = 'page-'.str_replace('*', 'x', $this->block->instance->pagetypepattern);
                if (get_string_manager()->string_exists($pagetypestringname, 'pagetype')) {
                    $pagetypelist[$this->block->instance->pagetypepattern] = get_string($pagetypestringname, 'pagetype');
                } else {
                    //as a last resort we could put the page type pattern in the select box
                    //however this causes mod-data-view to be added if the only option available is mod-data-*
                    // so we are just showing a warning to users about their prev setting being reset
                    $displaypagetypewarning = true;
                }
            }
        }

        // hide page type pattern select box if there is only one choice
        if (count($pagetypelist) > 1) {
            if ($displaypagetypewarning) {
                $mform->addElement('static', 'pagetypewarning', '', get_string('pagetypewarning','block'));
            }

            $mform->addElement('select', 'bui_pagetypepattern', get_string('restrictpagetypes', 'block'), $pagetypelist);
        } else {
            $values = array_keys($pagetypelist);
            $value = array_pop($values);
            // Now we are really hiding a lot (both page-contexts and page-type-patterns),
            // specially in some systemcontext pages having only one option (my/user...)
            // so, until it's decided if we are going to add the 'bring-back' pattern to
            // all those pages or no (see MDL-30574), we are going to show the unique
            // element statically
            // TODO: Revisit this once MDL-30574 has been decided and implemented, although
            // perhaps it's not bad to always show this statically when only one pattern is
            // available.
            if (!$this->is_editing_the_frontpage()) {
                // Try to beautify it
                $strvalue = $value;
                $strkey = 'page-'.str_replace('*', 'x', $strvalue);
                if (get_string_manager()->string_exists($strkey, 'pagetype')) {
                    $strvalue = get_string($strkey, 'pagetype');
                }
                // Show as static (hidden has been set already)
                $mform->addElement('static', 'bui_staticpagetypepattern',
                    get_string('restrictpagetypes','block'), $strvalue);
            }
        }

        if ($this->page->subpage) {
            if ($parentcontext->contextlevel != CONTEXT_USER) {
                $subpageoptions = array(
                    '%@NULL@%' => get_string('anypagematchingtheabove', 'block'),
                    $this->page->subpage => get_string('thisspecificpage', 'block', $this->page->subpage),
                );
                $mform->addElement('select', 'bui_subpagepattern', get_string('subpages', 'block'), $subpageoptions);
            }
        }

        $defaultregionoptions = $regionoptions;
        $defaultregion = $this->block->instance->defaultregion;
        if (!array_key_exists($defaultregion, $defaultregionoptions)) {
            $defaultregionoptions[$defaultregion] = $defaultregion;
        }
        $mform->addElement('select', 'bui_defaultregion', get_string('defaultregion', 'block'), $defaultregionoptions);
        $mform->addHelpButton('bui_defaultregion', 'defaultregion', 'block');

        $mform->addElement('select', 'bui_defaultweight', get_string('defaultweight', 'block'), $weightoptions);
        $mform->addHelpButton('bui_defaultweight', 'defaultweight', 'block');

        // Where this block is positioned on this page.
        $mform->addElement('header', 'onthispage', get_string('onthispage', 'block'));

        $mform->addElement('selectyesno', 'bui_visible', get_string('visible', 'block'));

        $blockregion = $this->block->instance->region;
        if (!array_key_exists($blockregion, $regionoptions)) {
            $regionoptions[$blockregion] = $blockregion;
        }
        $mform->addElement('select', 'bui_region', get_string('region', 'block'), $regionoptions);

        $mform->addElement('select', 'bui_weight', get_string('weight', 'block'), $weightoptions);

        $pagefields = array('bui_visible', 'bui_region', 'bui_weight');
        if (!$this->block->user_can_edit()) {
            $mform->hardFreezeAllVisibleExcept($pagefields);
        }
        if (!$this->page->user_can_edit_blocks()) {
            $mform->hardFreeze($pagefields);
        }

        if (!empty($this->_customdata['actionbuttons'])) {
            $this->add_action_buttons();
        }
    }

    /**
     * Returns true if the user is editing a frontpage.
     * @return bool
     */
    public function is_editing_the_frontpage() {
        // There are some conditions to check related to contexts.
        $ctxconditions = $this->page->context->contextlevel == CONTEXT_COURSE &&
            $this->page->context->instanceid == get_site()->id;
        $issiteindex = (strpos($this->page->pagetype, 'site-index') === 0);
        // So now we can be 100% sure if edition is happening at frontpage.
        return ($ctxconditions && $issiteindex);
    }

    /**
     * Prepare block configuration data and add default values when needed
     *
     * @param stdClass $defaults
     * @return stdClass
     */
    protected function prepare_defaults(stdClass $defaults): stdClass {
        // Prefix bui_ on all the core field names.
        $blockfields = array('showinsubcontexts', 'pagetypepattern', 'subpagepattern', 'parentcontextid',
                'defaultregion', 'defaultweight', 'visible', 'region', 'weight');
        foreach ($blockfields as $field) {
            $newname = 'bui_' . $field;
            $defaults->$newname = $defaults->$field ?? null;
        }

        // Copy block config into config_ fields.
        if (!empty($this->block->config)) {
            foreach ($this->block->config as $field => $value) {
                $configfield = 'config_' . $field;
                $defaults->$configfield = $value;
            }
        }

        // Munge ->subpagepattern becuase HTML selects don't play nicely with NULLs.
        if (empty($defaults->bui_subpagepattern)) {
            $defaults->bui_subpagepattern = '%@NULL@%';
        }

        $systemcontext = context_system::instance();
        if ($defaults->parentcontextid == $systemcontext->id) {
            $defaults->bui_contexts = BUI_CONTEXTS_ENTIRE_SITE; // System-wide and sticky
        } else {
            $defaults->bui_contexts = $defaults->bui_showinsubcontexts;
        }

        // Some fields may not be editable, remember the values here so we can return them in get_data().
        $this->defaults = [
            'bui_parentcontextid' => $defaults->bui_parentcontextid,
            'bui_contexts' => $defaults->bui_contexts,
            'bui_pagetypepattern' => $defaults->bui_pagetypepattern,
            'bui_subpagepattern' => $defaults->bui_subpagepattern,
        ];
        return $defaults;
    }

    /**
     * Load in existing data as form defaults
     *
     * @param stdClass $defaults
     * @return void
     */
    public function set_data($defaults) {
        parent::set_data($this->prepare_defaults($defaults));
    }

    /**
     * Override this to create any form fields specific to this type of block.
     * @param \MoodleQuickForm $mform the form being built.
     */
    protected function specific_definition($mform) {
        // By default, do nothing.
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return stdClass submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        if ($data = parent::get_data()) {
            // Blocklib expects 'bui_editingatfrontpage' property to be returned from this form.
            $data->bui_editingatfrontpage = $this->is_editing_the_frontpage();
            // Some fields are non-editable and we need to populate them with the values from set_data().
            return (object)((array)$data + $this->defaults);
        }
        return $data;
    }

    /**
     * Returns context where this form is used
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        return $this->page->context;
    }

    /**
     * Checks if current user has access to this form, otherwise throws exception
     */
    protected function check_access_for_dynamic_submission(): void {
        if ($this->block->instance->id) {
            if (!$this->page->user_can_edit_blocks() && !$this->block->user_can_edit()) {
                throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('editblock'));
            }
        } else {
            if (!$this->page->user_can_edit_blocks()) {
                throw new moodle_exception('nopermissions', '', $this->page->url->out(), get_string('addblock'));
            }
            $addableblocks = $this->page->blocks->get_addable_blocks();
            $blocktype = $this->block->name();
            if (!array_key_exists($blocktype, $addableblocks)) {
                throw new moodle_exception('cannotaddthisblocktype', '', $this->page->url->out(), $blocktype);
            }
        }
    }

    /**
     * Process the form submission, used if form was submitted via AJAX
     */
    public function process_dynamic_submission() {
        if ($this->block->instance->id) {
            $this->page->blocks->save_block_data($this->block, $this->get_data());
        } else {
            $blockregion = $this->optional_param('blockregion', null, PARAM_TEXT);
            $newblock = $this->page->blocks->add_block_at_end_of_default_region($this->block->name(),
                empty($blockregion) ? null : $blockregion);

            if (empty($newblock)) {
                return;
            }

            $this->page->blocks->load_blocks();
            $newblock = $this->page->blocks->find_instance($newblock->instance->id);
            $newdata = $this->prepare_defaults($newblock->instance);
            foreach ($this->get_data() as $key => $value) {
                $newdata->$key = $value;
            }
            $this->page->blocks->save_block_data($newblock, $newdata);
        }
    }

    /**
     * Load in existing data as form defaults
     */
    public function set_data_for_dynamic_submission(): void {
        $this->set_data($this->block->instance);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        return $this->page->url;
    }

    /**
     * Display the configuration form when block is being added to the page
     *
     * By default when block is added to the page it is added with the default configuration.
     * Some block may require configuration, for example, "glossary random entry" block
     * needs a glossary to be selected, "RSS feed" block needs an RSS feed to be selected, etc.
     *
     * Such blocks can override this function and return true. These blocks must
     * ensure that the function specific_definition() will work if there is no current block id.
     *
     * @return bool
     */
    public static function display_form_when_adding(): bool {
        return false;
    }
}
