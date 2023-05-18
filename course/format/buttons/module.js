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
 * format_buttons_renderer
 *
 * @package    format_buttons
 * @author     Rodrigo Brandão <https://www.linkedin.com/in/brandaorodrigo>
 * @copyright  2020 Rodrigo Brandão <rodrigo.brandao.contato@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.format_buttons = M.format_buttons || {
    ourYUI: null,
    numsections: 0
};

M.format_buttons.init = function(Y, numsections, currentsection, courseid) {
    this.ourYUI = Y;
    this.numsections = parseInt(numsections);
    document.getElementById('buttonsectioncontainer').style.display = 'table';

    var findHash = function (href) {
        var id = null;
        if (href.indexOf('#section-') !== 0) {
            var split = href.split('#section-');
            id = split[1];
        }
        return id;
    };

    var hash = findHash(window.location.href);
    if (hash) {
        currentsection = hash;
    }

    if (currentsection) {
        M.format_buttons.show(currentsection, courseid);
    }

    Y.delegate('click', function (e) {
        var href = e.currentTarget.get('href');
        currentsection = findHash(href);
        M.format_buttons.show(currentsection, courseid)
    }, '[data-region="drawer"]', '[data-type="30"]');

};

M.format_buttons.hide = function() {
    for (var i = 1; i <= this.numsections; i++) {
        if (document.getElementById('buttonsection-' + i) != undefined) {
            var buttonsection = document.getElementById('buttonsection-' + i);
            buttonsection.setAttribute('class', buttonsection.getAttribute('class').replace('sectionvisible', ''));
            document.getElementById('section-' + i).style.display = 'none';
        }
    }
};

M.format_buttons.show = function(id, courseid) {
    this.hide();
    if (id > 0) {
        var buttonsection = document.getElementById('buttonsection-' + id);
        var currentsection = document.getElementById('section-' + id);
        if (buttonsection && currentsection) {
            buttonsection.setAttribute('class', buttonsection.getAttribute('class') + ' sectionvisible');
            currentsection.style.display = 'block';
            document.cookie = 'sectionvisible_' + courseid + '=' + id + '; path=/';
            M.format_buttons.h5p();
        }
    }
};

M.format_buttons.h5p = function() {
    window.h5pResizerInitialized = false;
    var iframes = document.getElementsByTagName('iframe');
    var ready = {
        context: 'h5p',
        action: 'ready'
    };
    for (var i = 0; i < iframes.length; i++) {
        if (iframes[i].src.indexOf('h5p') !== -1) {
            iframes[i].contentWindow.postMessage(ready, '*');
        }
    }
};
