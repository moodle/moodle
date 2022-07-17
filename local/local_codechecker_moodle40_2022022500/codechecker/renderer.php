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
 * Renderer for displaying code-checker reports as HTML.
 *
 * @package    local_codechecker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class local_codechecker_renderer extends plugin_renderer_base {
    /** @var bool show phpsc standard flag. */
    private $showstandard;

    /** @var array string replaces used to clean up the input line for display. */
    protected $replaces = array(
        "\t" => '<span>&#x25b6;</span>',
        ' '  => '<span>&#183;</span>',
    );

    /**
     * Display the start of the list of the files checked.
     * @param int $numfiles the number of files checked.
     * @return string HTML to output.
     */
    public function summary_start($numfiles) {
        return html_writer::tag('h2', get_string('filesfound', 'local_codechecker', $numfiles)) .
                html_writer::start_tag('ul');
    }

    /**
     * Display an entry in the list of the files checked.
     * @param int $fileindex unique index of this file.
     * @param string $prettypath the name of the file checked.
     * @param int $numproblems the number of problems found in this file.
     * @return string HTML to output.
     */
    public function summary_line($fileindex, $prettypath, $numproblems) {
        if ($numproblems) {
            return html_writer::tag('li', html_writer::link(new moodle_url('#file' . $fileindex),
                    get_string('filesummary', 'local_codechecker',
                        array('path' => s($prettypath), 'count' => $numproblems))),
                    array('class' => 'fail'));
        } else {
            return html_writer::tag('li', s($prettypath), array('class' => 'good'));
        }
    }

    /**
     * Display the end of the list of the files checked.
     *
     * @param int $numfiles the number of files checked.
     * @param string $summary results summary string.
     * @param string $type results status (fail/warning).
     * @return string HTML to output.
     */
    public function summary_end($numfiles, $summary, $type) {
        $output = html_writer::end_tag('ul');
        if ($summary) {
            $output .= html_writer::tag('h2', get_string('summary', 'local_codechecker',
                    $summary), array('class' => $type));
        } else {
            $output .= html_writer::tag('h2', get_string('success', 'local_codechecker'),
                    array('class' => 'good'));
        }
        return $output;
    }

    /**
     * Display a message about the path being invalid.
     * @param string $path the invaid path.
     * @return string HTML to output.
     */
    public function invald_path_message($path) {
        return $this->output->notification(get_string(
                'invalidpath', 'local_codechecker', s($path)));
    }

    /**
     * Render the whole report to html.
     *
     * @param SimpleXMLElement $xml structure containing all the information to be rendered.
     * @param int $numerrors total number of error-level violations in the run.
     * @param int $numwarnings total number of warning-level violations in the run.
     * @param bool $showstandard Show phpcs standard associated with problem.
     * @return string the report html
     */
    public function report(SimpleXMLElement $xml, $numerrors, $numwarnings, $showstandard = false) {
        $this->showstandard = $showstandard;

        $grandsummary = '';
        $grandtype = '';
        if ($numerrors + $numwarnings > 0) {
            $grandsummary = get_string('numerrorswarnings', 'local_codechecker',
                    array('errors' => $numerrors, 'warnings' => $numwarnings));
            if ($numerrors) {
                $grandtype = 'fail error';
            } else {
                $grandtype = 'fail warning';
            }
        }

        // Output begins.
        $output = '';

        $output .= html_writer::start_tag('div', array('class' => 'local_codechecker_results'));

        // Sort the file by path.
        $files = $xml->xpath('file');
        $sortedfiles = array();
        foreach ($files as $fileinxml) {
            $sortedfiles[local_codechecker_pretty_path($fileinxml['name'])] = $fileinxml;
        }
        ksort($sortedfiles);
        $files = $sortedfiles;

        // Files count and list.
        $numfiles = count($files);
        $output .= $this->summary_start($numfiles);

        // Heading summaries.
        $index = 0;
        foreach ($files as $prettypath => $fileinxml) {
            $index++;

            $summary = '';
            if ($fileinxml['errors'] + $fileinxml['warnings'] > 0) {
                $numerrwarn = (object) array('errors' => "${fileinxml['errors']}", 'warnings' => "${fileinxml['warnings']}");
                $summary = get_string('numerrorswarnings', 'local_codechecker', $numerrwarn);
            }

            $output .= $this->summary_line($index, $prettypath, $summary);
        }
        $output .= $this->summary_end($numfiles, $grandsummary, $grandtype);

        // Details.
        $index = 0;
        foreach ($files as $prettypath => $fileinxml) {
            $index++;

            if ($fileinxml['errors'] + $fileinxml['warnings'] == 0) {
                continue;
            }

            $output .= $this->problems($index, $fileinxml, $prettypath);
        }

        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Display the full results of checking a file. Will only be called if
     * $problems is a non-empty array.
     * @param int $fileindex unique index of this file.
     * @param SimpleXMLElement $fileinxml the file with all its problems.
     * @param string $prettypath prettified file path.
     * @return string HTML to output.
     */
    public function problems($fileindex, $fileinxml, $prettypath) {
        $output = html_writer::start_tag('div',
                array('class' => 'resultfile', 'id' => 'file' . $fileindex));
        $output .= html_writer::tag('h3', html_writer::link(
                new moodle_url('/local/codechecker/', array('path' => $prettypath)),
                s($prettypath), array('title' => get_string('recheckfile', 'local_codechecker'))));
        $output .= html_writer::start_tag('ul');

        foreach ($fileinxml->xpath('error|warning') as $problem) {
            $output .= $this->problem_message($problem, $prettypath);
        }

        $output .= html_writer::end_tag('ul');
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Display an individual problem.
     *
     * @param SimpleXMLElement $problem structure to be displayed.
     * @param string $prettypath relative path to the file
     * @return string html to display an individual problem
     */
    public function problem_message($problem, $prettypath) {
        static $lastfileandline = ''; // To detect changes of line.
        $line = $problem['line'];
        $level = $problem->getName();

        $code = '';
        if ($lastfileandline !== $prettypath . '#@#' . $line) {
            // We have moved to another line, output it.
            $code = html_writer::tag('li', html_writer::tag('div',
                html_writer::tag('pre', '#' . $line . ': ' . str_replace(
                    array_keys($this->replaces),
                    array_values($this->replaces),
                    s(local_codechecker_get_line_of_code($line, $prettypath))
                    ))),
                array('class' => 'sourcecode')
            );
            $lastfileandline = $prettypath . '#@#' . $line;
        }

        $sourceclass = str_replace('.', '_', $problem['source']);
        if ($this->showstandard) {
            $problem = (string) $problem . ' (' . $problem['source'] . ')';
        }
        $info = html_writer::tag('div', s($problem), array('class' => 'info ' . $sourceclass));

        return $code .  html_writer::tag('li', $info, array('class' => 'fail ' . $level));
    }
}
