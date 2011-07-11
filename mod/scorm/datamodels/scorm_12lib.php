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
 * This is really a little language parser for AICC_SCRIPT
 * evaluates the expression and returns a boolean answer
 * see 2.3.2.5.1. Sequencing/Navigation Today  - from the SCORM 1.2 spec (CAM).
 *
 * @param string $prerequisites the aicc_script prerequisites expression
 * @param array  $usertracks the tracked user data of each SCO visited
 * @return boolean
 */
function scorm_eval_prerequisites($prerequisites, $usertracks) {

    // this is really a little language parser - AICC_SCRIPT is the reference
    // see 2.3.2.5.1. Sequencing/Navigation Today  - from the SCORM 1.2 spec
    $element = '';
    $stack = array();
    $statuses = array(
                'passed' => 'passed',
                'completed' => 'completed',
                'failed' => 'failed',
                'incomplete' => 'incomplete',
                'browsed' => 'browsed',
                'not attempted' => 'notattempted',
                'p' => 'passed',
                'c' => 'completed',
                'f' => 'failed',
                'i' => 'incomplete',
                'b' => 'browsed',
                'n' => 'notattempted'
                );
    $i=0;

    // expand the amp entities
    $prerequisites = preg_replace('/&amp;/', '&', $prerequisites);
    // find all my parsable tokens
    $prerequisites = preg_replace('/(&|\||\(|\)|\~)/', '\t$1\t', $prerequisites);
    // expand operators
    $prerequisites = preg_replace('/&/', '&&', $prerequisites);
    $prerequisites = preg_replace('/\|/', '||', $prerequisites);
    // now - grab all the tokens
    $elements = explode('\t', trim($prerequisites));

    // process each token to build an expression to be evaluated
    $stack = array();
    foreach ($elements as $element) {
        $element = trim($element);
        if (empty($element)) {
            continue;
        }
        if (!preg_match('/^(&&|\|\||\(|\))$/', $element)) {
            // create each individual expression
            // search for ~ = <> X*{}

            // sets like 3*{S34, S36, S37, S39}
            if (preg_match('/^(\d+)\*\{(.+)\}$/', $element, $matches)) {
                $repeat = $matches[1];
                $set = explode(',', $matches[2]);
                $count = 0;
                foreach ($set as $setelement) {
                    if (isset($usertracks[$setelement]) &&
                       ($usertracks[$setelement]->status == 'completed' || $usertracks[$setelement]->status == 'passed')) {
                        $count++;
                    }
                }
                if ($count >= $repeat) {
                    $element = 'true';
                } else {
                    $element = 'false';
                }

            // ~ Not
            } else if ($element == '~') {
                $element = '!';

            // = | <>
            } else if (preg_match('/^(.+)(\=|\<\>)(.+)$/', $element, $matches)) {
                $element = trim($matches[1]);
                if (isset($usertracks[$element])) {
                    $value = trim(preg_replace('/(\'|\")/', '', $matches[3]));
                    if (isset($statuses[$value])) {
                        $value = $statuses[$value];
                    }
                    if ($matches[2] == '<>') {
                        $oper = '!=';
                    } else {
                      $oper = '==';
                    }
                    $element = '(\''.$usertracks[$element]->status.'\' '.$oper.' \''.$value.'\')';
                } else {
                  $element = 'false';
                }

            // everything else must be an element defined like S45 ...
            } else {
                if (isset($usertracks[$element]) &&
                    ($usertracks[$element]->status == 'completed' || $usertracks[$element]->status == 'passed')) {
                    $element = 'true';
                } else {
                    $element = 'false';
                }
            }

        }
        $stack []= ' '.$element.' ';
    }
    return eval('return '.implode($stack).';');
}
