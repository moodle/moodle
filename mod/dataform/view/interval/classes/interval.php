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
 * @package dataformview
 * @subpackage interval
 * @copyright 2012 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class dataformview_interval_interval extends dataformview_grid_grid {

    protected $selection;
    protected $interval;
    protected $custom;
    protected $resetnext;
    protected $page;
    protected $cache = null;

    /**
     * Returns the view html to display.
     *
     * @param array $options An array of display options
     * @return string HTML fragment
     */
    public function display(array $options = array()) {
        $this->selection = $this->param4 ? $this->param4 : 0;
        $this->interval = $this->param5 ? $this->param5 : 0;
        $this->custom = $this->param6 ? $this->param6 : 0;
        $this->resetnext = $this->param8 ? $this->param8 : 100;

        $this->page = $this->filter->page;

        $this->filter->selection = $this->selection;

        // Set or clean up cache according to interval.
        if (!$this->interval and $this->param7) {
            $this->param7 = null;
            $this->update($this->data);
        }

        // Check if view is caching.
        if ($this->interval) {
            $filteroptions = $this->get_cache_filter_options();
            foreach ($filteroptions as $option => $value) {
                $this->filter->{$option} = $value;
            }

            if (!$entriesset = $this->get_cache_content()) {
                $entriesset = $this->entry_manager->fetch_entries(array('filter' => $this->filter));
                $this->update_cache_content($entriesset);
                $this->filter->page = $entriesset->page;
            }
            $options['entriesset'] = $entriesset;
        }
        return parent::display($options);
    }

    /**
     *
     */
    public function update_cache_content($entriesset) {
        $this->cache->content = $entriesset;
        $this->param7 = serialize($this->cache);
        $this->update($this->data);
    }

    /**
     *
     */
    public function get_cache_filter_options() {
        $options = array();
        // Setting the cache may change page number.
        if ($this->page > 0) {
            // Next is used and page advances.
            $options['page'] = $this->page;
        }
        return $options;
    }

    /**
     *
     */
    public function get_cache_content() {
        $refresh = $this->set_cache();
        if (!$refresh and isset($this->cache->content)) {
            return $this->cache->content;
        } else {
            return null;
        }
    }

    /**
     *
     */
    protected function set_cache() {

        // Assumes we are caching and interval is set.
        $now = time();
        if ($this->param7) {
            $this->cache = unserialize($this->param7);
            if (!empty($this->cache->next)) {
                $this->page = $this->cache->next;
            }
        } else {
            // First time.
            $this->cache = new stdClass;
            $this->cache->time = 0;
        }

        // Get checktime.
        switch ($this->interval) {
            case 'monthly':
                $checktime = mktime(0, 0, 0, date('m'), 1, date('Y'));
                break;

            case 'weekly':
                $checktime = strtotime('last '. get_string('firstdayofweek', 'dataform'));
                break;

            case 'daily':
                $checktime = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
                break;

            case 'hourly':
                $checktime = mktime(date('H'), 0, 0, date('m'), date('d'), date('Y'));
                break;

            case 'custom':
                $checktime = $now - ($now % $this->custom);
                break;

            default:
                $checktime = $now;
                break;
        }

        if ($checktime > $this->cache->time) {
            $this->cache->time = $checktime;

            if ($this->selection == mod_dataform_entry_manager::SELECT_NEXT_PAGE) {
                $this->cache->next++;
                if ($this->cache->next > $this->resetnext) {
                    $this->cache->next = 0;
                }
                $this->page = $this->cache->next;
            }
            return true;
        } else {
            return false;
        }
    }

}
