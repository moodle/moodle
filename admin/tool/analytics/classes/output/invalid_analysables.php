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
 * Invalid analysables renderable.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_analytics\output;

defined('MOODLE_INTERNAL') || die;

/**
 * Invalid analysables renderable.
 *
 * @package    tool_analytics
 * @copyright  2017 David Monllao {@link http://www.davidmonllao.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_analysables implements \renderable, \templatable {

    /**
     * @var \core_analytics\model
     */
    protected $model = null;

    /**
     * @var int
     */
    protected $page = 0;

    /**
     * @var int
     */
    protected $perpage = 0;

    /**
     * Inits the invalid analysables renderable.
     *
     * @param \core_analytics\model $model
     * @param int $page
     * @param int $perpage
     * @return \stdClass
     */
    public function __construct(\core_analytics\model $model, $page, $perpage) {

        $this->model = $model;
        $this->page = $page;
        $this->perpage = $perpage;
    }

    /**
     * Export the data.
     *
     * @param \renderer_base $output
     * @return \stdClass
     */
    public function export_for_template(\renderer_base $output) {
        global $PAGE;

        $offset = $this->page * $this->perpage;

        $analysables = $this->model->get_analyser(['notimesplitting' => true])->get_analysables();

        $skipped = 0;
        $enoughresults = false;
        $morepages = false;
        $results = array();
        foreach ($analysables as $key => $analysable) {

            $validtraining = $this->model->get_target()->is_valid_analysable($analysable, true);
            if ($validtraining === true) {
                if ($this->model->is_static()) {
                    // We still want to show this analysable if it is not valid to get predictions.
                    $validtraining = get_string('notrainingbasedassumptions', 'analytics');
                } else {
                    // We skip analysables that are valid for training or valid for prediction.
                    continue;
                }
            }

            $validprediction = $this->model->get_target()->is_valid_analysable($analysable, false);
            if ($validprediction === true) {
                // We skip analysables that are valid for training or valid for prediction.
                continue;
            }

            if ($offset && $skipped < $offset) {
                $skipped++;
                continue;
            }

            // Add a new results if we don't have enough yet.
            if (!$enoughresults) {
                $results[$analysable->get_id()] = array($analysable, $validtraining, $validprediction);
                if ($this->perpage && count($results) === $this->perpage) {
                    $enoughresults = true;
                }
            } else {
                // Confirmed that we have results we can not fit into this page.
                $morepages = true;
                break;
            }

            unset($analysables[$key]);
        }

        // Prepare the context object.
        $data = new \stdClass();
        $data->modelname = $this->model->get_target()->get_name();

        if ($this->page > 0) {
            $prev = clone $PAGE->url;
            $prev->param('page', $this->page - 1);
            $button = new \single_button($prev, get_string('previouspage', 'tool_analytics'), 'get');
            $data->prev = $button->export_for_template($output);
        }
        if ($morepages) {
            $next = clone $PAGE->url;
            $next->param('page', $this->page + 1);
            $button = new \single_button($next, get_string('nextpage', 'tool_analytics'), 'get');
            $data->next = $button->export_for_template($output);
        }

        $data->analysables = [];
        foreach ($results as list($analysable, $validtraining, $validprediction)) {
            $obj = new \stdClass();
            $obj->url = \html_writer::link($analysable->get_context()->get_url(), $analysable->get_name(),
                array('target' => '_blank'));

            if ($validtraining !== true) {
                $obj->validtraining = $validtraining;
            }
            if ($validprediction !== true) {
                $obj->validprediction = $validprediction;
            }
            $data->analysables[] = $obj;
        }

        if (empty($data->analysables)) {
            $data->noanalysables = [
                'message' => get_string('noinvalidanalysables', 'tool_analytics'),
                'announce' => true,
            ];
        }
        return $data;
    }
}
