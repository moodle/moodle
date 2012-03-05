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
 * Javascript library for enableing a drag and drop upload interface
 *
 * @package    moodlecore
 * @subpackage form
 * @copyright  2011 Davo Smith
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

M.form_dndupload = {}

M.form_dndupload.init = function(Y, options) {
    var dnduploadhelper = {
        // YUI object.
        Y: null,
        // URL for upload requests
        url: M.cfg.wwwroot + '/repository/repository_ajax.php?action=upload',
        // itemid used for repository upload
        itemid: null,
        // accepted filetypes accepted by this form passed to repository
        acceptedtypes: [],
        // maximum number of files this form allows
        maxfiles: 0,
        // maximum size of files allowed in this form
        maxbytes: 0,
        // unqiue id of this form field used for html elements
        clientid: '',
        // upload repository id, used for upload
        repositoryid: 0,
        // container which holds the node which recieves drag events
        container: null,
        // filemanager element we are working with
        filemanager: null,
        // callback  to filepicker element to refesh when uploaded
        callback: null,
        // Nasty hack to distinguish between dragenter(first entry),
        // dragenter+dragleave(moving between child elements) and dragleave (leaving element)
        entercount: 0,
        pageentercount: 0,

        /**
         * Initalise the drag and drop upload interface
         * Note: one and only one of options.filemanager and options.formcallback must be defined
         *
         * @param Y the YUI object
         * @param object options {
         *            itemid: itemid used for repository upload in this form
         *            acceptdtypes: accepted filetypes by this form
         *            maxfiles: maximum number of files this form allows
         *            maxbytes: maximum size of files allowed in this form
         *            clientid: unqiue id of this form field used for html elements
         *            containerprefix: prefix of htmlid of container
         *            repositories: array of repository objects passed from filepicker
         *            filemanager: filemanager element we are working with
         *            callback: callback  to filepicker element to refesh when uploaded
         *          }
         */
        init: function(Y, options) {
            this.Y = Y;

            if (!this.browser_supported()) {
                return; // Browser does not support the required functionality
            }

            // try and retrieve enabled upload repository
            this.repositoryid = this.get_upload_repositoryid(options.repositories);

            if (!this.repositoryid) {
                return; // no upload repository is enabled to upload to
            }

            this.acceptedtypes = options.acceptedtypes;
            this.clientid = options.clientid;
            this.maxfiles = options.maxfiles;
            this.maxbytes = options.maxbytes;
            this.itemid = options.itemid;
            this.container = this.Y.one(options.containerprefix + this.clientid);

            if (options.filemanager) {
                // Needed to tell the filemanager to redraw when files uploaded
                // and to check how many files are already uploaded
                this.filemanager = options.filemanager;
                // Add a callback to show the 'drag and drop enabled' message
                // within the filemanager box once it has finished loading,
                // if there are no files yet uploaded
                this.filemanager.emptycallback = function(clientid) {
                    var el = Y.one('#dndenabled2-'+clientid);
                    if (el) {
                        el.setStyle('display', 'inline');
                    }
                }
            } else if (options.formcallback) {

                // Needed to tell the filepicker to update when a new
                // file is uploaded
                this.callback = options.formcallback;
            } else {
                if (M.cfg.developerdebug) {
                    alert('dndupload: Need to define either options.filemanager or options.callback');
                }
                return;
            }

            this.init_events();
            this.init_page_events();
            this.Y.one('#dndenabled-'+this.clientid).setStyle('display', 'inline');
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
         * Get upload repoistory from array of enabled repositories
         *
         * @param array repositories repository objects passed from filepicker
         * @param returns int id of upload repository or false if not found
         */
        get_upload_repositoryid: function(repositories) {
            for (var i in repositories) {
                if (repositories[i].type == "upload") {
                    return repositories[i].id;
                }
            }

            return false;
        },

        /**
         * Initialise drag events on node container, all events need
         * to be processed for drag and drop to work
         */
        init_events: function() {
            this.Y.on('dragenter', this.drag_enter, this.container, this);
            this.Y.on('dragleave', this.drag_leave, this.container, this);
            this.Y.on('dragover',  this.drag_over,  this.container, this);
            this.Y.on('drop',      this.drop,      this.container, this);
        },

        /**
         * Initialise whole-page events (to show / hide the 'drop files here'
         * message)
         */
        init_page_events: function() {
            this.Y.on('dragenter', this.drag_enter_page, 'body', this);
            this.Y.on('dragleave', this.drag_leave_page, 'body', this);
        },

        /**
         * Show the 'drop files here' message when file(s) are dragged
         * onto the page
         */
        drag_enter_page: function(e) {
            if (!this.has_files(e) || this.reached_maxfiles()) {
                return false;
            }

            this.pageentercount++;
            if (this.pageentercount >= 2) {
                this.pageentercount = 2;
                return false;
            }

            this.show_drop_target();

            return false;
        },

        /**
         * Hide the 'drop files here' message when file(s) are dragged off
         * the page again
         */
        drag_leave_page: function(e) {
            this.pageentercount--;
            if (this.pageentercount == 1) {
                return false;
            }
            this.pageentercount = 0;

            this.hide_drop_target();

            return false;
        },

        /**
         * Check if the drag contents are valid and then call
         * preventdefault / stoppropagation to let the browser know
         * we will handle this drag/drop
         *
         * @param e event object
         * @return boolean true if a valid file drag event
         */
        check_drag: function(e, maxfilesalert) {
            if (!this.has_files(e)) {
                return false;
            }

            e.preventDefault();
            e.stopPropagation();

            if (this.reached_maxfiles()) {
                if (typeof(maxfilesalert) != 'undefined' && maxfilesalert) {
                    alert(M.util.get_string('maxfilesreached', 'moodle', this.maxfiles));
                }
                return false;
            }

            return true;
        },

        /**
         * Handle a dragenter event, highlight the destination node
         * when a suitable drag event occurs
         */
        drag_enter: function(e) {
            if (!this.check_drag(e)) {
                return true;
            }

            this.entercount++;
            if (this.entercount >= 2) {
                this.entercount = 2; // Just moved over a child element - nothing to do
                return false;
            }

            // These lines are needed if the user has dragged something directly
            // from application onto the 'fileupload' box, without crossing another
            // part of the page first
            this.pageentercount = 2;
            this.show_drop_target();

            this.show_upload_ready();
            return false;
        },

        /**
         * Handle a dragleave event, Remove the highlight if dragged from
         * node
         */
        drag_leave: function(e) {
            if (!this.check_drag(e)) {
                return true;
            }

            this.entercount--;
            if (this.entercount == 1) {
                return false; // Just moved over a child element - nothing to do
            }

            this.entercount = 0;
            this.hide_upload_ready();
            return false;
        },

        /**
         * Handle a dragover event. Required to intercept to prevent the browser from
         * handling the drag and drop event as normal
         */
        drag_over: function(e) {
            if (!this.check_drag(e)) {
                return true;
            }

            return false;
        },

        /**
         * Handle a drop event.  Remove the highlight and then upload each
         * of the files (until we reach the file limit, or run out of files)
         */
        drop: function(e) {
            if (!this.check_drag(e, true)) {
                return true;
            }

            this.entercount = 0;
            this.pageentercount = 0;
            this.hide_upload_ready();
            this.hide_drop_target();

            var files = e._event.dataTransfer.files;
            if (this.filemanager) {
                var currentfilecount = this.filemanager.filecount;
                if (((currentfilecount + files.length) > this.maxfiles) && (this.maxfiles != -1)) {
                    alert(M.util.get_string('maxfilesreached', 'moodle', this.maxfiles));
                    return false;
                }
                this.show_progress_spinner();
                for (var i=0, f; f=files[i]; i++) {
                    if (this.upload_file(f)) {
                        currentfilecount++;
                    }
                }
            } else {
                this.show_progress_spinner();
                if (files.length >= 1) {
                    this.upload_file(files[0]);
                }
            }

            return false;
        },

        /**
         * Check to see if the drag event has any files in it
         *
         * @param e event object
         * @return boolean true if event has files
         */
        has_files: function(e) {
            var types = e._event.dataTransfer.types;
            for (var i=0; i<types.length; i++) {
                if (types[i] == 'Files') {
                    return true;
                }
            }
            return false;
        },

        /**
         * Check if reached the maximumum number of allowed files
         *
         * @return boolean true if reached maximum number of files
         */
        reached_maxfiles: function() {
            if (this.filemanager) {
                if (this.filemanager.filecount >= this.maxfiles && this.maxfiles != -1) {
                    return true;
                }
            }
            return false;
        },

        /**
         * Highlight the area where files could be dropped
         */
        show_drop_target: function() {
            this.Y.one('#filemanager-uploadmessage'+this.clientid).setStyle('display', 'block');
        },

        hide_drop_target: function() {
            this.Y.one('#filemanager-uploadmessage'+this.clientid).setStyle('display', 'none');
        },

        /**
         * Highlight the destination node (ready to drop)
         */
        show_upload_ready: function() {
            this.container.addClass('dndupload-over');
        },

        /**
         * Remove highlight on destination node
         */
        hide_upload_ready: function() {
            this.container.removeClass('dndupload-over');
        },

        /**
         * Display a progress spinner in the destination node
         */
        show_progress_spinner: function() {
            // add a loading spinner to show something is happening
            var loadingspinner = this.Y.Node.create('<div id="dndprogresspinner-'+this.clientid+'" style="text-align: center">');
            loadingspinner.append('<img src="'+M.util.image_url('i/loading_small')+'" />');
            this.container.append(loadingspinner);
        },

        /**
         * Remove progress spinner in the destination node
         */
        hide_progress_spinner: function() {
            var spinner = this.Y.one('#dndprogresspinner-'+this.clientid);
            if (spinner) {
                spinner.remove();
            }
        },

        /**
         * Tell the attached filemanager element (if any) to refresh on file
         * upload
         */
        update_filemanager: function() {
            if (this.filemanager) {
                // update the filemanager that we've uploaded the files
                this.filemanager.filepicker_callback();
            }
        },

        /**
         * Upload a single file via an AJAX call to the 'upload' repository
         */
        upload_file: function(file) {
            if (file.size > this.maxbytes && this.maxbytes > 0) {
                // Check filesize before attempting to upload
                this.hide_progress_spinner();
                alert(M.util.get_string('uploadformlimit', 'moodle')+"\n'"+file.name+"'");
                return false;
            }

            // This would be an ideal place to use the Y.io function
            // however, this does not support data encoded using the
            // FormData object, which is needed to transfer data from
            // the DataTransfer object into an XMLHTTPRequest
            // This can be converted when the YUI issue has been integrated:
            // http://yuilibrary.com/projects/yui3/ticket/2531274
            var xhr = new XMLHttpRequest();
            var self = this;
            xhr.onreadystatechange = function() { // Process the server response
                if (xhr.readyState == 4) {
                    self.hide_progress_spinner();
                    if (xhr.status == 200) {
                        var result = JSON.parse(xhr.responseText);
                        if (result) {
                            if (result.error) {
                                alert(result.error);
                            } else if (self.callback) {
                                // Only update the filepicker if there were no errors
                                if (result.event == 'fileexists') {
                                    // Do not worry about this, as we only care about the last
                                    // file uploaded, with the filepicker
                                    result.file = result.newfile.filename;
                                    result.url = result.newfile.url;
                                }
                                result.client_id = self.clientid;
                                self.callback(result);
                            } else {
                                self.update_filemanager();
                            }
                        }
                    } else {
                        alert(M.util.get_string('serverconnection', 'error'));
                    }
                }
            };

            // Prepare the data to send
            var formdata = new FormData();
            formdata.append('repo_upload_file', file); // The FormData class allows us to attach a file
            formdata.append('sesskey', M.cfg.sesskey);
            formdata.append('repo_id', this.repositoryid);
            formdata.append('itemid', this.itemid);
            if (this.filemanager) { // Filepickers do not have folders
                formdata.append('savepath', this.filemanager.currentpath);
            }

            if (this.acceptedtypes.constructor == Array) {
                for (var i=0; i<this.acceptedtypes.length; i++) {
                    formdata.append('accepted_types[]', this.acceptedtypes[i]);
                }
            } else {
                formdata.append('accepted_types[]', this.acceptedtypes);
            }

            // Send the file & required details
            xhr.open("POST", this.url, true);
            xhr.send(formdata);
            return true;
        }
    };

    dnduploadhelper.init(Y, options);
};
