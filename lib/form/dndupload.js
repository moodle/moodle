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
        // options may include: itemid, acceptedtypes, maxfiles, maxbytes, clientid, repositoryid, author, contextid
        options: {},
        // itemid used for repository upload
        itemid: null,
        // accepted filetypes accepted by this form passed to repository
        acceptedtypes: [],
        // maximum size of files allowed in this form
        maxbytes: 0,
        // Maximum combined size of files allowed in this form. {@link FILE_AREA_MAX_BYTES_UNLIMITED}
        areamaxbytes: -1,
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
        // Holds the progress bar elements for each file.
        progressbars: {},
        // Number of request in queue and number of request uploading.
        totalOfRequest: 0,
        // Number of request upload.
        numberOfRequestUpload: 0,

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
         *            areamaxbytes: maximum combined size of files allowed in this form
         *            clientid: unqiue id of this form field used for html elements
         *            contextid: id of the current cotnext
         *            containerid: htmlid of container
         *            repositories: array of repository objects passed from filepicker
         *            filemanager: filemanager element we are working with
         *            formcallback: callback  to filepicker element to refesh when uploaded
         *          }
         */
        init: function(Y, options) {
            this.Y = Y;

            if (!this.browser_supported()) {
                Y.one('body').addClass('dndnotsupported');
                return; // Browser does not support the required functionality
            }

            // try and retrieve enabled upload repository
            this.repositoryid = this.get_upload_repositoryid(options.repositories);

            if (!this.repositoryid) {
                Y.one('body').addClass('dndnotsupported');
                return; // no upload repository is enabled to upload to
            }

            Y.one('body').addClass('dndsupported');

            this.options = options;
            this.acceptedtypes = options.acceptedtypes;
            this.clientid = options.clientid;
            this.maxbytes = options.maxbytes;
            this.areamaxbytes = options.areamaxbytes;
            this.itemid = options.itemid;
            this.author = options.author;
            this.container = this.Y.one('#'+options.containerid);

            if (options.filemanager) {
                // Needed to tell the filemanager to redraw when files uploaded
                // and to check how many files are already uploaded
                this.filemanager = options.filemanager;
            } else if (options.formcallback) {

                // Needed to tell the filepicker to update when a new
                // file is uploaded
                this.callback = options.formcallback;
            } else {
                if (M.cfg.developerdebug) {
                    alert('dndupload: Need to define either options.filemanager or options.formcallback');
                }
                return;
            }

            this.init_events();
            this.init_page_events();
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
            this.Y.on('drop', function() {
                this.pageentercount = 0;
                this.hide_drop_target();
            }.bind(this));
        },

        /**
         * Check if the filemanager / filepicker is disabled
         * @return bool - true if disabled
         */
        is_disabled: function() {
            return (this.container.ancestor('.fitem.disabled') != null);
        },

        /**
         * Show the 'drop files here' message when file(s) are dragged
         * onto the page
         */
        drag_enter_page: function(e) {
            if (this.is_disabled()) {
                return false;
            }
            if (!this.has_files(e)) {
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
        check_drag: function(e) {
            if (this.is_disabled()) {
                return false;
            }
            if (!this.has_files(e)) {
                return false;
            }

            e.preventDefault();
            e.stopPropagation();

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
                var options = {
                    files: files,
                    options: this.options,
                    repositoryid: this.repositoryid,
                    currentfilecount: this.filemanager.filecount, // All files uploaded.
                    currentfiles: this.filemanager.options.list, // Only the current folder.
                    callback: Y.bind('update_filemanager', this),
                    callbackprogress: Y.bind('update_progress', this),
                    callbackcancel: Y.bind('hide_progress', this),
                    callbackNumberOfRequestUpload: {
                        get: Y.bind('getNumberOfRequestUpload', this),
                        increase: Y.bind('increaseNumberOfRequestUpload', this),
                        decrease: Y.bind('decreaseNumberOfRequestUpload', this),
                        getTotal: Y.bind('getTotalRequestUpload', this),
                        increaseTotal: Y.bind('increaseTotalRequest', this),
                        reset: Y.bind('resetNumberOfRequestUpload', this)
                    },
                    callbackClearProgress: Y.bind('clear_progress', this),
                    callbackStartProgress: Y.bind('startProgress', this),
                };
                this.show_progress();
                var uploader = new dnduploader(options);
                uploader.start_upload();
            } else {
                if (files.length >= 1) {
                    options = {
                        files:[files[0]],
                        options: this.options,
                        repositoryid: this.repositoryid,
                        currentfilecount: 0,
                        currentfiles: [],
                        callback: Y.bind('update_filemanager', this),
                        callbackprogress: Y.bind('update_progress', this),
                        callbackcancel: Y.bind('hide_progress', this),
                        callbackNumberOfRequestUpload: {
                            get: Y.bind('getNumberOfRequestUpload', this),
                            increase: Y.bind('increaseNumberOfRequestUpload', this),
                            decrease: Y.bind('decreaseNumberOfRequestUpload', this),
                            getTotal: Y.bind('getTotalRequestUpload', this),
                            increaseTotal: Y.bind('increaseTotalRequest', this),
                            reset: Y.bind('resetNumberOfRequestUpload', this)
                        },
                        callbackClearProgress: Y.bind('clear_progress', this),
                        callbackStartProgress: Y.bind('startProgress', this),
                    };
                    this.show_progress();
                    uploader = new dnduploader(options);
                    uploader.start_upload();
                }
            }

            return false;
        },

        /**
         * Increase number of request upload.
         */
        increaseNumberOfRequestUpload: function() {
            this.numberOfRequestUpload++;
        },

        /**
         * Increase total request.
         *
         * @param {number} newFileCount Number of new files.
         */
        increaseTotalRequest: function(newFileCount) {
            this.totalOfRequest += newFileCount;
        },

        /**
         * Decrease number of request upload.
         */
        decreaseNumberOfRequestUpload: function() {
            this.numberOfRequestUpload--;
        },

        /**
         * Return number of request upload.
         *
         * @returns {number}
         */
        getNumberOfRequestUpload: function() {
            return this.numberOfRequestUpload;
        },

        /**
         * Return number of request upload.
         *
         * @returns {number}
         */
        getTotalRequestUpload: function() {
            return this.totalOfRequest;
        },

        /**
         * Return number of request upload.
         *
         * @returns {number}
         */
        resetNumberOfRequestUpload: function() {
            this.numberOfRequestUpload = 0;
            this.totalOfRequest = 0;
        },

        /**
         * Check to see if the drag event has any files in it
         *
         * @param e event object
         * @return boolean true if event has files
         */
        has_files: function(e) {
            // In some browsers, dataTransfer.types may be null for a
            // 'dragover' event, so ensure a valid Array is always
            // inspected.
            var types = e._event.dataTransfer.types || [];
            for (var i=0; i<types.length; i++) {
                if (types[i] == 'Files') {
                    return true;
                }
            }
            return false;
        },

        /**
         * Highlight the area where files could be dropped
         */
        show_drop_target: function() {
            this.container.addClass('dndupload-ready');
        },

        hide_drop_target: function() {
            this.container.removeClass('dndupload-ready');
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
         * Show the element showing the upload in progress
         */
        show_progress: function() {
            this.container.addClass('dndupload-inprogress');
        },

        /**
         * Hide the element showing upload in progress
         */
        hide_progress: function() {
            if (!Object.keys(this.progressbars).length) {
                this.container.removeClass('dndupload-inprogress');
            }
        },

        /**
         * Tell the attached filemanager element (if any) to refresh on file
         * upload
         */
        update_filemanager: function(params) {
            this.clear_progress();
            this.hide_progress();
            if (this.filemanager) {
                // update the filemanager that we've uploaded the files
                this.filemanager.filepicker_callback();
            } else if (this.callback) {
                this.callback(params);
            }
        },

        /**
         * Clear the all progress bars.
         */
        clear_progress: function() {
            var filename;
            for (filename in this.progressbars) {
                if (this.progressbars.hasOwnProperty(filename)) {
                    this.progressbars[filename].progressouter.remove(true);
                    delete this.progressbars[filename];
                }
            }
        },

        /**
         * Show the current progress of the uploaded file.
         */
        update_progress: function(filename, percent) {
            this.startProgress(filename);
            this.progressbars[filename].progressinner.setStyle('width', percent + '%');
            this.progressbars[filename].progressinner.setAttribute('aria-valuenow', percent);
            this.progressbars[filename].progressinnertext.setContent(percent + '% ' + M.util.get_string('complete', 'moodle'));
        },

        /**
         * Start to show the progress of the uploaded file.
         *
         * @param {String} filename Name of file upload.
         */
        startProgress: function(filename) {
            if (this.progressbars[filename] === undefined) {
                var dispfilename = filename;
                if (dispfilename.length > 50) {
                    dispfilename = dispfilename.substr(0, 49) + '&hellip;';
                }
                var progressouter = this.container.create('<div>' + dispfilename +
                    '<div class="progress">' +
                    '   <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">' +
                    '       <span class="sr-only"></span>' +
                    '   </div>' +
                    '</div></div>');
                var progressinner = progressouter.one('.progress-bar');
                var progressinnertext = progressinner.one('.sr-only');
                var progresscontainer = this.container.one('.dndupload-progressbars');
                progresscontainer.appendChild(progressouter);

                this.progressbars[filename] = {
                    progressouter: progressouter,
                    progressinner: progressinner,
                    progressinnertext: progressinnertext
                };
            }
        }
    };

    var dnduploader = function(options) {
        dnduploader.superclass.constructor.apply(this, arguments);
    };

    Y.extend(dnduploader, Y.Base, {
        // The URL to send the upload data to.
        api: M.cfg.wwwroot+'/repository/repository_ajax.php',
        // Options passed into the filemanager/filepicker element.
        options: {},
        // The function to call when all uploads complete.
        callback: null,
        // The function to call as the upload progresses
        callbackprogress: null,
        // The function to call if the upload is cancelled
        callbackcancel: null,
        // The list of files dropped onto the element.
        files: null,
        // The ID of the 'upload' repository.
        repositoryid: 0,
        // Array of files already in the current folder (to check for name clashes).
        currentfiles: null,
        // Total number of files already uploaded (to check for exceeding limits).
        currentfilecount: 0,
        // Number of new files will be upload in this dndupload (to check for exceeding limits).
        newFileCount: 0,
        // Total size of the files present in the area.
        currentareasize: 0,
        // The list of files to upload.
        uploadqueue: [],
        // This list of files with name clashes.
        renamequeue: [],
        // Size of the current queue.
        queuesize: 0,
        // Set to true if the user has clicked on 'overwrite all'.
        overwriteall: false,
        // Set to true if the user has clicked on 'rename all'.
        renameall: false,
        // The file manager helper.
        filemanagerhelper: null,
        // The function to call as the number of request upload.
        callbackNumberOfRequestUpload: null,
        // The function to call as the clear progresses.
        callbackClearProgress: null,
        // The function to call as the start progress.
        callbackStartProgress: null,

        /**
         * Initialise the settings for the dnduploader
         * @param object params - includes:
         *                     options (copied from the filepicker / filemanager)
         *                     repositoryid - ID of the upload repository
         *                     callback - the function to call when uploads are complete
         *                     currentfiles - the list of files already in the current folder in the filemanager
         *                     currentfilecount - the total files already in the filemanager
         *                     files - the list of files to upload
         * @return void
         */
        initializer: function(params) {
            this.options = params.options;
            this.repositoryid = params.repositoryid;
            this.callback = params.callback;
            this.callbackprogress = params.callbackprogress;
            this.callbackcancel = params.callbackcancel;
            this.currentfiles = params.currentfiles;
            this.currentfilecount = params.currentfilecount;
            this.currentareasize = 0;
            this.filemanagerhelper = this.options.filemanager;
            this.callbackNumberOfRequestUpload = params.callbackNumberOfRequestUpload;
            this.callbackClearProgress = params.callbackClearProgress;
            this.callbackStartProgress = params.callbackStartProgress;

            // Retrieve the current size of the area.
            for (var i = 0; i < this.currentfiles.length; i++) {
                this.currentareasize += this.currentfiles[i].size;
            };

            if (!this.initialise_queue(params.files)) {
                if (this.callbackcancel) {
                    this.callbackcancel();
                }
            }
        },

        /**
         * Entry point for starting the upload process (starts by processing any
         * renames needed)
         */
        start_upload: function() {
            this.process_renames(); // Automatically calls 'do_upload' once renames complete.
        },

        /**
         * Display a message in a popup
         * @param string msg - the message to display
         * @param string type - 'error' or 'info'
         */
        print_msg: function(msg, type) {
            var header = M.util.get_string('error', 'moodle');
            if (type != 'error') {
                type = 'info'; // one of only two types excepted
                header = M.util.get_string('info', 'moodle');
            }
            if (!this.msg_dlg) {
                this.msg_dlg_node = Y.Node.create(M.core_filepicker.templates.message);
                this.msg_dlg_node.generateID();

                this.msg_dlg = new M.core.dialogue({
                    bodyContent: this.msg_dlg_node,
                    centered: true,
                    modal: true,
                    visible: false
                });
                this.msg_dlg.plug(Y.Plugin.Drag,{handles:['#'+this.msg_dlg_node.get('id')+' .yui3-widget-hd']});
                this.msg_dlg_node.one('.fp-msg-butok').on('click', function(e) {
                    e.preventDefault();
                    this.msg_dlg.hide();
                }, this);
            }

            this.msg_dlg.set('headerContent', header);
            this.msg_dlg_node.removeClass('fp-msg-info').removeClass('fp-msg-error').addClass('fp-msg-'+type)
            this.msg_dlg_node.one('.fp-msg-text').setContent(msg);
            this.msg_dlg.show();
        },

        /**
         * Check the size of each file and add to either the uploadqueue or, if there
         * is a name clash, the renamequeue
         * @param FileList files - the files to upload
         * @return void
         */
        initialise_queue: function(files) {
            this.uploadqueue = [];
            this.renamequeue = [];
            this.queuesize = 0;

            // Loop through the files and find any name clashes with existing files.
            var i;
            for (i=0; i<files.length; i++) {
                if (this.options.maxbytes > 0 && files[i].size > this.options.maxbytes) {
                    // Check filesize before attempting to upload.
                    var maxbytesdisplay = this.display_size(this.options.maxbytes);
                    this.print_msg(M.util.get_string('maxbytesfile', 'error', {
                            file: files[i].name,
                            size: maxbytesdisplay
                        }), 'error');
                    this.uploadqueue = []; // No uploads if one file is too big.
                    return;
                }

                if (this.has_name_clash(files[i].name)) {
                    this.renamequeue.push(files[i]);
                } else {
                    if (!this.add_to_upload_queue(files[i], files[i].name, false)) {
                        return false;
                    }
                }
                this.queuesize += files[i].size;
            }
            return true;
        },

        /**
         * Generate the display for file size
         * @param int size The size to convert to human readable form
         * @return string
         */
        display_size: function(size) {
            // This is snippet of code (with some changes) is from the display_size function in moodlelib.
            var gb = M.util.get_string('sizegb', 'moodle'),
                mb = M.util.get_string('sizemb', 'moodle'),
                kb = M.util.get_string('sizekb', 'moodle'),
                b  = M.util.get_string('sizeb', 'moodle');

            if (size >= 1073741824) {
                size = Math.round(size / 1073741824 * 10) / 10 + gb;
            } else if (size >= 1048576) {
                size = Math.round(size / 1048576 * 10) / 10 + mb;
            } else if (size >= 1024) {
                size = Math.round(size / 1024 * 10) / 10 + kb;
            } else {
                size = parseInt(size, 10) + ' ' + b;
            }

            return size;
        },

        /**
         * Add a single file to the uploadqueue, whilst checking the maxfiles limit
         * @param File file - the file to add
         * @param string filename - the name to give the file on upload
         * @param bool overwrite - true to overwrite the existing file
         * @return bool true if added successfully
         */
        add_to_upload_queue: function(file, filename, overwrite) {
            if (!overwrite) {
                this.newFileCount++;
            }

            // The value for "unlimited files" is -1, so 0 should mean 0.
            if (this.options.maxfiles >= 0 && this.getTotalNumberOfFiles() > this.options.maxfiles) {
                // Too many files - abort entire upload.
                this.uploadqueue = [];
                this.renamequeue = [];
                this.print_msg(M.util.get_string('maxfilesreached', 'moodle', this.options.maxfiles), 'error');
                return false;
            }
            // The new file will cause the area to reach its limit, we cancel the upload of all files.
            // -1 is the value defined by FILE_AREA_MAX_BYTES_UNLIMITED.
            if (this.options.areamaxbytes > -1) {
                var sizereached = this.currentareasize + this.queuesize + file.size;
                if (sizereached > this.options.areamaxbytes) {
                    this.uploadqueue = [];
                    this.renamequeue = [];
                    this.print_msg(M.util.get_string('maxareabytesreached', 'moodle'), 'error');
                    return false;
                }
            }
            this.uploadqueue.push({file:file, filename:filename, overwrite:overwrite});
            return true;
        },

        /**
         * Get total number of files: Number of uploaded files, number of files unloading in other dndupload,
         * number of files need to be upload in this dndupload.
         * @return number Total number of files.
         */
        getTotalNumberOfFiles: function() {
            // Get number of files we added into other dndupload.
            let totalOfFiles = 0;
            if(this.callbackNumberOfRequestUpload) {
                totalOfFiles = this.callbackNumberOfRequestUpload.getTotal();
            }

            return this.currentfilecount + this.newFileCount + totalOfFiles;
        },

        /**
         * Take the next file from the renamequeue and ask the user what to do with
         * it. Called recursively until the queue is empty, then calls do_upload.
         * @return void
         */
        process_renames: function() {
            if (this.renamequeue.length == 0) {
                // All rename processing complete - start the actual upload.
                if(this.callbackNumberOfRequestUpload && this.uploadqueue.length > 0) {
                    this.callbackNumberOfRequestUpload.increaseTotal(this.newFileCount);
                }
                this.do_upload();
                return;
            }
            var multiplefiles = (this.renamequeue.length > 1);

            // Get the next file from the rename queue.
            var file = this.renamequeue.shift();
            // Generate a non-conflicting name for it.
            var newname = this.generate_unique_name(file.name);

            // If the user has clicked on overwrite/rename ALL then process
            // this file, as appropriate, then process the rest of the queue.
            if (this.overwriteall) {
                if (this.add_to_upload_queue(file, file.name, true)) {
                    this.process_renames();
                }
                return;
            }
            if (this.renameall) {
                if (this.add_to_upload_queue(file, newname, false)) {
                    this.process_renames();
                }
                return;
            }

            // Ask the user what to do with this file.
            var self = this;

            var process_dlg_node;
            if (multiplefiles) {
                process_dlg_node = Y.Node.create(M.core_filepicker.templates.processexistingfilemultiple);
            } else {
                process_dlg_node = Y.Node.create(M.core_filepicker.templates.processexistingfile);
            }
            var node = process_dlg_node;
            node.generateID();
            var process_dlg = new M.core.dialogue({
                bodyContent: node,
                headerContent: M.util.get_string('fileexistsdialogheader', 'repository'),
                centered: true,
                modal: true,
                visible: false
            });
            process_dlg.plug(Y.Plugin.Drag,{handles:['#'+node.get('id')+' .yui3-widget-hd']});

            // Overwrite original.
            node.one('.fp-dlg-butoverwrite').on('click', function(e) {
                e.preventDefault();
                process_dlg.hide();
                if (self.add_to_upload_queue(file, file.name, true)) {
                    self.process_renames();
                }
            }, this);

            // Rename uploaded file.
            node.one('.fp-dlg-butrename').on('click', function(e) {
                e.preventDefault();
                process_dlg.hide();
                if (self.add_to_upload_queue(file, newname, false)) {
                    self.process_renames();
                }
            }, this);

            // Cancel all uploads.
            node.one('.fp-dlg-butcancel').on('click', function(e) {
                e.preventDefault();
                process_dlg.hide();
                if (self.callbackcancel) {
                    this.notifyUploadCompleted();
                    self.callbackClearProgress();
                    self.callbackcancel();
                }
            }, this);

            // When we are at the file limit, only allow 'overwrite', not rename.
            if (this.getTotalNumberOfFiles() == this.options.maxfiles) {
                node.one('.fp-dlg-butrename').setStyle('display', 'none');
                if (multiplefiles) {
                    node.one('.fp-dlg-butrenameall').setStyle('display', 'none');
                }
            }

            // If there are more files still to go, offer the 'overwrite/rename all' options.
            if (multiplefiles) {
                // Overwrite all original files.
                node.one('.fp-dlg-butoverwriteall').on('click', function(e) {
                    e.preventDefault();
                    process_dlg.hide();
                    this.overwriteall = true;
                    if (self.add_to_upload_queue(file, file.name, true)) {
                        self.process_renames();
                    }
                }, this);

                // Rename all new files.
                node.one('.fp-dlg-butrenameall').on('click', function(e) {
                    e.preventDefault();
                    process_dlg.hide();
                    this.renameall = true;
                    if (self.add_to_upload_queue(file, newname, false)) {
                        self.process_renames();
                    }
                }, this);
            }
            node.one('.fp-dlg-text').setContent(M.util.get_string('fileexists', 'moodle', file.name));
            process_dlg_node.one('.fp-dlg-butrename').setContent(M.util.get_string('renameto', 'repository', newname));

            // Destroy the dialog once it has been hidden.
            process_dlg.after('visibleChange', function(e) {
                if (!process_dlg.get('visible')) {
                    if (self.callbackcancel) {
                        self.callbackcancel();
                    }
                    process_dlg.destroy(true);
                }
            }, this);

            process_dlg.show();
        },

        /**
         * Trigger upload completed event.
         */
        notifyUploadCompleted: function() {
            require(['core_form/events'], function(FormEvent) {
                const elementId = this.filemanagerhelper ? this.filemanagerhelper.filemanager.get('id') : this.options.containerid;
                FormEvent.triggerUploadCompleted(elementId);
            }.bind(this));
         },

        /**
         * Trigger form upload start events.
         */
        notifyUploadStarted: function() {
            require(['core_form/events'], function(FormEvent) {
                const elementId = this.filemanagerhelper ? this.filemanagerhelper.filemanager.get('id') : this.options.containerid;
                FormEvent.triggerUploadStarted(elementId);
            }.bind(this));
        },

        /**
         * Checks if there is already a file with the given name in the current folder
         * or in the list of already uploading files
         * @param string filename - the name to test
         * @return bool true if the name already exists
         */
        has_name_clash: function(filename) {
            // Check against the already uploaded files
            var i;
            for (i=0; i<this.currentfiles.length; i++) {
                if (filename == this.currentfiles[i].filename) {
                    return true;
                }
            }
            // Check against the uploading files that have already been processed
            for (i=0; i<this.uploadqueue.length; i++) {
                if (filename == this.uploadqueue[i].filename) {
                    return true;
                }
            }
            return false;
        },

        /**
         * Gets a unique file name
         *
         * @param string filename
         * @return string the unique filename generated
         */
        generate_unique_name: function(filename) {
            // Loop through increating numbers until a unique name is found.
            while (this.has_name_clash(filename)) {
                filename = increment_filename(filename);
            }
            return filename;
        },

        /**
         * Upload the next file from the uploadqueue - called recursively after each
         * upload is complete, then handles the callback to the filemanager/filepicker
         * @param lastresult - the last result from the server
         */
        do_upload: function(lastresult) {
            if (this.uploadqueue.length > 0) {
                var filedetails = this.uploadqueue.shift();
                this.upload_file(filedetails.file, filedetails.filename, filedetails.overwrite);
            } else {
                if (this.callbackNumberOfRequestUpload && !this.callbackNumberOfRequestUpload.get()) {
                    this.uploadfinished(lastresult);
                }
            }
        },

        /**
         * Run the callback to the filemanager/filepicker
         */
        uploadfinished: function(lastresult) {
            this.callbackNumberOfRequestUpload.reset();
            this.callback(lastresult);
        },

        /**
         * Upload a single file via an AJAX call to the 'upload' repository. Automatically
         * calls do_upload as each upload completes.
         * @param File file - the file to upload
         * @param string filename - the name to give the file
         * @param bool overwrite - true if the existing file should be overwritten
         */
        upload_file: function(file, filename, overwrite) {

            // This would be an ideal place to use the Y.io function
            // however, this does not support data encoded using the
            // FormData object, which is needed to transfer data from
            // the DataTransfer object into an XMLHTTPRequest
            // This can be converted when the YUI issue has been integrated:
            // http://yuilibrary.com/projects/yui3/ticket/2531274
            var xhr = new XMLHttpRequest();
            var self = this;
            if (self.callbackNumberOfRequestUpload) {
                self.callbackNumberOfRequestUpload.increase();
            }

            // Start progress bar.
            xhr.onloadstart = function() {
                self.callbackStartProgress(filename);
                self.notifyUploadStarted();
            };

            // Update the progress bar
            xhr.upload.addEventListener('progress', function(e) {
                if (e.lengthComputable && self.callbackprogress) {
                    var percentage = Math.round((e.loaded * 100) / e.total);
                    self.callbackprogress(filename, percentage);
                }
            }, false);

            xhr.onreadystatechange = function() { // Process the server response
                if (xhr.readyState == 4) {
                    self.notifyUploadCompleted();
                    if (xhr.status == 200) {
                        var result = JSON.parse(xhr.responseText);
                        if (result) {
                            if (result.error) {
                                self.print_msg(result.error, 'error'); // TODO add filename?
                                self.uploadfinished();
                            } else {
                                // Only update the filepicker if there were no errors
                                if (result.event == 'fileexists') {
                                    // Do not worry about this, as we only care about the last
                                    // file uploaded, with the filepicker
                                    result.file = result.newfile.filename;
                                    result.url = result.newfile.url;
                                }
                                result.client_id = self.options.clientid;
                                if (self.callbackprogress) {
                                    self.callbackprogress(filename, 100);
                                }
                            }
                        }
                        if (self.callbackNumberOfRequestUpload) {
                            self.callbackNumberOfRequestUpload.decrease();
                        }
                        self.do_upload(result); // continue uploading
                    } else {
                        self.print_msg(M.util.get_string('serverconnection', 'error'), 'error');
                        self.uploadfinished();
                    }
                }
            };

            // Prepare the data to send
            var formdata = new FormData();
            formdata.append('repo_upload_file', file); // The FormData class allows us to attach a file
            formdata.append('sesskey', M.cfg.sesskey);
            formdata.append('repo_id', this.repositoryid);
            formdata.append('itemid', this.options.itemid);
            if (this.options.author) {
                formdata.append('author', this.options.author);
            }
            if (this.options.filemanager && this.options.filemanager.currentpath) { // Filepickers do not have folders
                formdata.append('savepath', this.options.filemanager.currentpath);
            } else {
                formdata.append('savepath', '/');
            }
            formdata.append('title', filename);
            if (overwrite) {
                formdata.append('overwrite', 1);
            }
            if (this.options.contextid) {
                formdata.append('ctx_id', this.options.contextid);
            }

            // Accepted types can be either a string or an array, but an array is
            // expected in the processing script, so make sure we are sending an array
            if (this.options.acceptedtypes.constructor == Array) {
                for (var i=0; i<this.options.acceptedtypes.length; i++) {
                    formdata.append('accepted_types[]', this.options.acceptedtypes[i]);
                }
            } else {
                formdata.append('accepted_types[]', this.options.acceptedtypes);
            }

            // Send the file & required details.
            var uploadUrl = this.api;
            if (uploadUrl.indexOf('?') !== -1) {
                uploadUrl += '&action=upload';
            } else {
                uploadUrl += '?action=upload';
            }
            xhr.open("POST", uploadUrl, true);
            xhr.send(formdata);
            return true;
        }
    });

    dnduploadhelper.init(Y, options);
};
