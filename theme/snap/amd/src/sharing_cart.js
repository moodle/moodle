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
 *  Sharing Cart
 *
 *  @package
 *  @copyright  Copyright (c) 2020 Open LMS (https://www.openlms.net)
 *  @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import $ from 'jquery';

export default class SharingCartForSnap {
    constructor(courseSections) {
        this.snapSpinner = '';
        this.courseSections = courseSections;
    }

    /**
     * Get a string from moodle
     * @param {String} identifier
     * @returns {String}
     */
    str = (identifier) => {
        return M.str.block_sharing_cart[identifier] || M.str.moodle[identifier];
    };
    /**
     * Sets a create command pointer
     * @param {string} create_command
     */
    setCreateCommand = (create_command) => {
        this.create_command = create_command;
    };
    /**
     *  Create a custom command icon
     *  @param {String} cssClass The css class for the icon
     *  @param {String} title The title and tooltip for the icon
     *  @param {String} imageUrl Image URL for the icon
     */
    create_special_activity_command = (cssClass, title, imageUrl) => {
        return $('<a href="javascript:void(0)"/>')
            .addClass(cssClass)
            .addClass('dropdown-item menu-action cm-edit-action')
            .attr('title', title)
            .append(
                $('<img class="icon"/>')
                    .attr('alt', title)
                    .attr('src', imageUrl)
            );
    };
    /**
     * Adds a spinner when necessary
     * @param {function} spinner
     */
    addSpinner = (spinner) => {
        if (this.snapSpinner) {
            this.snapSpinner.show();
        } else {
            this.snapSpinner = spinner;
        }
    };
    /**
     * Hides the spinner if exist
     */
    hideSpinner = () => {
        if (this.snapSpinner) {
            this.snapSpinner.hide();
        }
    };
    /**
     * Creates a custom modal when restoring a sharing cart activity/module
     * @param {Object} input
     */
    onRestore = (input) => {
        const restore_targets = input.restore_targets;
        const course = input.course;
        const param = input.param;
        const get_action_url = input.get_action_url;
        const ModalFactory = input.ModalFactory;
        const ModalEvents = input.ModalEvents;
        const id = input.id;

        var sectionsURLs = [];
        this.courseSections.forEach(function(section) {
            var urlArray = {
                'directory': restore_targets.is_directory,
                'target': id,
                'course'   : course.id,
                'section'  : section.num,
                'sesskey'  : M.cfg.sesskey
            };
            urlArray[param] = id;
            var url = get_action_url('restore', urlArray);

            var sectionName = null;
            if (section.name === null) {
                $('#chapters').find('.chapter-title').each(function() {
                    var currSection = $(this).attr('href');
                    if (currSection == '#section-'+section.num) {
                        sectionName = $(this).text();
                    }
                });
            } else {
                sectionName = section.name;
            }
            var sectionURL = {
                name : sectionName,
                url : url
            };
            sectionsURLs.push(sectionURL);
        });

        ModalFactory.create({
            type: ModalFactory.types.SAVE_CANCEL,
            title: M.str.block_sharing_cart['restore'],
            body: ((sections) => {
                var s = M.str.block_sharing_cart['snap_dialog_restore'];
                // Create Select element.
                s += '<select id="select-dialog" class="custom-select">';
                for(var i = 0; i < sections.length; i++) {
                    s += '<option value="' + sections[i].url + '">' + sections[i].name + '</option>';
                }
                s += '</select> <br> <br>';
                return s;
            })(sectionsURLs)
        })
            .done(function(modal) {
                modal.show();
                modal.getRoot().on(ModalEvents.save, function() {
                    window.location.href = $('#select-dialog').val();
                });
                modal.getRoot().on(ModalEvents.cancel, function() {
                    modal.destroy();
                });
            });
    };

    /**
     *
     * @param {node} $activity
     * @param {object} iconBackup
     * @param {function} on_backup
     */
    add_backup_command = ($activity, iconBackup, on_backup) => {
        var $menu = $activity.find("ul[role='menu']");

        if($menu.length)
        {
            var li = $menu.find('li').first().clone();
            var $backup = li.find('a').attr('title', this.str('backup'))
                .attr('href', 'javascript:void(0)'); //eslint-disable-line no-script-url
            var img = li.find('img');

            if (img.length) {
                li.find('img')
                    .attr('alt', this.str('backup'))
                    .attr('title', this.str('backup'))
                    .attr('src', M.util.image_url(iconBackup.pix));
            } else {
                li.find('i')
                    .attr('class', 'icon fa fa-upload')
                    .attr('title', this.str('backup'))
                    .attr('aria-label', this.str('backup'));
            }

            li.find('span').html(this.str('backup'));
            $menu.append(li);
        }
        else
        {
            var $backup = this.create_command("backup");
            if ($('#page-course-view-tiles').length) {
                $menu = $activity.find('div[role="menu"]');
            } else {
                $menu = $activity.find('div.snap-edit-more-dropdown ul.dropdown-menu');
            }
            if($menu.length)
            {
                const cssClass = iconBackup.css;
                const title = this.str('backup');
                const imageUrl =  M.util.image_url(iconBackup.pix);
                $backup = this.create_special_activity_command(cssClass, title, imageUrl);

                if($menu.css("display") === "none")
                {
                    var $button = $menu.find('.editing_backup');
                    if ($button.length == 0) {
                        $menu.append($backup);
                        $backup.append($("<span class='menu-action-text'/>").append($backup.attr('title')));
                    }
                }
            }
            else
            {
                $activity.find(".commands").append($backup);
            }
        }
        // Get activity name
        var activityClass = $activity[0].className;
        var modtype = activityClass.substr(activityClass.indexOf('modtype_') + 8);
        var activityName = this.str('activity_string');
        if (modtype !== 'label') {
            activityName = $('.activity#' + $activity[0].id)
                .find('.activityinstance p.instancename')
                .html();
        }

        $backup.click(function(e)
        {
            on_backup(e, activityName);
        });
    };

    snapFix = (input) => {
        const course = input.course;
        const iconBackup = input.iconBackup;
        const on_section_backup = input.on_section_backup;
        const on_backup = input.on_backup;
        const _this = this;

        if(course.is_frontpage)
        {
            if($('.sitetopic li.activity').length > 0)
            {
                var valid = $('.sitetopic li.activity').data('block-sharing-cart');
                if(valid !== 'done')
                {
                    $('.sitetopic li.activity').each(function()
                    {
                        _this.add_backup_command($(this), iconBackup, on_backup);
                    });
                    $('.sitetopic li.activity').data("block-sharing-cart", "done");
                }
            }
            if($('.block_site_main_menu').length > 0)
            {
                var valid = $('.block_site_main_menu .content > ul >  li').data('block-sharing-cart');
                if(valid !== 'done')
                {
                    $('.block_site_main_menu .content > ul >  li').each(function()
                    {
                        _this.add_backup_command($(this), iconBackup, on_backup);
                    });
                    $('.block_site_main_menu .content > ul >  li').data("block-sharing-cart", "done");
                }
            }
        }
        else
        {
            if($('.course-content li.activity').length > 0)
            {
                var valid = $('.course-content li.activity').data('block-sharing-cart');
                if(valid !== 'done' || this.courseSections.length == 1)
                {
                    $('.course-content li.activity').each(function()
                    {
                        _this.add_backup_command($(this), iconBackup, on_backup);
                    });
                    $('.course-content li.activity').data("block-sharing-cart", "done");
                }
            }
        }

        if (M.cfg.theme !== 'snap') {
            let _this = this;
            $("li.section").each(function () {
                var sectionID = $(this).find("div.content h3.sectionname span.inplaceeditable").attr("data-itemid");

                var $menu = $(this).find("ul[role='menu']").first();

                if ($menu.length){
                    var li = $menu.find('li').first().clone();
                    var img = li.find('img');

                    if (img.length) {
                        img.attr('alt', _this.str('backup'))
                            .attr('title', _this.str('backup'))
                            .attr('src', M.util.image_url(iconBackup.pix, null));
                    } else {
                        li.find('i')
                            .attr('class', 'icon fa fa-upload')
                            .attr('title', _this.str('backup'))
                            .attr('aria-label', _this.str('backup'));
                    }

                    li.find('span').html(_this.str('backup'));
                    li.find('a').attr('href', 'javascript:void(0)'); //eslint-disable-line no-script-url

                    $menu.append(li);

                    li.find('a').click(function () {
                        on_section_backup(sectionID);
                    });
                }
                else {
                    $menu = $(this).find("div[role='menu']").first();

                    var $backup = null;

                    if ($menu.length) {
                        const cssClass = iconBackup.css;
                        const title = _this.str('backup');
                        const imageUrl =  M.util.image_url(iconBackup.pix);
                        $backup = _this.create_special_activity_command(cssClass, title, imageUrl);
                        $menu.append($backup.attr("role", "menuitem"));

                        if ($menu.css("display") === "none") {
                            $backup.append($("<span class='menu-action-text'/>").append($backup.attr('title')));
                        }
                    }
                    else {
                        $backup = _this.create_command("backup");
                        $(this).find(".commands").append($backup);
                    }

                    $backup.click(function () {
                        on_section_backup(sectionID);
                    });
                }
            });
        } else {
            $.each($("a.snap-delete"), function(i, val)
            {
                var sectionID = $(val).attr("href").split('?')[1].split('&')[0].split('=')[1];
                var $link = $('<a/>').attr('class', 'snap-sharing-cart').attr('role', 'button').attr('id', sectionID);
                $link.attr('tabindex', '0').attr('title', 'Backup Section');
                var $button = $(val).parent().find('a.snap-sharing-cart');
                if ($button.length == 0) {
                    $(val).parent().append($link);
                }
            });

            $('a.snap-sharing-cart').unbind().click(function () {
                const sectionId = $(this).attr('id');
                const section = _this.courseSections ? _this.courseSections.find(section => section['id'] === sectionId)
                    : {num: 0, name: undefined};
                const sectionNumber = section['num'];
                const courseId = course.id;
                const sectionName = section['name'] === null ? undefined : section['name'];
                on_section_backup(sectionId, sectionNumber, courseId, sectionName);
            });
        }
    };
}