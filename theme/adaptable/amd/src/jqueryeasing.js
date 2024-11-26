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

//
// ES6 wrapper for jqueryeasing.
//
// @module     theme_adaptable/jqueryeasing
// @copyright  2024 G J Barnard.
// @author     G J Barnard -
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//

import $ from 'jquery';
import log from 'core/log';

/**
 * Initialise jqueryeasing.
 */
export default function jqueryeasingInit() {
    log.debug('Adaptable ES6 jqueryeasing init');
    /*
     * jQuery Easing v1.4.1 - http://gsgd.co.uk/sandbox/jquery/easing/
     * Open source under the BSD License.
     * Copyright © 2008 George McGinley Smith
     * All rights reserved.
     * https://raw.github.com/gdsmith/jquery-easing/master/LICENSE
     */

    // Preserve the original jQuery "swing" easing as "jswing"
    if (typeof $.easing !== 'undefined') {
        $.easing['jswing'] = $.easing['swing'];
    }

    var pow = Math.pow,
        sqrt = Math.sqrt,
        sin = Math.sin,
        cos = Math.cos,
        PI = Math.PI,
        c1 = 1.70158,
        c2 = c1 * 1.525,
        c3 = c1 + 1,
        c4 = (2 * PI) / 3,
        c5 = (2 * PI) / 4.5;

    // x is the fraction of animation progress, in the range 0..1
    function bounceOut(x) {
        var n1 = 7.5625,
            d1 = 2.75;
        if (x < 1 / d1) {
            return n1 * x * x;
        } else if (x < 2 / d1) {
            return n1 * (x -= (1.5 / d1)) * x + .75;
        } else if (x < 2.5 / d1) {
            return n1 * (x -= (2.25 / d1)) * x + .9375;
        } else {
            return n1 * (x -= (2.625 / d1)) * x + .984375;
        }
    }

    $.extend($.easing,
        {
            def: 'easeOutQuad',
            swing: function (x) {
                return $.easing[$.easing.def](x);
            },
            easeInQuad: function (x) {
                return x * x;
            },
            easeOutQuad: function (x) {
                return 1 - (1 - x) * (1 - x);
            },
            easeInOutQuad: function (x) {
                return x < 0.5 ?
                    2 * x * x :
                    1 - pow(-2 * x + 2, 2) / 2;
            },
            easeInCubic: function (x) {
                return x * x * x;
            },
            easeOutCubic: function (x) {
                return 1 - pow(1 - x, 3);
            },
            easeInOutCubic: function (x) {
                return x < 0.5 ?
                    4 * x * x * x :
                    1 - pow(-2 * x + 2, 3) / 2;
            },
            easeInQuart: function (x) {
                return x * x * x * x;
            },
            easeOutQuart: function (x) {
                return 1 - pow(1 - x, 4);
            },
            easeInOutQuart: function (x) {
                return x < 0.5 ?
                    8 * x * x * x * x :
                    1 - pow(-2 * x + 2, 4) / 2;
            },
            easeInQuint: function (x) {
                return x * x * x * x * x;
            },
            easeOutQuint: function (x) {
                return 1 - pow(1 - x, 5);
            },
            easeInOutQuint: function (x) {
                return x < 0.5 ?
                    16 * x * x * x * x * x :
                    1 - pow(-2 * x + 2, 5) / 2;
            },
            easeInSine: function (x) {
                return 1 - cos(x * PI / 2);
            },
            easeOutSine: function (x) {
                return sin(x * PI / 2);
            },
            easeInOutSine: function (x) {
                return -(cos(PI * x) - 1) / 2;
            },
            easeInExpo: function (x) {
                return x === 0 ? 0 : pow(2, 10 * x - 10);
            },
            easeOutExpo: function (x) {
                return x === 1 ? 1 : 1 - pow(2, -10 * x);
            },
            easeInOutExpo: function (x) {
                return x === 0 ? 0 : x === 1 ? 1 : x < 0.5 ?
                    pow(2, 20 * x - 10) / 2 :
                    (2 - pow(2, -20 * x + 10)) / 2;
            },
            easeInCirc: function (x) {
                return 1 - sqrt(1 - pow(x, 2));
            },
            easeOutCirc: function (x) {
                return sqrt(1 - pow(x - 1, 2));
            },
            easeInOutCirc: function (x) {
                return x < 0.5 ?
                    (1 - sqrt(1 - pow(2 * x, 2))) / 2 :
                    (sqrt(1 - pow(-2 * x + 2, 2)) + 1) / 2;
            },
            easeInElastic: function (x) {
                return x === 0 ? 0 : x === 1 ? 1 :
                    -pow(2, 10 * x - 10) * sin((x * 10 - 10.75) * c4);
            },
            easeOutElastic: function (x) {
                return x === 0 ? 0 : x === 1 ? 1 :
                    pow(2, -10 * x) * sin((x * 10 - 0.75) * c4) + 1;
            },
            easeInOutElastic: function (x) {
                return x === 0 ? 0 : x === 1 ? 1 : x < 0.5 ?
                    -(pow(2, 20 * x - 10) * sin((20 * x - 11.125) * c5)) / 2 :
                    pow(2, -20 * x + 10) * sin((20 * x - 11.125) * c5) / 2 + 1;
            },
            easeInBack: function (x) {
                return c3 * x * x * x - c1 * x * x;
            },
            easeOutBack: function (x) {
                return 1 + c3 * pow(x - 1, 3) + c1 * pow(x - 1, 2);
            },
            easeInOutBack: function (x) {
                return x < 0.5 ?
                    (pow(2 * x, 2) * ((c2 + 1) * 2 * x - c2)) / 2 :
                    (pow(2 * x - 2, 2) * ((c2 + 1) * (x * 2 - 2) + c2) + 2) / 2;
            },
            easeInBounce: function (x) {
                return 1 - bounceOut(1 - x);
            },
            easeOutBounce: bounceOut,
            easeInOutBounce: function (x) {
                return x < 0.5 ?
                    (1 - bounceOut(1 - 2 * x)) / 2 :
                    (1 + bounceOut(2 * x - 1)) / 2;
            }
        }
    );
};
