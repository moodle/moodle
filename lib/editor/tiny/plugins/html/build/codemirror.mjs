// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Rollup generator for CodeMirror
 *
 * @copyright   2023 Matt Porritt <matt.porritt@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import { EditorView, basicSetup } from "codemirror"
import { EditorState } from "@codemirror/state";
import { html } from "@codemirror/lang-html"
import { xml } from "@codemirror/lang-xml"
import { javascript } from "@codemirror/lang-javascript"

const lang = {
    html,
    javascript,
    xml,
};

export {
    EditorView,
    EditorState,
    basicSetup,
    lang,
};
