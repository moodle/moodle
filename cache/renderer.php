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
     * @param array $stores
     * @param array $plugins
     * @return string HTML
     */
    public function store_instance_summariers(array $stores, array $plugins) {
        $table = new html_table();
        $table->head = array(
            get_string('storename', 'cache'),
            get_string('plugin', 'cache'),
            get_string('storeready', 'cache'),
            get_string('mappings', 'cache'),
            get_string('modes', 'cache'),
            get_string('supports', 'cache'),
            get_string('actions', 'cache'),
        );
        $table->colclasses = array(
            'storename',
            'plugin',
            'storeready',
            'mappings',
            'modes',
            'supports',
            'actions'
        );
        $table->data = array();

        $defaultstoreactions = get_string('defaultstoreactions', 'cache');

        foreach ($stores as $name => $store) {
            $actions = cache_administration_helper::get_store_instance_actions($name, $store);
            $modes = array();
            foreach ($store['modes'] as $mode => $enabled) {
                if ($enabled) {
                    $modes[] = get_string('mode_'.$mode, 'cache');
                }
            }

            $supports = array();
            foreach ($store['supports'] as $support => $enabled) {
                if ($enabled) {
                    $supports[] = get_string('supports_'.$support, 'cache');
                }
            }

            $info = '';
            if (!empty($store['default'])) {
                $info = $this->output->pix_icon('i/info', $defaultstoreactions, '', array('class' => 'icon'));
            }
            $htmlactions = array();
            foreach ($actions as $action) {
                $htmlactions[] = $this->output->action_link($action['url'], $action['text']);
            }

            $storename = $store['name'];
            if (!empty($store['default'])) {
                $storename = get_string('store_'.$store['name'], 'cache');
            }

            $row = new html_table_row(array(
                $storename,
                get_string('pluginname', 'cachestore_'.$store['plugin']),
                ($store['isready'] && $store['requirementsmet']) ? $this->output->pix_icon('i/valid', '1') : '',
                $store['mappings'],
                join(', ', $modes),
                join(', ', $supports),
                $info.join(', ', $htmlactions)
            ));
            $row->attributes['class'] = 'store-'.$name;
            if ($store['default']) {
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
     * Displays plugin summaries
     *
     * @param array $plugins
     * @return string HTML
     */
    public function store_plugin_summaries(array $plugins) {
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

        foreach ($plugins as $name => $plugin) {
            $actions = cache_administration_helper::get_store_plugin_actions($name, $plugin);

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

            $htmlactions = array();
            foreach ($actions as $action) {
                $htmlactions[] = $this->output->action_link($action['url'], $action['text']);
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
     * Displays definition summaries
     *
     * @param array $definitions
     * @param array $actions
     * @return string HTML
     */
    public function definition_summaries(array $definitions, array $actions) {
        $table = new html_table();
        $table->head = array(
            get_string('definition', 'cache'),
            get_string('mode', 'cache'),
            get_string('component', 'cache'),
            get_string('area', 'cache'),
            get_string('mappings', 'cache'),
            get_string('actions', 'cache'),
        );
        $table->colclasses = array(
            'definition',
            'mode',
            'component',
            'area',
            'mappings',
            'actions'
        );
        $table->data = array();

        $none = new lang_string('none', 'cache');
        foreach ($definitions as $id => $definition) {
            $htmlactions = array();
            foreach ($actions as $action) {
                $action['url']->param('definition', $id);
                $htmlactions[] = $this->output->action_link($action['url'], $action['text']);
            }
            if (!empty($definition['mappings'])) {
                $mapping = join(', ', $definition['mappings']);
            } else {
                $mapping = '<em>'.$none.'</em>';
            }

            $row = new html_table_row(array(
                $definition['name'],
                get_string('mode_'.$definition['mode'], 'cache'),
                $definition['component'],
                $definition['area'],
                $mapping,
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
            'default',
            'uses',
            // Useful later: 'actions'.
        );
        $table->rowclasses = array(
            'lock_name',
            'lock_default',
            'lock_uses',
            // Useful later: 'lock_actions',.
        );
        $table->head = array(
            get_string('lockname', 'cache'),
            get_string('lockdefault', 'cache'),
            get_string('lockuses', 'cache'),
            // Useful later: get_string('actions', 'cache').
        );
        $table->data = array();
        $tick = $this->output->pix_icon('i/valid', '');
        foreach ($locks as $lock) {
            $table->data[] = new html_table_row(array(
                new html_table_cell($lock['name']),
                new html_table_cell($lock['default'] ? $tick : ''),
                new html_table_cell($lock['uses']),
            ));
        }

        $html = html_writer::start_tag('div', array('id' => 'core-cache-lock-summary'));
        $html .= $this->output->heading(get_string('locksummary', 'cache'), 3);
        $html .= html_writer::table($table);
        $html .= html_writer::end_tag('div');
        return $html;
    }
}