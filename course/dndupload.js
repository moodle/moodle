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
 * Javascript library for enableing a drag and drop upload to courses
 *
 * @package    core
 * @subpackage course
 * @copyright  2012 Davo Smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
M.course_dndupload = {
    // YUI object.
    Y: null,
    // URL for upload requests
    url: M.cfg.wwwroot + '/course/dndupload.php',
    // maximum size of files allowed in this form
    maxbytes: 0,
    // ID of the course we are on
    courseid: null,
    // Data about the different file/data handlers that are available
    handlers: null,
    // Nasty hack to distinguish between dragenter(first entry),
    // dragenter+dragleave(moving between child elements) and dragleave (leaving element)
    entercount: 0,
    // Used to keep track of the section we are dragging across - to make
    // spotting movement between sections more reliable
    currentsection: null,
    // Used to store the pending uploads whilst the user is being asked for further input
    uploadqueue: null,
    // True if the there is currently a dialog being shown (asking for a name, or giving a
    // choice of file handlers)
    uploaddialog: false,
    // An array containing the last selected file handler for each file type
    lastselected: null,

    // The following are used to identify specific parts of the course page

    // The type of HTML element that is a course section
    sectiontypename: 'li',
    // The classes that an element must have to be identified as a course section
    sectionclasses: ['section', 'main'],
    // The ID of the main content area of the page (for adding the 'status' div)
    pagecontentid: 'page',
    // The selector identifying the list of modules within a section (note changing this may require
    // changes to the get_mods_element function)
    modslistselector: 'ul.section',

    /**
     * Initalise the drag and drop upload interface
     * Note: one and only one of options.filemanager and options.formcallback must be defined
     *
     * @param Y the YUI object
     * @param object options {
     *            courseid: ID of the course we are on
     *            maxbytes: maximum size of files allowed in this form
     *            handlers: Data about the different file/data handlers that are available
     *          }
     */
    init: function(Y, options) {
        this.Y = Y;

        if (!this.browser_supported()) {
            return; // Browser does not support the required functionality
        }

        this.maxbytes = options.maxbytes;
        this.courseid = options.courseid;
        this.handlers = options.handlers;
        this.uploadqueue = new Array();
        this.lastselected = new Array();

        var sectionselector = this.sectiontypename + '.' + this.sectionclasses.join('.');
        var sections = this.Y.all(sectionselector);
        if (sections.isEmpty()) {
            return; // No sections - incompatible course format or front page.
        }
        sections.each( function(el) {
            this.add_preview_element(el);
            this.init_events(el);
        }, this);

        if (options.showstatus) {
            this.add_status_div();
        }
    },

    /**
     * Add a div element to tell the user that drag and drop upload
     * is available (or to explain why it is not available)
     */
    add_status_div: function() {
        var Y = this.Y,
            coursecontents = Y.one('#' + this.pagecontentid),
            div,
            handlefile = (this.handlers.filehandlers.length > 0),
            handletext = false,
            handlelink = false,
            i = 0,
            styletop,
            styletopunit;

        if (!coursecontents) {
            return;
        }

        div = Y.Node.create('<div id="dndupload-status"></div>').setStyle('opacity', '0.0');
        coursecontents.insert(div, 0);

        for (i = 0; i < this.handlers.types.length; i++) {
            switch (this.handlers.types[i].identifier) {
                case 'text':
                case 'text/html':
                    handletext = true;
                    break;
                case 'url':
                    handlelink = true;
                    break;
            }
        }
        $msgident = 'dndworking';
        if (handlefile) {
            $msgident += 'file';
        }
        if (handletext) {
            $msgident += 'text';
        }
        if (handlelink) {
            $msgident += 'link';
        }
        div.setContent(M.util.get_string($msgident, 'moodle'));

        styletop = div.getStyle('top') || '0px';
        styletopunit = styletop.replace(/^\d+/, '');
        styletop = parseInt(styletop.replace(/\D*$/, ''), 10);

        var fadein = new Y.Anim({
            node: '#dndupload-status',
            from: {
                opacity: 0.0,
                top: (styletop - 30).toString() + styletopunit
            },

            to: {
                opacity: 1.0,
                top: styletop.toString() + styletopunit
            },
            duration: 0.5
        });

        var fadeout = new Y.Anim({
            node: '#dndupload-status',
            from: {
                opacity: 1.0,
                top: styletop.toString() + styletopunit
            },

            to: {
                opacity: 0.0,
                top: (styletop - 30).toString() + styletopunit
            },
            duration: 0.5
        });

        fadein.run();
        fadein.on('end', function(e) {
            Y.later(3000, this, function() {
                fadeout.run();
            });
        });

        fadeout.on('end', function(e) {
            Y.one('#dndupload-status').remove(true);
        });
    },

    /**
     * Check the browser has the required functionality
     * @return true if browser supports drag/drop upload
     */
    browser_supported: function() {
        if (typeof FileReader == 'undefined') {
            return false;
        }
        if (typeof FormData == 'undefined') {
            return false;
        }
        return true;
    },

    /**
     * Initialise drag events on node container, all events need
     * to be processed for drag and drop to work
     * @param el the element to add events to
     */
    init_events: function(el) {
        this.Y.on('dragenter', this.drag_enter, el, this);
        this.Y.on('dragleave', this.drag_leave, el, this);
        this.Y.on('dragover',  this.drag_over,  el, this);
        this.Y.on('drop',      this.drop,       el, this);
    },

    /**
     * Work out which course section a given element is in
     * @param el the child DOM element within the section
     * @return the DOM element representing the section
     */
    get_section: function(el) {
        var sectionclasses = this.sectionclasses;
        return el.ancestor( function(test) {
            var i;
            for (i=0; i<sectionclasses.length; i++) {
                if (!test.hasClass(sectionclasses[i])) {
                    return false;
                }
                return true;
            }
        }, true);
    },

    /**
     * Work out the number of the section we have been dropped on to, from the section element
     * @param DOMElement section the selected section
     * @return int the section number
     */
    get_section_number: function(section) {
        var sectionid = section.get('id').split('-');
        if (sectionid.length < 2 || sectionid[0] != 'section') {
            return false;
        }
        return parseInt(sectionid[1]);
    },

    /**
     * Check if the event includes data of the given type
     * @param e the event details
     * @param type the data type to check for
     * @return true if the data type is found in the event data
     */
    types_includes: function(e, type) {
        var i;
        var types = e._event.dataTransfer.types;
        type = type.toLowerCase();
        for (i=0; i<types.length; i++) {
            if (!types.hasOwnProperty(i)) {
                continue;
            }
            if (types[i].toLowerCase() === type) {
                return true;
            }
        }
        return false;
    },

    /**
     * Look through the event data, checking it against the registered data types
     * (in order of priority) and return details of the first matching data type
     * @param e the event details
     * @return object|false - false if not found or an object {
     *           realtype: the type as given by the browser
     *           addmessage: the message to show to the user during dragging
     *           namemessage: the message for requesting a name for the resource from the user
     *           type: the identifier of the type (may match several 'realtype's)
     *           }
     */
    drag_type: function(e) {
        // Check there is some data attached.
        if (e._event.dataTransfer === null) {
            return false;
        }
        if (e._event.dataTransfer.types === null) {
            return false;
        }
        if (e._event.dataTransfer.types.length == 0) {
            return false;
        }

        // Check for files first.
        if (this.types_includes(e, 'Files')) {
            if (e.type != 'drop' || e._event.dataTransfer.files.length != 0) {
                if (this.handlers.filehandlers.length == 0) {
                    return false; // No available file handlers - ignore this drag.
                }
                return {
                    realtype: 'Files',
                    addmessage: M.util.get_string('addfilehere', 'moodle'),
                    namemessage: null, // Should not be asked for anyway
                    type: 'Files'
                };
            }
        }

        // Check each of the registered types.
        var types = this.handlers.types;
        for (var i=0; i<types.length; i++) {
            // Check each of the different identifiers for this type
            var dttypes = types[i].datatransfertypes;
            for (var j=0; j<dttypes.length; j++) {
                if (this.types_includes(e, dttypes[j])) {
                    return {
                        realtype: dttypes[j],
                        addmessage: types[i].addmessage,
                        namemessage: types[i].namemessage,
                        handlermessage: types[i].handlermessage,
                        type: types[i].identifier,
                        handlers: types[i].handlers
                    };
                }
            }
        }
        return false; // No types we can handle
    },

    /**
     * Check the content of the drag/drop includes a type we can handle, then, if
     * it is, notify the browser that we want to handle it
     * @param event e
     * @return string type of the event or false
     */
    check_drag: function(e) {
        var type = this.drag_type(e);
        if (type) {
            // Notify browser that we will handle this drag/drop
            e.stopPropagation();
            e.preventDefault();
        }
        return type;
    },

    /**
     * Handle a dragenter event: add a suitable 'add here' message
     * when a drag event occurs, containing a registered data type
     * @param e event data
     * @return false to prevent the event from continuing to be processed
     */
    drag_enter: function(e) {
        if (!(type = this.check_drag(e))) {
            return false;
        }

        var section = this.get_section(e.currentTarget);
        if (!section) {
            return false;
        }

        if (this.currentsection && this.currentsection != section) {
            this.currentsection = section;
            this.entercount = 1;
        } else {
            this.entercount++;
            if (this.entercount > 2) {
                this.entercount = 2;
                return false;
            }
        }

        this.show_preview_element(section, type);

        return false;
    },

    /**
     * Handle a dragleave event: remove the 'add here' message (if present)
     * @param e event data
     * @return false to prevent the event from continuing to be processed
     */
    drag_leave: function(e) {
        if (!this.check_drag(e)) {
            return false;
        }

        this.entercount--;
        if (this.entercount == 1) {
            return false;
        }
        this.entercount = 0;
        this.currentsection = null;

        this.hide_preview_element();
        return false;
    },

    /**
     * Handle a dragover event: just prevent the browser default (necessary
     * to allow drag and drop handling to work)
     * @param e event data
     * @return false to prevent the event from continuing to be processed
     */
    drag_over: function(e) {
        this.check_drag(e);
        return false;
    },

    /**
     * Handle a drop event: hide the 'add here' message, check the attached
     * data type and start the upload process
     * @param e event data
     * @return false to prevent the event from continuing to be processed
     */
    drop: function(e) {
        if (!(type = this.check_drag(e))) {
            return false;
        }

        this.hide_preview_element();

        // Work out the number of the section we are on (from its id)
        var section = this.get_section(e.currentTarget);
        var sectionnumber = this.get_section_number(section);

        // Process the file or the included data
        if (type.type == 'Files') {
            var files = e._event.dataTransfer.files;
            for (var i=0, f; f=files[i]; i++) {
                this.handle_file(f, section, sectionnumber);
            }
        } else {
            var contents = e._event.dataTransfer.getData(type.realtype);
            if (contents) {
                this.handle_item(type, contents, section, sectionnumber);
            }
        }

        return false;
    },

    /**
     * Find or create the 'ul' element that contains all of the module
     * instances in this section
     * @param section the DOM element representing the section
     * @return false to prevent the event from continuing to be processed
     */
    get_mods_element: function(section) {
        // Find the 'ul' containing the list of mods
        var modsel = section.one(this.modslistselector);
        if (!modsel) {
            // Create the above 'ul' if it doesn't exist
            modsel = document.createElement('ul');
            modsel.className = 'section img-text';
            var contentel = section.get('children').pop();
            var brel = contentel.get('children').pop();
            contentel.insertBefore(modsel, brel);
            modsel = this.Y.one(modsel);
        }

        return modsel;
    },

    /**
     * Add a new dummy item to the list of mods, to be replaced by a real
     * item & link once the AJAX upload call has completed
     * @param name the label to show in the element
     * @param section the DOM element reperesenting the course section
     * @return DOM element containing the new item
     */
    add_resource_element: function(name, section, module) {
        var modsel = this.get_mods_element(section);

        var resel = {
            parent: modsel,
            li: document.createElement('li'),
            div: document.createElement('div'),
            indentdiv: document.createElement('div'),
            a: document.createElement('a'),
            icon: document.createElement('img'),
            namespan: document.createElement('span'),
            groupingspan: document.createElement('span'),
            progressouter: document.createElement('span'),
            progress: document.createElement('span')
        };

        resel.li.className = 'activity ' + module + ' modtype_' + module;

        resel.indentdiv.className = 'mod-indent';
        resel.li.appendChild(resel.indentdiv);

        resel.div.className = 'activityinstance';
        resel.indentdiv.appendChild(resel.div);

        resel.a.href = '#';
        resel.div.appendChild(resel.a);

        resel.icon.src = M.util.image_url('i/ajaxloader');
        resel.icon.className = 'activityicon iconlarge';
        resel.a.appendChild(resel.icon);

        resel.namespan.className = 'instancename';
        resel.namespan.innerHTML = name;
        resel.a.appendChild(resel.namespan);

        resel.groupingspan.className = 'groupinglabel';
        resel.div.appendChild(resel.groupingspan);

        resel.progressouter.className = 'dndupload-progress-outer';
        resel.progress.className = 'dndupload-progress-inner';
        resel.progress.innerHTML = '&nbsp;';
        resel.progressouter.appendChild(resel.progress);
        resel.div.appendChild(resel.progressouter);

        modsel.insertBefore(resel.li, modsel.get('children').pop()); // Leave the 'preview element' at the bottom

        return resel;
    },

    /**
     * Hide any visible dndupload-preview elements on the page
     */
    hide_preview_element: function() {
        this.Y.all('li.dndupload-preview').addClass('dndupload-hidden');
        this.Y.all('.dndupload-over').removeClass('dndupload-over');
    },

    /**
     * Unhide the preview element for the given section and set it to display
     * the correct message
     * @param section the YUI node representing the selected course section
     * @param type the details of the data type detected in the drag (including the message to display)
     */
    show_preview_element: function(section, type) {
        this.hide_preview_element();
        var preview = section.one('li.dndupload-preview').removeClass('dndupload-hidden');
        section.addClass('dndupload-over');

        // Horrible work-around to allow the 'Add X here' text to be a drop target in Firefox.
        var node = preview.one('span').getDOMNode();
        node.firstChild.nodeValue = type.addmessage;
    },

    /**
     * Add the preview element to a course section. Note: this needs to be done before 'addEventListener'
     * is called, otherwise Firefox will ignore events generated when the mouse is over the preview
     * element (instead of passing them up to the parent element)
     * @param section the YUI node representing the selected course section
     */
    add_preview_element: function(section) {
        var modsel = this.get_mods_element(section);
        var preview = {
            li: document.createElement('li'),
            div: document.createElement('div'),
            icon: document.createElement('img'),
            namespan: document.createElement('span')
        };

        preview.li.className = 'dndupload-preview dndupload-hidden';

        preview.div.className = 'mod-indent';
        preview.li.appendChild(preview.div);

        preview.icon.src = M.util.image_url('t/addfile');
        preview.icon.className = 'icon';
        preview.div.appendChild(preview.icon);

        preview.div.appendChild(document.createTextNode(' '));

        preview.namespan.className = 'instancename';
        preview.namespan.innerHTML = M.util.get_string('addfilehere', 'moodle');
        preview.div.appendChild(preview.namespan);

        modsel.appendChild(preview.li);
    },

    /**
     * Find the registered handler for the given file type. If there is more than one, ask the
     * user which one to use. Then upload the file to the server
     * @param file the details of the file, taken from the FileList in the drop event
     * @param section the DOM element representing the selected course section
     * @param sectionnumber the number of the selected course section
     */
    handle_file: function(file, section, sectionnumber) {
        var handlers = new Array();
        var filehandlers = this.handlers.filehandlers;
        var extension = '';
        var dotpos = file.name.lastIndexOf('.');
        if (dotpos != -1) {
            extension = file.name.substr(dotpos+1, file.name.length).toLowerCase();
        }

        for (var i=0; i<filehandlers.length; i++) {
            if (filehandlers[i].extension == '*' || filehandlers[i].extension == extension) {
                handlers.push(filehandlers[i]);
            }
        }

        if (handlers.length == 0) {
            // No handlers at all (not even 'resource'?)
            return;
        }

        if (handlers.length == 1) {
            this.upload_file(file, section, sectionnumber, handlers[0].module);
            return;
        }

        this.file_handler_dialog(handlers, extension, file, section, sectionnumber);
    },

    /**
     * Show a dialog box, allowing the user to choose what to do with the file they are uploading
     * @param handlers the available handlers to choose between
     * @param extension the extension of the file being uploaded
     * @param file the File object being uploaded
     * @param section the DOM element of the section being uploaded to
     * @param sectionnumber the number of the selected course section
     */
    file_handler_dialog: function(handlers, extension, file, section, sectionnumber) {
        if (this.uploaddialog) {
            var details = new Object();
            details.isfile = true;
            details.handlers = handlers;
            details.extension = extension;
            details.file = file;
            details.section = section;
            details.sectionnumber = sectionnumber;
            this.uploadqueue.push(details);
            return;
        }
        this.uploaddialog = true;

        var timestamp = new Date().getTime();
        var uploadid = Math.round(Math.random()*100000)+'-'+timestamp;
        var content = '';
        var sel;
        if (extension in this.lastselected) {
            sel = this.lastselected[extension];
        } else {
            sel = handlers[0].module;
        }
        content += '<p>'+M.util.get_string('actionchoice', 'moodle', file.name)+'</p>';
        content += '<div id="dndupload_handlers'+uploadid+'">';
        for (var i=0; i<handlers.length; i++) {
            var id = 'dndupload_handler'+uploadid+handlers[i].module;
            var checked = (handlers[i].module == sel) ? 'checked="checked" ' : '';
            content += '<input type="radio" name="handler" value="'+handlers[i].module+'" id="'+id+'" '+checked+'/>';
            content += ' <label for="'+id+'">';
            content += handlers[i].message;
            content += '</label><br/>';
        }
        content += '</div>';

        var Y = this.Y;
        var self = this;
        var panel = new M.core.dialogue({
            bodyContent: content,
            width: '350px',
            modal: true,
            visible: false,
            render: true,
            align: {
                node: null,
                points: [Y.WidgetPositionAlign.CC, Y.WidgetPositionAlign.CC]
            }
        });
        panel.show();
        // When the panel is hidden - destroy it and then check for other pending uploads
        panel.after("visibleChange", function(e) {
            if (!panel.get('visible')) {
                panel.destroy(true);
                self.check_upload_queue();
            }
        });

        // Add the submit/cancel buttons to the bottom of the dialog.
        panel.addButton({
            label: M.util.get_string('upload', 'moodle'),
            action: function(e) {
                e.preventDefault();
                // Find out which module was selected
                var module = false;
                var div = Y.one('#dndupload_handlers'+uploadid);
                div.all('input').each(function(input) {
                    if (input.get('checked')) {
                        module = input.get('value');
                    }
                });
                if (!module) {
                    return;
                }
                panel.hide();
                // Remember this selection for next time
                self.lastselected[extension] = module;
                // Do the upload
                self.upload_file(file, section, sectionnumber, module);
            },
            section: Y.WidgetStdMod.FOOTER
        });
        panel.addButton({
            label: M.util.get_string('cancel', 'moodle'),
            action: function(e) {
                e.preventDefault();
                panel.hide();
            },
            section: Y.WidgetStdMod.FOOTER
        });
    },

    /**
     * Check to see if there are any other dialog boxes to show, now that the current one has
     * been dealt with
     */
    check_upload_queue: function() {
        this.uploaddialog = false;
        if (this.uploadqueue.length == 0) {
            return;
        }

        var details = this.uploadqueue.shift();
        if (details.isfile) {
            this.file_handler_dialog(details.handlers, details.extension, details.file, details.section, details.sectionnumber);
        } else {
            this.handle_item(details.type, details.contents, details.section, details.sectionnumber);
        }
    },

    /**
     * Do the file upload: show the dummy element, use an AJAX call to send the data
     * to the server, update the progress bar for the file, then replace the dummy
     * element with the real information once the AJAX call completes
     * @param file the details of the file, taken from the FileList in the drop event
     * @param section the DOM element representing the selected course section
     * @param sectionnumber the number of the selected course section
     */
    upload_file: function(file, section, sectionnumber, module) {

        // This would be an ideal place to use the Y.io function
        // however, this does not support data encoded using the
        // FormData object, which is needed to transfer data from
        // the DataTransfer object into an XMLHTTPRequest
        // This can be converted when the YUI issue has been integrated:
        // http://yuilibrary.com/projects/yui3/ticket/2531274
        var xhr = new XMLHttpRequest();
        var self = this;

        if (file.size > this.maxbytes) {
            new M.core.alert({
                message: "'" + file.name + "' " + M.util.get_string('filetoolarge', 'moodle')
            });
            return;
        }

        // Add the file to the display
        var resel = this.add_resource_element(file.name, section, module);

        // Update the progress bar as the file is uploaded
        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                var percentage = Math.round((e.loaded * 100) / e.total);
                resel.progress.style.width = percentage + '%';
            }
        }, false);

        // Wait for the AJAX call to complete, then update the
        // dummy element with the returned details
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    var result = JSON.parse(xhr.responseText);
                    if (result) {
                        if (result.error == 0) {
                            // All OK - replace the dummy element.
                            resel.li.outerHTML = result.fullcontent;
                            if (self.Y.UA.gecko > 0) {
                                // Fix a Firefox bug which makes sites with a '~' in their wwwroot
                                // log the user out when clicking on the link (before refreshing the page).
                                resel.li.outerHTML = unescape(resel.li.outerHTML);
                            }
                            self.add_editing(result.elementid);
                        } else {
                            // Error - remove the dummy element
                            resel.parent.removeChild(resel.li);
                            new M.core.alert({message: result.error});
                        }
                    }
                } else {
                    new M.core.alert({message: M.util.get_string('servererror', 'moodle')});
                }
            }
        };

        // Prepare the data to send
        var formData = new FormData();
        formData.append('repo_upload_file', file);
        formData.append('sesskey', M.cfg.sesskey);
        formData.append('course', this.courseid);
        formData.append('section', sectionnumber);
        formData.append('module', module);
        formData.append('type', 'Files');

        // Send the AJAX call
        xhr.open("POST", this.url, true);
        xhr.send(formData);
    },

    /**
     * Show a dialog box to gather the name of the resource / activity to be created
     * from the uploaded content
     * @param type the details of the type of content
     * @param contents the contents to be uploaded
     * @section the DOM element for the section being uploaded to
     * @sectionnumber the number of the section being uploaded to
     */
    handle_item: function(type, contents, section, sectionnumber) {
        if (type.handlers.length == 0) {
            // Nothing to handle this - should not have got here
            return;
        }

        if (type.handlers.length == 1 && type.handlers[0].noname) {
            // Only one handler and it doesn't need a name (i.e. a label).
            this.upload_item('', type.type, contents, section, sectionnumber, type.handlers[0].module);
            this.check_upload_queue();
            return;
        }

        if (this.uploaddialog) {
            var details = new Object();
            details.isfile = false;
            details.type = type;
            details.contents = contents;
            details.section = section;
            details.setcionnumber = sectionnumber;
            this.uploadqueue.push(details);
            return;
        }
        this.uploaddialog = true;

        var timestamp = new Date().getTime();
        var uploadid = Math.round(Math.random()*100000)+'-'+timestamp;
        var nameid = 'dndupload_handler_name'+uploadid;
        var content = '';
        if (type.handlers.length > 1) {
            content += '<p>'+type.handlermessage+'</p>';
            content += '<div id="dndupload_handlers'+uploadid+'">';
            var sel = type.handlers[0].module;
            for (var i=0; i<type.handlers.length; i++) {
                var id = 'dndupload_handler'+uploadid+type.handlers[i].module;
                var checked = (type.handlers[i].module == sel) ? 'checked="checked" ' : '';
                content += '<input type="radio" name="handler" value="'+i+'" id="'+id+'" '+checked+'/>';
                content += ' <label for="'+id+'">';
                content += type.handlers[i].message;
                content += '</label><br/>';
            }
            content += '</div>';
        }
        var disabled = (type.handlers[0].noname) ? ' disabled = "disabled" ' : '';
        content += '<label for="'+nameid+'">'+type.namemessage+'</label>';
        content += ' <input type="text" id="'+nameid+'" value="" '+disabled+' />';

        var Y = this.Y;
        var self = this;
        var panel = new M.core.dialogue({
            bodyContent: content,
            width: '350px',
            modal: true,
            visible: true,
            render: true,
            align: {
                node: null,
                points: [Y.WidgetPositionAlign.CC, Y.WidgetPositionAlign.CC]
            }
        });

        // When the panel is hidden - destroy it and then check for other pending uploads
        panel.after("visibleChange", function(e) {
            if (!panel.get('visible')) {
                panel.destroy(true);
                self.check_upload_queue();
            }
        });

        var namefield = Y.one('#'+nameid);
        var submit = function(e) {
            e.preventDefault();
            var name = Y.Lang.trim(namefield.get('value'));
            var module = false;
            var noname = false;
            if (type.handlers.length > 1) {
                // Find out which module was selected
                var div = Y.one('#dndupload_handlers'+uploadid);
                div.all('input').each(function(input) {
                    if (input.get('checked')) {
                        var idx = input.get('value');
                        module = type.handlers[idx].module;
                        noname = type.handlers[idx].noname;
                    }
                });
                if (!module) {
                    return;
                }
            } else {
                module = type.handlers[0].module;
                noname = type.handlers[0].noname;
            }
            if (name == '' && !noname) {
                return;
            }
            if (noname) {
                name = '';
            }
            panel.hide();
            // Do the upload
            self.upload_item(name, type.type, contents, section, sectionnumber, module);
        };

        // Add the submit/cancel buttons to the bottom of the dialog.
        panel.addButton({
            label: M.util.get_string('upload', 'moodle'),
            action: submit,
            section: Y.WidgetStdMod.FOOTER,
            name: 'submit'
        });
        panel.addButton({
            label: M.util.get_string('cancel', 'moodle'),
            action: function(e) {
                e.preventDefault();
                panel.hide();
            },
            section: Y.WidgetStdMod.FOOTER
        });
        var submitbutton = panel.getButton('submit').button;
        namefield.on('key', submit, 'enter'); // Submit the form if 'enter' pressed
        namefield.after('keyup', function() {
            if (Y.Lang.trim(namefield.get('value')) == '') {
                submitbutton.disable();
            } else {
                submitbutton.enable();
            }
        });

        // Enable / disable the 'name' box, depending on the handler selected.
        for (i=0; i<type.handlers.length; i++) {
            if (type.handlers[i].noname) {
                Y.one('#dndupload_handler'+uploadid+type.handlers[i].module).on('click', function (e) {
                    namefield.set('disabled', 'disabled');
                    submitbutton.enable();
                });
            } else {
                Y.one('#dndupload_handler'+uploadid+type.handlers[i].module).on('click', function (e) {
                    namefield.removeAttribute('disabled');
                    namefield.focus();
                    if (Y.Lang.trim(namefield.get('value')) == '') {
                        submitbutton.disable();
                    }
                });
            }
        }

        // Focus on the 'name' box
        Y.one('#'+nameid).focus();
    },

    /**
     * Upload any data types that are not files: display a dummy resource element, send
     * the data to the server, update the progress bar for the file, then replace the
     * dummy element with the real information once the AJAX call completes
     * @param name the display name for the resource / activity to create
     * @param type the details of the data type found in the drop event
     * @param contents the actual data that was dropped
     * @param section the DOM element representing the selected course section
     * @param sectionnumber the number of the selected course section
     * @param module the module chosen to handle this upload
     */
    upload_item: function(name, type, contents, section, sectionnumber, module) {

        // This would be an ideal place to use the Y.io function
        // however, this does not support data encoded using the
        // FormData object, which is needed to transfer data from
        // the DataTransfer object into an XMLHTTPRequest
        // This can be converted when the YUI issue has been integrated:
        // http://yuilibrary.com/projects/yui3/ticket/2531274
        var xhr = new XMLHttpRequest();
        var self = this;

        // Add the item to the display
        var resel = this.add_resource_element(name, section, module);

        // Wait for the AJAX call to complete, then update the
        // dummy element with the returned details
        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4) {
                if (xhr.status == 200) {
                    var result = JSON.parse(xhr.responseText);
                    if (result) {
                        if (result.error == 0) {
                            // All OK - replace the dummy element.
                            resel.li.outerHTML = result.fullcontent;
                            if (self.Y.UA.gecko > 0) {
                                // Fix a Firefox bug which makes sites with a '~' in their wwwroot
                                // log the user out when clicking on the link (before refreshing the page).
                                resel.li.outerHTML = unescape(resel.li.outerHTML);
                            }
                            self.add_editing(result.elementid);
                        } else {
                            // Error - remove the dummy element
                            resel.parent.removeChild(resel.li);
                            new M.core.alert({message: result.error});
                        }
                    }
                } else {
                    new M.core.alert({message: M.util.get_string('servererror', 'moodle')});
                }
            }
        };

        // Prepare the data to send
        var formData = new FormData();
        formData.append('contents', contents);
        formData.append('displayname', name);
        formData.append('sesskey', M.cfg.sesskey);
        formData.append('course', this.courseid);
        formData.append('section', sectionnumber);
        formData.append('type', type);
        formData.append('module', module);

        // Send the data
        xhr.open("POST", this.url, true);
        xhr.send(formData);
    },

    /**
     * Call the AJAX course editing initialisation to add the editing tools
     * to the newly-created resource link
     * @param elementid the id of the DOM element containing the new resource link
     * @param sectionnumber the number of the selected course section
     */
    add_editing: function(elementid) {
        var node = Y.one('#' + elementid);
        YUI().use('moodle-course-coursebase', function(Y) {
            Y.log("Invoking setup_for_resource", 'debug', 'coursedndupload');
            M.course.coursebase.invoke_function('setup_for_resource', node);
        });
        if (M.core.actionmenu && M.core.actionmenu.newDOMNode) {
            M.core.actionmenu.newDOMNode(node);
        }
    }
};
