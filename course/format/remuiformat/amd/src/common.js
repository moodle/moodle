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
        TOGGLE_HIGHLIGHT: '.section_action_menu .dropdown-item.editing_highlight',
        TOGGLE_SHOWHIDE: '.section_action_menu .dropdown-item.editing_showhide',
        DELETE: '.section_action_menu .dropdown-item.editing_delete',
        BUTTON_HIDE: '.cm_action_menu .dropdown-menu .editing_hide',
        BUTTON_SHOW: '.cm_action_menu .dropdown-menu .editing_show',
        ACTIVITYBUTTON_HIDE: '.cm_action_menu .dropdown-menu [data-action="cmHide"]',
        ACTIVITYBUTTON_SHOW: '.cm_action_menu .dropdown-menu [data-action="cmShow"]',
        ACTIVITYDUPLICATE: '.cm_action_menu .dropdown-item.editing_duplicate',
        ACTIVITIYDELETE: '.cm_action_menu  .dropdown-item.editing_delete'
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
                return 4;
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
    /**
     * Init method
     *
     */
    function init() {

        $('#page-course-view-remuiformat .section-modchooser-link:not(.dropdown-item)').addClass("btn btn-primary");

        adjustGeneralSectionActivities();
        $(window).resize(function() {
            adjustGeneralSectionActivities();
        });

        if ($(".general-section-activities li:last").css('display') == 'none') {
            $(".showactivitywrapper").show();
        } else {
            $(".showactivitywrapper").hide();
        }

        $(SELECTORS.ACTIVITY_TOGGLE).on('click', function() {

            if ($(this).hasClass(SELECTORS.SHOW)) {
                $(this).html(M.util.get_string('showless', 'format_remuiformat'));
                $(this).toggleClass(SELECTORS.SHOW); // Remove show class
            } else {
                $(this).html(M.util.get_string('showmore', 'format_remuiformat'));
                $(this).toggleClass(SELECTORS.SHOW); // Add show class
                $("html, body").animate({
                    scrollTop: $(SELECTORS.FIRST_SECTION + ' .activity:first-child').offset().top - 66
                }, "slow");
            }
            $(SELECTORS.FIRST_SECTION).toggleClass(SELECTORS.ACTIVITY_TOGGLE_CLASS);
        });

        // Handling highlight and show hide dropdown.
        $('body').on('click', `${SELECTORS.TOGGLE_HIGHLIGHT},
                               ${SELECTORS.TOGGLE_SHOWHIDE},
                               ${SELECTORS.BUTTON_HIDE},
                               ${SELECTORS.BUTTON_SHOW},
                               ${SELECTORS.ACTIVITYBUTTON_HIDE},
                               ${SELECTORS.ACTIVITYBUTTON_SHOW}`, function() {
            setTimeout(function() {
                location.reload();
            }, 400);
        });

        // Handling activity duplicate.
        $('body').on('click', `${SELECTORS.ACTIVITYDUPLICATE}`, function() {
            setTimeout(function() {
                location.reload();
            }, 200);
        });

        // Handling deleteAction
        $('body').on('click', `${SELECTORS.ACTIVITIYDELETE},${SELECTORS.DELETE}`, function(event) {
            event.preventDefault();
            if($(this).attr('data-action') == 'cmDelete' ){
                window.location.href = $(this).attr('href');
            }
            if($(this).attr('data-action') == 'deleteSection' ){
                if(moodleversionbranch >= '405'){
                    location.reload();
                }else{
                    window.location.href = $(this).attr('href');
                }
            }
            return true;
        });

        // Handling addSubsection
        $('body').on('click', '[data-action="addModule"]', function(event) {
            setTimeout(() => {
                location.reload();
            }, 200);
            return true;
        });

        var summaryheight = $('.read-more-target').height();

        if (summaryheight > 300) {
            $('.generalsectioninfo').find('#readmorebtn').removeClass('d-none');
            $('.read-more-target').addClass('summary-collapsed').removeClass('summary-expanded');
        }
        $('#readmorebtn').on('click', function() {
            $('.read-more-target').addClass('summary-expanded').removeClass('summary-collapsed');
            $('.generalsectioninfo').find('#readmorebtn').addClass('d-none');
            $('.generalsectioninfo').find('#readlessbtn').removeClass('d-none');
        });
        $('#readlessbtn').on('click', function () {
            $('.read-more-target').addClass('summary-collapsed').removeClass('summary-expanded');
            $('.generalsectioninfo').find('#readmorebtn').removeClass('d-none');
            $('.generalsectioninfo').find('#readlessbtn').addClass('d-none');
        });

    }

    return {
        init: init,
        adjustGeneralSectionActivities: adjustGeneralSectionActivities
    };
});
