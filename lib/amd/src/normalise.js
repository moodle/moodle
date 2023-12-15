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
 * Normalisation helpers.
 *
 * @module     core/normalise
 * @copyright  2020 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import jQuery from 'jquery';

/**
 * Normalise a list of Nodes into an Array of Nodes.
 *
 * @method getList
 * @param {(Array|jQuery|NodeList|HTMLElement)} nodes
 * @returns {HTMLElement[]}
 */
export const getList = nodes => {
    if (nodes instanceof HTMLElement) {
        // A single record to conver to a NodeList.
        return [nodes];
    }

    if (nodes instanceof Array) {
        // A single record to conver to a NodeList.
        return nodes;
    }

    if (nodes instanceof NodeList) {
        // Already a NodeList.
        return Array.from(nodes);
    }

    if (nodes instanceof jQuery) {
        // A jQuery object to a NodeList.
        return nodes.get();
    }

    // Fallback to just having a go.
    return Array.from(nodes);
};

/**
 * Return the first element in a list of normalised Nodes.
 *
 * @param {Array|jQuery|NodeList|HTMLElement} nodes the unmormalised list of nodes
 * @returns {HTMLElement|undefined} the first list element
 */
export const getFirst = nodes => {
    const list = getList(nodes);
    return list[0];
};

/**
 * Normalise a single node into an HTMLElement.
 *
 * @param {jQuery|Y.Node|HTMLElement} node The node to normalise
 * @returns {HTMLElement}
 */
export const getElement = (node) => {
    if (node instanceof HTMLElement) {
        return node;
    }

    if (node?._node) {
        // This is likely a YUI Node.
        // We can use (node instanceof Y.Node) but we would have to load YUI to do some.
        return node._node;
    }

    if (node instanceof jQuery && node.length > 0) {
        return node.get(0);
    }

    return null;
};
