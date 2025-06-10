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

function wrsqz_convert_for_compound($text) {
    $answerarray = array();
    $compoundanswertext = '<math xmlns="http://www.w3.org/1998/Math/MathML">';

    $text = trim($text);
    if (!strpos($text, '(')) {
        $answerarray = explode(" ", $text);
        foreach ($answerarray as $key => $value) {
            if ($key != 0) {
                $compoundanswertext .= '<mspace linebreak="newline"/>';
            }
            $value = trim($value);
            $compoundanswertext .= '<mi>' . substr($value, 1) . '</mi><mo>=</mo><mi>' . $value . '</mi>';
        }
    } else {
        $answerarray = explode(")", $text);
        foreach ($answerarray as $key => $value) {
            if ($value != '') {
                if ($key != 0) {
                    $compoundanswertext .= '<mspace linebreak="newline"/>';
                }
                $openpar = strpos($value, '(');
                $value = trim(substr($value, 0, $openpar));
                $compoundanswertext .= '<mi>' . substr($value, 1) . '</mi><mo>=</mo><mi>' . $value . '</mi>';
            }
        }
    }

    $compoundanswertext .= '</math>';
    return $compoundanswertext;
}
