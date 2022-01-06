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
/*
 * @package    assignfeedback_editpdfplus
 * @copyright  2017 Université de Lausanne
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * @module mod_assignfeedback_editpdfplus/global
 * @returns {Global} global object
 */
define([],
        function () {
            /**
             * Global class.
             * Define constantes
             *
             * @class Global
             */
            function Global() {
                // Return this object reference.
                return(this);

            }

            Global.ANNOTATIONCOLOUR = {
                'white': 'rgb(255,255,255)',
                'yellowlemon': 'rgb(255,255,0)',
                'yellow': 'rgb(255,207,53)',
                'red': 'rgb(239,69,64)',
                'green': 'rgb(152,202,62)',
                'blue': 'rgb(0,0,255)',
                'black': 'rgb(51,51,51)'
            };

            Global.TOOLTYPE = {
                'HIGHLIGHTPLUS': 1,
                'LINEPLUS': 2,
                'STAMPPLUS': 3,
                'FRAME': 4,
                'VERTICALLINE': 5,
                'STAMPCOMMENT': 6,
                'COMMENTPLUS': 7,
                'PEN': 8,
                'LINE': 9,
                'RECTANGLE': 10,
                'OVAL': 11,
                'HIGHLIGHT': 12
            };

            Global.SELECTOR = {
                DRAWINGCANVAS: '.drawingcanvas'
            };

            Global.CSS = {
                DIALOGUE: 'assignfeedback_editpdfplus_widget'
            };

            return Global;
        });