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
// Collapse block.
//
// @module     theme_adaptable/collapseblock
// @copyright  2023 G J Barnard.
// @author     G J Barnard -
//               {@link https://moodle.org/user/profile.php?id=442195}
//               {@link https://gjbarnard.co.uk}
// @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
//

import $ from 'jquery';
import * as AdaptableUtil from 'theme_adaptable/util';
import log from 'core/log';

/**
 * Collapse Block class.
 */
class CollapseBlock {
    static noInit = true;

    static collapseBlock() {
        if (CollapseBlock.noInit) {
            CollapseBlock.noInit = false;
            $('.block-collapsible').click(function() {
                var instanceId = $(this).data('instanceid');
                var blockInstance = $('#inst' + instanceId);

                $('#inst' + instanceId + ' .content').slideToggle('slow', function() {
                    if (blockInstance.hasClass('hidden')) {
                        blockInstance.removeClass('hidden');
                        AdaptableUtil.setUserPreference('block' + instanceId + 'hidden', 0);
                    } else {
                        blockInstance.addClass('hidden');
                        AdaptableUtil.setUserPreference('block' + instanceId + 'hidden', 1);
                    }
                });
            });
            log.debug('Adaptable Collapse Block ES6 collapseBlock');
        }
    }
}

/**
 * Init.
 */
export const collapseBlockInit = () => {
    if (document.readyState !== 'loading') {
        CollapseBlock.collapseBlock();
    } else {
        document.addEventListener('DOMContentLoaded', function () {
            CollapseBlock.collapseBlock();
        });
    }
};
