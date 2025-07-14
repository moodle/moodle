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
 * Behat steps definitions for drag and drop markers.
 *
 * @package   qtype_ddmarker
 * @category  test
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../../lib/behat/behat_base.php');

/**
 * Steps definitions related with the drag and drop markers question type.
 *
 * @copyright 2015 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_qtype_ddmarker extends behat_base {

    /**
     * Get the xpath for a given drag item.
     *
     * @param string $marker the text of the item to drag.
     * @param bool $iskeyboard is using keyboard or not.
     * @return string the xpath expression.
     */
    protected function marker_xpath($marker, $iskeyboard = false) {
        if ($iskeyboard) {
            return '//span[contains(@class, "marker") and not(contains(@class, "dragplaceholder")) ' .
                    'and span[@class = "markertext" and contains(normalize-space(.), "' .
                    $this->escape($marker) . '")]]';
        }
        return '//span[contains(@class, "marker") and contains(@class, "unneeded") ' .
                'and not(contains(@class, "dragplaceholder")) and span[@class = "markertext" and contains(normalize-space(.), "' .
                $this->escape($marker) . '")]]';
    }

    /**
     * Drag the drag item with the given text to the given space.
     *
     * @param string $marker the marker to drag. The label, optionally followed by ,<instance number> (int) if relevant.
     * @param string $coordinates the position to drag the marker to, 'x,y'.
     *
     * @Given /^I drag "(?P<marker>[^"]*)" to "(?P<coordinates>\d+,\d+)" in the drag and drop markers question$/
     */
    public function i_drag_to_in_the_drag_and_drop_markers_question($marker, $coordinates) {
        list($x, $y) = explode(',', $coordinates);

        // This is a bit nasty, but Behat (indeed Selenium) will only drag on
        // DOM node so that its centre is over the centre of anothe DOM node.
        // Therefore to make it drag to the specified place, we have to add
        // a target div.
        // We also need to scroll the marker into view so we can calculate the correct offsetHeight and offsetWidth.
        $markerxpath = $this->marker_xpath($marker);
        $this->execute_script("
                (function() {
                    require(['core/pending'], function(Pending) {
                        if (document.getElementById('target-{$x}-{$y}')) {
                            return;
                        }
                        const pendingPromise = new Pending('qtype_ddmarker:drag-drop');
                        const image = document.querySelector('.dropbackground');
                        const target = document.createElement('div');
                        target.setAttribute('id', 'target-{$x}-{$y}');
                        const container = document.querySelector('.droparea');
                        container.insertBefore(target, image);
                        const widthRatio = image.offsetWidth / image.naturalWidth;
                        const heightRatio = image.offsetHeight / image.naturalHeight;
                        const marker = document.evaluate('{$markerxpath}', document, null, XPathResult.ANY_TYPE, null).iterateNext();
                        marker.scrollIntoView();
                        const xadjusted = {$x} * widthRatio
                                        + (container.offsetWidth - image.offsetWidth) / 2
                                        + marker.offsetWidth / 2;
                        const yadjusted = {$y} * heightRatio
                                        + (container.offsetHeight - image.offsetHeight) / 2
                                        + marker.offsetHeight / 2;
                        target.style.setProperty('position', 'absolute');
                        target.style.setProperty('left', xadjusted + 'px');
                        target.style.setProperty('top', yadjusted + 'px');
                        target.style.setProperty('width', '1px');
                        target.style.setProperty('height', '1px');
                        pendingPromise.resolve();
                    });
                }())"
        );

        $generalcontext = behat_context_helper::get('behat_general');
        $generalcontext->i_drag_and_i_drop_it_in($markerxpath,
                'xpath_element', "#target-{$x}-{$y}", 'css_element');
    }

    /**
     * Type some characters while focussed on a given drop box.
     *
     * @param string $direction the direction key to press.
     * @param int $
     * @param string $marker the marker to drag. The label, optionally followed by ,<instance number> (int) if relevant.
     *
     * @Given /^I type "(?P<direction>up|down|left|right)" "(?P<repeats>\d+)" times on marker "(?P<marker>[^"]*)" in the drag and drop markers question$/
     */
    public function i_type_on_marker_in_the_drag_and_drop_markers_question($direction, $repeats, $marker) {
        $node = $this->get_selected_node('xpath_element', $this->marker_xpath($marker, true));
        $this->ensure_node_is_visible($node);
        $node->focus();
        for ($i = 0; $i < $repeats; $i++) {
            $this->execute('behat_general::i_press_named_key', ['', $direction]);
        }
    }
}
