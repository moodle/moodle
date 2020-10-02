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
 * The Cache renderer.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * The cache renderer (mainly admin interfaces).
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_cache_renderer extends plugin_renderer_base {

    /**
     * Displays store summaries.
     *
     * @param array $storeinstancesummaries information about each store instance,
     *      as returned by core_cache\administration_helper::get_store_instance_summaries().
     * @param array $storepluginsummaries information about each store plugin as
     *      returned by core_cache\administration_helper::get_store_plugin_summaries().
     * @return string HTML
     */
    public function store_instance_summariers(array $storeinstancesummaries, array $storepluginsummaries) {
        $table = new html_table();
        $table->head = array(
            get_string('storename', 'cache'),
            get_string('plugin', 'cache'),
            get_string('storeready', 'cache'),
            get_string('mappings', 'cache'),
            get_string('modes', 'cache'),
            get_string('supports', 'cache'),
            get_string('locking', 'cache') . ' ' . $this->output->help_icon('locking', 'cache'),
            get_string('actions', 'cache'),
        );
        $table->colclasses = array(
            'storename',
            'plugin',
            'storeready',
            'mappings',
            'modes',
            'supports',
            'locking',
            'actions'
        );
        $table->data = array();

        $defaultstoreactions = get_string('defaultstoreactions', 'cache');

        foreach ($storeinstancesummaries as $name => $storesummary) {
            $htmlactions = cache_factory::get_administration_display_helper()->get_store_instance_actions($name, $storesummary);
            $modes = array();
            foreach ($storesummary['modes'] as $mode => $enabled) {
                if ($enabled) {
                    $modes[] = get_string('mode_'.$mode, 'cache');
                }
            }

            $supports = array();
            foreach ($storesummary['supports'] as $support => $enabled) {
                if ($enabled) {
                    $supports[] = get_string('supports_'.$support, 'cache');
                }
            }

            $info = '';
            if (!empty($storesummary['default'])) {
                $info = $this->output->pix_icon('i/info', $defaultstoreactions, '', array('class' => 'icon'));
            }

            $isready = $storesummary['isready'] && $storesummary['requirementsmet'];
            $readycell = new html_table_cell;
            if ($isready) {
                $readycell->text = $this->output->pix_icon('i/valid', '1');
            }

            $storename = $storesummary['name'];
            if (!empty($storesummary['default'])) {
                $storename = get_string('store_'.$storesummary['name'], 'cache');
            }
            if (!$isready && (int)$storesummary['mappings'] > 0) {
                $readycell->text = $this->output->help_icon('storerequiresattention', 'cache');
                $readycell->attributes['class'] = 'store-requires-attention';
            }

            $lock = $storesummary['lock']['name'];
            if (!empty($storesummary['lock']['default'])) {
                $lock = get_string($storesummary['lock']['name'], 'cache');
            }

            $row = new html_table_row(array(
                $storename,
                get_string('pluginname', 'cachestore_'.$storesummary['plugin']),
                $readycell,
                $storesummary['mappings'],
                join(', ', $modes),
                join(', ', $supports),
                $lock,
                $info.join(', ', $htmlactions)
            ));
            $row->attributes['class'] = 'store-'.$name;
            if ($storesummary['default']) {
                $row->attributes['class'] .= ' default-store';
            }
            $table->data[] = $row;
        }

        $html  = html_writer::start_tag('div', array('id' => 'core-cache-store-summaries'));
        $html .= $this->output->heading(get_string('storesummaries', 'cache'), 3);
        $html .= html_writer::table($table);
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Displays plugin summaries.
     *
     * @param array $storepluginsummaries information about each store plugin as
     *      returned by core_cache\administration_helper::get_store_plugin_summaries().
     * @return string HTML
     */
    public function store_plugin_summaries(array $storepluginsummaries) {
        $table = new html_table();
        $table->head = array(
            get_string('plugin', 'cache'),
            get_string('storeready', 'cache'),
            get_string('stores', 'cache'),
            get_string('modes', 'cache'),
            get_string('supports', 'cache'),
            get_string('actions', 'cache'),
        );
        $table->colclasses = array(
            'plugin',
            'storeready',
            'stores',
            'modes',
            'supports',
            'actions'
        );
        $table->data = array();

        foreach ($storepluginsummaries as $name => $plugin) {
            $htmlactions = cache_factory::get_administration_display_helper()->get_store_plugin_actions($name, $plugin);

            $modes = array();
            foreach ($plugin['modes'] as $mode => $enabled) {
                if ($enabled) {
                    $modes[] = get_string('mode_'.$mode, 'cache');
                }
            }

            $supports = array();
            foreach ($plugin['supports'] as $support => $enabled) {
                if ($enabled) {
                    $supports[] = get_string('supports_'.$support, 'cache');
                }
            }

            $row = new html_table_row(array(
                $plugin['name'],
                ($plugin['requirementsmet']) ? $this->output->pix_icon('i/valid', '1') : '',
                $plugin['instances'],
                join(', ', $modes),
                join(', ', $supports),
                join(', ', $htmlactions)
            ));

            $row->attributes['class'] = 'plugin-'.$name;
            $table->data[] = $row;
        }

        $html  = html_writer::start_tag('div', array('id' => 'core-cache-plugin-summaries'));
        $html .= $this->output->heading(get_string('pluginsummaries', 'cache'), 3);
        $html .= html_writer::table($table);
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Displays definition summaries.
     *
     * @param array $definitionsummaries information about each definition, as returned by
     *      core_cache\administration_helper::get_definition_summaries().
     * @param context $context the system context.
     *
     * @return string HTML.
     */
    public function definition_summaries(array $definitionsummaries, context $context) {
        $table = new html_table();
        $table->head = array(
            get_string('definition', 'cache'),
            get_string('mode', 'cache'),
            get_string('component', 'cache'),
            get_string('area', 'cache'),
            get_string('mappings', 'cache'),
            get_string('sharing', 'cache'),
            get_string('canuselocalstore', 'cache'),
            get_string('actions', 'cache')
        );
        $table->colclasses = array(
            'definition',
            'mode',
            'component',
            'area',
            'mappings',
            'sharing',
            'canuselocalstore',
            'actions'
        );
        $table->data = array();

        core_collator::asort_array_of_arrays_by_key($definitionsummaries, 'name');

        $none = new lang_string('none', 'cache');
        foreach ($definitionsummaries as $id => $definition) {
            $htmlactions = cache_factory::get_administration_display_helper()->get_definition_actions($context, $definition);
            if (!empty($definition['mappings'])) {
                $mapping = join(', ', $definition['mappings']);
            } else {
                $mapping = '<em>'.$none.'</em>';
            }

            $uselocalcachecol = get_string('no');
            if ($definition['mode'] != cache_store::MODE_REQUEST) {
                if (isset($definition['canuselocalstore']) && $definition['canuselocalstore']) {
                    $uselocalcachecol = get_string('yes');
                }
            }

            $row = new html_table_row(array(
                $definition['name'],
                get_string('mode_'.$definition['mode'], 'cache'),
                $definition['component'],
                $definition['area'],
                $mapping,
                join(', ', $definition['selectedsharingoption']),
                $uselocalcachecol,
                join(', ', $htmlactions)
            ));
            $row->attributes['class'] = 'definition-'.$definition['component'].'-'.$definition['area'];
            $table->data[] = $row;
        }

        $html  = html_writer::start_tag('div', array('id' => 'core-cache-definition-summaries'));
        $html .= $this->output->heading(get_string('definitionsummaries', 'cache'), 3);
        $html .= html_writer::table($table);

        $url = new moodle_url('/cache/admin.php', array('action' => 'rescandefinitions', 'sesskey' => sesskey()));
        $link = html_writer::link($url, get_string('rescandefinitions', 'cache'));
        $html .= html_writer::tag('div', $link, array('id' => 'core-cache-rescan-definitions'));

        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Displays mode mappings
     *
     * @param string $applicationstore
     * @param string $sessionstore
     * @param string $requeststore
     * @param moodle_url $editurl
     * @return string HTML
     */
    public function mode_mappings($applicationstore, $sessionstore, $requeststore, moodle_url $editurl) {
        $table = new html_table();
        $table->colclasses = array(
            'mode',
            'mapping',
        );
        $table->rowclasses = array(
            'mode_application',
            'mode_session',
            'mode_request'
        );
        $table->head = array(
            get_string('mode', 'cache'),
            get_string('mappings', 'cache'),
        );
        $table->data = array(
            array(get_string('mode_'.cache_store::MODE_APPLICATION, 'cache'), $applicationstore),
            array(get_string('mode_'.cache_store::MODE_SESSION, 'cache'), $sessionstore),
            array(get_string('mode_'.cache_store::MODE_REQUEST, 'cache'), $requeststore)
        );

        $html = html_writer::start_tag('div', array('id' => 'core-cache-mode-mappings'));
        $html .= $this->output->heading(get_string('defaultmappings', 'cache'), 3);
        $html .= html_writer::table($table);
        $link = html_writer::link($editurl, get_string('editmappings', 'cache'));
        $html .= html_writer::tag('div', $link, array('class' => 'edit-link'));
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Display basic information about lock instances.
     *
     * @todo Add some actions so that people can configure lock instances.
     *
     * @param array $locks
     * @return string
     */
    public function lock_summaries(array $locks) {
        $table = new html_table();
        $table->colclasses = array(
            'name',
            'type',
            'default',
            'uses',
            'actions'
        );
        $table->rowclasses = array(
            'lock_name',
            'lock_type',
            'lock_default',
            'lock_uses',
            'lock_actions',
        );
        $table->head = array(
            get_string('lockname', 'cache'),
            get_string('locktype', 'cache'),
            get_string('lockdefault', 'cache'),
            get_string('lockuses', 'cache'),
            get_string('actions', 'cache')
        );
        $table->data = array();
        $tick = $this->output->pix_icon('i/valid', '');
        foreach ($locks as $lock) {
            $actions = array();
            if ($lock['uses'] === 0 && !$lock['default']) {
                $url = new moodle_url('/cache/admin.php', array('lock' => $lock['name'], 'action' => 'deletelock', 'sesskey' => sesskey()));
                $actions[] = html_writer::link($url, get_string('delete', 'cache'));
            }
            $table->data[] = new html_table_row(array(
                new html_table_cell($lock['name']),
                new html_table_cell($lock['type']),
                new html_table_cell($lock['default'] ? $tick : ''),
                new html_table_cell($lock['uses']),
                new html_table_cell(join(' ', $actions))
            ));
        }

        $html = html_writer::start_tag('div', array('id' => 'core-cache-lock-summary'));
        $html .= $this->output->heading(get_string('locksummary', 'cache'), 3);
        $html .= html_writer::table($table);
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Renders additional actions for locks, such as Add.
     *
     * @return string
     */
    public function additional_lock_actions() : string {
        $url = new moodle_url('/cache/admin.php', array('action' => 'newlockinstance', 'sesskey' => sesskey()));
        $select = new single_select($url, 'lock', cache_factory::get_administration_display_helper()->get_addable_lock_options());
        $select->label = get_string('addnewlockinstance', 'cache');

        $html = html_writer::start_tag('div', array('id' => 'core-cache-lock-additional-actions'));
        $html .= html_writer::tag('div', $this->output->render($select), array('class' => 'new-instance'));
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Renders an array of notifications for the cache configuration screen.
     *
     * Takes an array of notifications with the form:
     * $notifications = array(
     *     array('This is a success message', true),
     *     array('This is a failure message', false),
     * );
     *
     * @param array $notifications
     * @return string
     */
    public function notifications(array $notifications = array()) {
        if (count($notifications) === 0) {
            // There are no notifications to render.
            return '';
        }
        $html = html_writer::start_div('notifications');
        foreach ($notifications as $notification) {
            list($message, $notifysuccess) = $notification;
            $html .= $this->notification($message, ($notifysuccess) ? 'notifysuccess' : 'notifyproblem');
        }
        $html .= html_writer::end_div();
        return $html;
    }
}
