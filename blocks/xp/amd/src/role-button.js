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
 * Role button.
 *
 * @copyright  2022 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define([], function() {
    /**
     * Delegate click to a zone.
     *
     * @param {String} rootSelector The root selector.
     * @param {String} nodeSelector The node on which the event must happen.
     * @param {Function} onClick The callback, receiving the node.
     */
    function delegateClick(rootSelector, nodeSelector, onClick) {
        const nodes = document.querySelectorAll(rootSelector);

        const handleHit = (e) => {
            const node = e.target.closest('[role=button]');
            if (node && node.matches(nodeSelector)) {
                if (e.defaultPrevented) {
                    return;
                }
                e.preventDefault();
                onClick(node);
            }
        };

        nodes.forEach((node) => {
            node.addEventListener('click', handleHit);

            node.addEventListener('mousedown', (e) => {
                if (e.key !== ' ' && e.key !== 'Enter') {
                    return;
                }
                handleHit(e);
            });

            node.querySelectorAll(nodeSelector).forEach((node) => {
                if (!node.getAttribute('role')) {
                    node.setAttribute('role', 'button');
                }
            });
        });
    }

    /**
     * Register a click.
     *
     * @param {String} selector The selector.
     * @param {Function} onClick The callback, receiving the node.
     */
    function registerClick(selector, onClick) {
        const nodes = document.querySelectorAll(selector);

        nodes.forEach((node) => {
            const handleHit = (e) => {
                if (e.defaultPrevented) {
                    return;
                }
                e.preventDefault();
                onClick(node);
            };

            node.addEventListener('click', handleHit);
            node.addEventListener('mousedown', (e) => {
                if (e.key !== ' ' && e.key !== 'Enter') {
                    return;
                }
                handleHit(e);
            });

            if (!node.getAttribute('role')) {
                node.setAttribute('role', 'button');
            }
        });
    }

    return {
        delegateClick,
        registerClick,
    };
});
