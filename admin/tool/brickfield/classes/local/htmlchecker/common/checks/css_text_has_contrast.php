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

namespace tool_brickfield\local\htmlchecker\common\checks;

use DOMXPath;
use tool_brickfield\local\htmlchecker\common\brickfield_accessibility_color_test;

/**
 * Brickfield accessibility HTML checker library.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class css_text_has_contrast extends brickfield_accessibility_color_test {

    /** @var int The default severity code for this test. */
    public $defaultseverity = \tool_brickfield\local\htmlchecker\brickfield_accessibility::BA_TEST_SEVERE;

    /** @var string The default background color. */
    public $defaultbackground = '#ffffff';

    /** @var string The default color. */
    public $defaultcolor = '#000000';

    /**
     * The main check function. This is called by the parent class to actually check content
     */
    public function check(): void {
        if (isset($this->options['css_background'])) {
            $this->defaultbackground = $this->options['css_background'];
        }

        if (isset($this->options['css_foreground'])) {
            $this->defaultcolor = $this->options['css_foreground'];
        }

        $xpath = new DOMXPath($this->dom);

        // Selects all nodes that have a style attribute OR 'strong' OR 'em' elements that:
        // Contain only the text in their text nodes
        // OR Have text nodes AND text nodes that are not equal to the string-value of the context node
        // OR Have a text node descendant that equals the string-value of the context node and has no style attributes.

        $entries = $xpath->query('//*[(text() = . or ( ./*[text() != .]) or (.//*[text() = . and not(@style)]))
            and ((@style) or (name() = "strong") or (name() = "em"))]');

        foreach ($entries as $element) {
            $style = $this->css->get_style($element);

            if (isset($style['background-color']) || isset($style['color'])) {
                if (!isset($style['background-color'])) {
                    $style['background-color'] = $this->defaultbackground;
                }

                if (!isset($style['color'])) {
                    $style['color'] = $this->defaultcolor;
                }

                if ((isset($style['background']) || isset($style['background-color'])) && isset($style['color']) &&
                    $element->nodeValue) {

                    $background = (isset($style['background-color'])) ? $style['background-color'] : $style['background'];
                    if (!$background || !empty($this->options['css_only_use_default'])) {
                        $background = $this->defaultbackground;
                    }

                    $style['color'] = '#' . $this->convert_color($style['color']);
                    $style['background-color'] = '#' . $this->convert_color($background);

                    if (substr($background, 0, 3) == "rgb") {
                        $background = '#' . $this->convert_color($background);
                    }

                    $luminosity = $this->get_luminosity($style['color'], $background);
                    $fontsize = 0;
                    $bold = false;
                    $italic = false;

                    if (isset($style['font-size'])) {
                        $fontsize = $this->get_fontsize($style['font-size']);
                    }

                    if (isset($style['font-weight'])) {
                        preg_match_all('!\d+!', $style['font-weight'], $matches);

                        if (count($matches) > 0) {
                            if ($matches >= 700) {
                                $bold = true;
                            } else {
                                if ($style['font-weight'] === 'bold' || $style['font-weight'] === 'bolder') {
                                    $bold = true;
                                }
                            }
                        }
                    } else if ($element->tagName === "strong") {
                        $bold = true;
                        $style['font-weight'] = "bold";
                    } else {
                        $style['font-weight'] = "normal";
                    }

                    if (isset($style['font-style'])) {
                        if ($style['font-style'] === "italic") {
                            $italic = true;
                        }
                    } else if ($element->tagName === "em") {
                        $italic = true;
                        $style['font-style'] = "italic";
                    } else {
                        $style['font-style'] = "normal";
                    }

                    if ($element->tagName === 'h1' || $element->tagName === 'h2' || $element->tagName === 'h3' ||
                        $element->tagName === 'h4' || $element->tagName === 'h5' || $element->tagName === 'h6' ||
                        $fontsize >= 18 || $fontsize >= 14 && $bold) {
                        if ($luminosity < 3) {
                            $message = 'heading: background-color: ' . $background . '; color:' . $style["color"] .
                                '; font-style: ' . $style['font-style'] . '; font-weight: ' . $style['font-weight'] . '; ';
                            $this->add_report($element, $message);
                        }
                    } else {
                        if ($luminosity < 4.5) {
                            $message = 'text: background-color: ' . $background . '; color:' . $style["color"] . '; font-style: ' .
                                $style['font-style'] . '; font-weight: ' . $style['font-weight'] . '; ';
                            $this->add_report($element, $message);
                        }
                    }
                }
            }
        }
    }
}
