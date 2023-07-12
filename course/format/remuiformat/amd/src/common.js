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
 * Enhancements to Lists components for easy course accessibility.
 *
 * @module     format/remuiformat
 * @copyright  WisdmLabs
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 define(['jquery'], function($) {

 	var SELECTORS = {
 		ACTIVITY_TOGGLE: '.showactivity',
 		ACTIVITY_TOGGLE_CLASS: 'showhideactivity',
 		ACTIVITY_TOGGLE_WRAPPER: '.showactivitywrapper',
 		FIRST_SECTION: '#section-0',
 		SHOW: 'show',
        TOGGLE_HIGHLIGHT: '.dropdown-item.editing_highlight',
		TOGGLE_SHOWHIDE: '.dropdown-item.editing_showhide'
 	};

 	/**
 	 * Get number activities can be shown in first row and hide rest
 	 * @return {Integer} Number activities in first row
 	 */
 	function getActivitiesPerRow() {
 		let width = $(window).width();
 		if ($('.remui-format-list').length) {
	 		if (width >= 992) {
	 			return 4;
	 		}
	 		if (width >= 768) {
	 			return 3;
	 		}
	 		return 2;
 		} else {
 			if (width >= 768) {
	 			return 3;
	 		}
	 		if (width >= 481) {
	 			return 2;
	 		}
	 		return 1;
 		}
 	}

 	/**
 	 * Adjust the general section activities visibility after first row
 	 */
 	function adjustGeneralSectionActivities() {
 		if ($(SELECTORS.FIRST_SECTION + ' .activity').length <= getActivitiesPerRow()) {
 			$(SELECTORS.FIRST_SECTION).removeClass(SELECTORS.ACTIVITY_TOGGLE_CLASS);
 			$(SELECTORS.ACTIVITY_TOGGLE_WRAPPER).hide();
 		} else {
 			$(SELECTORS.ACTIVITY_TOGGLE_WRAPPER).show();
 			$(SELECTORS.FIRST_SECTION).addClass(SELECTORS.ACTIVITY_TOGGLE_CLASS);
 		}
 	}

    function init() {

    	$('#page-course-view-remuiformat .section-modchooser-link').addClass("btn btn-primary");

    	adjustGeneralSectionActivities();
    	$(window).resize(function() {
            adjustGeneralSectionActivities();
        });

        $(SELECTORS.ACTIVITY_TOGGLE).on('click', function() {

	        if($(this).hasClass(SELECTORS.SHOW)) {
	            $(this).html('<i class="fa fa-angle-up" aria-hidden="true"></i>');
	            $(this).toggleClass(SELECTORS.SHOW); //Remove show class
	        } else {
	            $(this).html('<i class="fa fa-angle-down" aria-hidden="true"></i>');
	            $(this).toggleClass(SELECTORS.SHOW); //Add show class
	            $("html, body").animate({
	            	scrollTop: $(SELECTORS.FIRST_SECTION + ' .activity:first-child').offset().top - 66
	            }, "slow");
	        }
	        $(SELECTORS.FIRST_SECTION).toggleClass(SELECTORS.ACTIVITY_TOGGLE_CLASS);
	    });
        $('body').on('click', `${SELECTORS.TOGGLE_HIGHLIGHT}, ${SELECTORS.TOGGLE_SHOWHIDE}`, function() {
            window.reload();
        });

    }

    return {
    	init: init,
    	adjustGeneralSectionActivities: adjustGeneralSectionActivities
    };
});
