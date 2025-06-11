/**
 * This file is part of Moodle - http://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @package
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery', 'core/log', 'core/ajax', 'core/notification', 'theme_snap/ajax_notification', './cropper'],
    function($, log, ajax, notification, ajaxNotify, Cropper) {

        var savedImageURL = $('#page-header').css("background-image");
        var temporalImageURL = 'none';
        var temporalImageID = 0;
        var temporalFileName = '';
        var cropper = null;
        var cropperRatio = 6/1;
        if ($('#page-site-index').length) {
            cropperRatio = 6/2;
        }
        // TODO - in Moodle 3.1 we should use the core template for this.
        var addCoverImageAlert = function(id, msg, position = null) {
            if (position === "dialogue") {
                var alertPosition = '.snap_cover_image_description';
            } else {
                var alertPosition = '#snap-coverimagecontrol';
            }
            var closestr = M.util.get_string('closebuttontitle', 'moodle');
            if (!$(id).length) {
                $(alertPosition).before(
                    '<div id="' + id + '" class="snap-alert-cover-image alert alert-warning" role="alert">' +
                    msg +
                    '<button type="button" class="close" data-dismiss="alert" aria-label="' + closestr + '">' +
                    '<span aria-hidden="true">&times;</span>' +
                    '</button>' +
                    '</div>'
                );
            }
        };

        /**
         * Get human file size from bytes.
         * http://stackoverflow.com/questions/10420352/converting-file-size-in-bytes-to-human-readable.
         * @param {int} size
         * @returns {string}
         */
        var humanFileSize = function(size) {
            var i = Math.floor(Math.log(size) / Math.log(1024));
            return (size / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + ['B', 'kB', 'MB', 'GB', 'TB'][i];
        };

        /**
         * First state - image selection button visible.
         */
        var state1 = function() {
            temporalImageURL = 'none';
            temporalImageID = 0;
            temporalFileName = '';
            $('#page-header').css('background-image', savedImageURL);
            if (savedImageURL === "none") {
                $('.path-course-view #page-header').removeClass('mast-image');
                $('.path-course-view #page-header .breadcrumb-item a').removeClass('mast-breadcrumb');
            }
            $('#snap-alert-cover-image-size').remove();
            $('#snap-alert-cover-image-bytes').remove();
            $('#snap-coverfiles').val('');
            $('#snap-changecoverimageconfirmation .ok').removeClass('ajaxing');
            $('#snap-changecoverimageconfirmation').removeClass('state-visible');
            $('label[for="snap-coverfiles"]').removeClass('ajaxing');
            $('label[for="snap-coverfiles"]').addClass('state-visible');
            $('body').removeClass('cover-image-change');
        };

        /**
         * Second state - confirm / cancel buttons visible.
         */
        var state2 = function() {
            $('#snap-alert-cover-image-upload-failed').remove();
            $('#snap-changecoverimageconfirmation').removeClass('disabled');
            $('label[for="snap-coverfiles"]').removeClass('state-visible');
            $('#snap-changecoverimageconfirmation').addClass('state-visible');
            $('body').removeClass('cover-image-change');
        };

        /**
         * Get original image url.
         * @param {string} url
         */
        var getOriginalImageURL = function(url) {
            var newURL = url;
            newURL = newURL.replace("url(", "");
            newURL = newURL.replace(")", "");
            newURL = newURL.replace(/['"]+/g, "");
            newURL = newURL.replace(";", "");
            newURL = newURL.replace("croppedimage", "coverimage");
            newURL = newURL.replace("course-image-cropped", "course-image");
            newURL = newURL.replace("category-image-cropped", "category-image");
            newURL = newURL.replace("site-image-cropped", "site-image");
            return newURL;
        };

        /**
         * Get cropped image url.
         * @param {string} url
         */
        var getCroppedImageURL = function(url) {
            var newURL = url;
            newURL = newURL.replace("url(", "");
            newURL = newURL.replace(")", "");
            newURL = newURL.replace(/['"]+/g, "");
            newURL = newURL.replace(";", "");
            return newURL;
        };

        /**
         * Load current image.
         */
        var loadCurrentImage = function() {
            var currentImageUrl = savedImageURL;
            if (currentImageUrl === "none") {
                $('.snap_cover_image_save_button').addClass("d-none");
                return "";
            }
            $('.snap_cover_image_save_button').removeClass("d-none");
            currentImageUrl = getOriginalImageURL(currentImageUrl);
            return currentImageUrl;
        };

        /**
         * Apply listeners to aspect ratio options.
         */
        var aspectRatioOptions = function() {
            if ($('#page-course-view-topics, #page-course-view-weeks').length) {
                // If the Table of contents display option is set to Top.
                if ($('#page-header #course-toc').length) {
                    cropperRatio = 6/2;
                    cropper.setAspectRatio(cropperRatio);
                } else {
                    cropperRatio = 6/1;
                    cropper.setAspectRatio(cropperRatio);
                }
            }
        };

        /**
         * Moodle dialogue box.
         * @param {string} courseShortName
         * @param {int} categoryId
         * @param {object} fpoptions
         * @param {int} siteMaxBytes
         */
        var moodledialogue = function(courseShortName, categoryId, fpoptions, siteMaxBytes) {
            var maxbytesstr = humanFileSize(siteMaxBytes);
            let title = M.util.get_string('imageproperties', 'theme_snap');
            let coverImageDesc = M.util.get_string('coverimagedesc', 'theme_snap', maxbytesstr);
            let coverImageCropperDesc = M.util.get_string('coverimagecropperdesc', 'theme_snap');
            let coverImageSettingsWarning = M.util.get_string('coverimagesettingswarning', 'theme_snap');
            let browseRepositories = M.util.get_string('browserepositories', 'theme_snap');
            let selectImageString = M.util.get_string('selectimage', 'theme_snap');
            let deleteImageString = M.util.get_string('deleteimage', 'theme_snap');
            let previewDisplay = "'display:none'";
            let currentImageURL = '';
            if (temporalImageURL !== 'none') {
                currentImageURL = temporalImageURL;
            } else {
                currentImageURL = loadCurrentImage();
                temporalImageURL = currentImageURL;
            }
            if (currentImageURL !== "") {
                 previewDisplay = "'display:block'";
            }

            let content =
                '<div class="mb-1 snap_cover_image_dialogue">' +
                    '<p class="snap_cover_image_description">' + coverImageDesc + '</p>' +
                    '<p class="snap_cover_image_cropper_description d-none">' + coverImageCropperDesc + '</p>' +
                    '<p class="snap_cover_image_cropper_description d-none">' + coverImageSettingsWarning + '</p>' +
                    '<div class="input-group input-append w-100 snap_cover_image_options">' +
                        '<button class="btn btn-secondary snap_cover_image_browser" id="id_snap_cover_image_browser">' +
                        browseRepositories + '</button>' +
                        '<button class="btn btn-secondary snap_cover_image_delete_image_button d-none" ' +
                        'id="id_snap_cover_image_delete_image">' +
                        deleteImageString + '</button>' +
                    '</div>' +
                // Add the image preview.
                '<div class="mdl-align">' +
                    '<div class="snap_cover_image_preview_box">' +
                        '<img id="id_snap_cover_image_preview" class="snap_cover_image_preview" alt="" ' +
                            'style=' + previewDisplay +
                            'src=' + currentImageURL + '>' +
                    '</div>' +
                '</div>' +
                // Add the save button.
                '<div class="snap_cover_image_save">' +
                        '<button class="btn btn-primary snap_cover_image_save_button d-none" ' +
                        'id="id_snap_cover_image_save_button">' +
                         selectImageString + '</button>' +
                    '</div>' +
                '</div>';

            var dialogue = new M.core.dialogue({
                headerContent: title,
                bodyContent: content,
                width: '800px',
                modal: true,
                visible: false,
                render: true,
                additionalBaseClass: 'snap_cover_image_dialogue',
            });
            dialogue.show();

            if (savedImageURL !== 'none') {
                $('.snap_cover_image_delete_image_button').removeClass('d-none');
            }

            // Preview the original image in the modal.
            if (currentImageURL !== "") {
                $('.snap_cover_image_save_button').removeClass("d-none");
                // Initialize preview image cropper for the current saved image.
                var imageToCrop = document.getElementById('id_snap_cover_image_preview');
                cropper = new Cropper(imageToCrop, {
                    viewMode: 3,
                    aspectRatio: cropperRatio,
                    dragMode: "none",
                    zoomable: false,
                    minCropBoxWidth: 300,
                });
                aspectRatioOptions();
                $('.snap_cover_image_cropper_description').removeClass('d-none');
                $('#id_snap_cover_image_save_button').click(function() {
                    if (cropper.getCroppedCanvas() !== null) {
                        var croppedImage = cropper.getCroppedCanvas().toDataURL("image/png");
                        // Ensure that the page-header in courses has the mast-image class.
                        $('.path-course-view #page-header').addClass('mast-image');
                        $('.path-course-view #page-header .breadcrumb-item a').addClass('mast-breadcrumb');

                        $('#page-header').css('background-image', 'url(' + croppedImage + ')');
                        state2();
                        saveImage({}, courseShortName, categoryId, cropper);
                    }
                });
            }

            // Delete image option.
            $('.snap_cover_image_delete_image_button').click(function(e) {
                e.preventDefault();
                cropper = null;
                let cancelString = M.util.get_string('cancel', 'moodle');
                let confirmString = M.util.get_string('yes', 'moodle');
                let confirmDeleteString = M.util.get_string('confirmdeletefile', 'theme_snap');
                var cancelButton = '<button id="delete_image_cancel_button" class="btn btn-secondary">' +
                    cancelString + '</button>';
                var confirmButton = '<button id="delete_image_confirm_button" ' +
                    'class="btn btn-primary">' + confirmString + '</button>';
                dialogue.hide();

                let deleteDialogueContent =
                    '<div class="mb-1 snap_cover_image_delete_dialogue">' +
                        '<p class="snap_cover_image_delete_description">' + confirmDeleteString + '</p>' +
                        // Add the delete confirmation buttons.
                        '<div class="snap_cover_image_delete_confirmation">' + cancelButton + confirmButton + '</div>' +
                    '</div>';

                var confirmationDialogue = new M.core.dialogue({
                    headerContent: title,
                    bodyContent: deleteDialogueContent,
                    width: '600px',
                    modal: true,
                    visible: false,
                    render: true,
                    additionalBaseClass: 'snap_cover_image_delete_image_dialogue',
                });
                confirmationDialogue.show();
                confirmationDialogue.after("visibleChange", function() {
                    if (!confirmationDialogue.get('visible')) {
                        state1();
                        confirmationDialogue.destroy(true);
                    }
                });

                $('#delete_image_cancel_button').click(function(e) {
                    e.preventDefault();
                    confirmationDialogue.hide();
                });
                $('#delete_image_confirm_button').click(function(e) {
                    e.preventDefault();
                    var ajaxParams = {};
                    if (categoryId !== null) {
                        ajaxParams.categoryid = categoryId;
                    } else if (courseShortName !== null) {
                        ajaxParams.courseshortname = courseShortName;
                    }
                    ajaxParams.deleteimage = true;
                    ajax.call([
                        {
                            methodname: 'theme_snap_cover_image',
                            args: {params: ajaxParams},
                            done: function() {
                                temporalImageURL = 'none';
                                temporalImageID = 0;
                                temporalFileName = '';
                                savedImageURL = 'none';
                                cropper = null;
                                state1();
                                $('#page-header').removeClass('mast-image');
                                $('#page-header .breadcrumb-item a').removeClass('mast-breadcrumb');
                                $('#page-header').data('servercoverfile', 'none');
                                $('#page-header').css('background-image', 'none');
                                confirmationDialogue.hide();
                            },
                            fail: function(response) {
                                ajaxNotify.ifErrorShowBestMsg(response);
                            }
                        }
                    ], true, true);
                });
            });

            $('body').addClass('cover-image-change');
            $('label[for="snap-coverfiles"]').addClass('ajaxing');

            $('#id_snap_cover_image_browser').click(function(e) {
                e.preventDefault();
                showFilepicker('image', fpoptions, filepickerCallback(courseShortName, categoryId), dialogue);
            });
            $('#id_snap_cover_image_save_button').click(function() {
                dialogue.hide();
            });
            $('.snap_cover_image_dialogue .closebutton, .moodle-dialogue-lightbox').click(function() {
                cropper = null;
                state1();
            });
            dialogue.after("visibleChange", function() {
                if ($('#snap-changecoverimageconfirmation .ok').hasClass('ajaxing')) {
                    state2();
                }
                if (!dialogue.get('visible')) {
                    dialogue.destroy(true);
                }
            });
        };

        /**
         * Load the image in the preview box after being uploaded using the file picker.
         * @param {object} params
         * @param {string} courseShortName
         * @param {int} categoryId
         */
        var loadPreviewImage = function(params, courseShortName, categoryId) {

            var image = new Image();
            image.onerror = function() {
                var preview = document.getElementById('id_snap_cover_image_preview');
                preview.setAttribute('style', 'display:none');
            };

            image.onload = function() {
                var input;
                var imageWidth = this.width;
                input = document.getElementById('id_snap_cover_image_preview');
                input.setAttribute('src', params.url);
                input.setAttribute('style', 'display:block');
                $('.snap_cover_image_save_button').removeClass("d-none");

                // Warn if image resolution is too small.
                if (imageWidth < 1024) {
                    $('#snap-alert-cover-image-size').remove();
                    addCoverImageAlert('snap-alert-cover-image-size',
                        M.util.get_string('error:coverimageresolutionlow', 'theme_snap'),
                        'dialogue'
                    );
                } else {
                    $('#snap-alert-cover-image-size').remove();
                }

                if (cropper !== null) {
                    cropper.replace(params.url);
                } else {
                    // Initialize preview image cropper for the uploaded image.
                    var imageToCrop = document.getElementById('id_snap_cover_image_preview');
                    temporalImageURL = params.url;
                    temporalFileName = params.file;
                    if (params.id !== undefined) {
                        temporalImageID = params.id;
                    } else if (params.file !== undefined) {
                        var fileNameWithoutSpaces = params.file.replace(/ .*/, "");
                        var regex = new RegExp("draft\\/(\\d+)\\/" + fileNameWithoutSpaces, "g");
                        var urlId = params.url.match(regex);
                        temporalImageID = urlId[0].match(/\d+/)[0];
                    }
                    cropper = new Cropper(imageToCrop, {
                        viewMode: 3,
                        aspectRatio: cropperRatio,
                        dragMode: "none",
                        zoomable: false,
                        minCropBoxWidth: 300,
                    });
                }
                aspectRatioOptions();
                $('.snap_cover_image_cropper_description').removeClass('d-none');
                $('#id_snap_cover_image_save_button').click(function() {
                    var croppedImage = cropper.getCroppedCanvas().toDataURL("image/png");
                    // Ensure that the page-header in courses has the mast-image class.
                    $('.path-course-view #page-header').addClass('mast-image');
                    $('.path-course-view #page-header .breadcrumb-item a').addClass('mast-breadcrumb');

                    $('#page-header').css('background-image', 'url(' + croppedImage + ')');

                    state2();
                    saveImage(params, courseShortName, categoryId, cropper);
                });

            };
            image.src = params.url;
        };


        /**
         * Callback for file picker.
         * @param {string} courseShortName
         * @param {int} categoryId
         */
        var filepickerCallback = function(courseShortName, categoryId) {
            return function(params) {
            if (params.url !== '') {
                temporalImageURL = params.url;
                temporalFileName = params.file;
                if (params.id !== undefined) {
                    temporalImageID = params.id;
                } else if (params.file !== undefined) {
                    var fileNameWithoutSpaces = params.file.replace(/ .*/, "");
                    var regex = new RegExp("draft\\/(\\d+)\\/" + fileNameWithoutSpaces, "g");
                    var urlId = params.url.match(regex);
                    temporalImageID = urlId[0].match(/\d+/)[0];
                }
                // Load the preview image.
                loadPreviewImage(params, courseShortName, categoryId);
                var ajaxParams = {};
                ajaxParams.fileid = temporalImageID;
                ajaxParams.imagefilename = temporalFileName;
                ajaxParams.contrastvalidation = true;
                if (categoryId !== null) {
                    ajaxParams.categoryid = categoryId;
                } else if (courseShortName !== null) {
                    ajaxParams.courseshortname = courseShortName;
                }
                ajax.call([
                    {
                        methodname: 'theme_snap_cover_image',
                        args: {params: ajaxParams},
                        done: function(response) {
                            $('#snap-alert-cover-image-contrast').remove();
                            if (response.contrast) {
                                addCoverImageAlert('snap-alert-cover-image-contrast', response.contrast,
                                    'dialogue'
                                );
                            }
                        },
                        fail: function(response) {
                            ajaxNotify.ifErrorShowBestMsg(response);
                        }
                    }
                ], true, true);
                }
            };
        };

        /**
         * Create file picker.
         * @param {string} type
         * @param {object} fpoptions
         * @param {Function} callback
         * @param {object} dialogue
         */
        var showFilepicker = function(type, fpoptions, callback, dialogue) {
            Y.use('core_filepicker', function(Y) {
                var options = fpoptions;
                options.formcallback = callback;
                M.core_filepicker.show(Y, options);
            });
            $('.filepicker .closebutton').click(function() {
                dialogue.hide();
                state1();
            });
        };

        /**
         * Save image after confirmation.
         * @param {object} params
         * @param {string} courseShortName
         * @param {int} categoryId
         * @param {object} cropper
         */
        var saveImage = function(params, courseShortName, categoryId, cropper) {

            $('#snap-changecoverimageconfirmation .ok').off('click').click(function() {
                var ajaxParams = {};

                if (categoryId !== null) {
                    ajaxParams.categoryid = categoryId;
                } else if (courseShortName !== null) {
                    ajaxParams.courseshortname = courseShortName;
                } else {
                    return;
                }

                if (params.id !== undefined) {
                    ajaxParams.fileid = params.id;
                } else if (params.file !== undefined) {
                        var fileNameWithoutSpaces = params.file.replace(/ .*/, "");
                        var regex = new RegExp("draft\\/(\\d+)\\/" + fileNameWithoutSpaces, "g");
                        var urlId = params.url.match(regex);
                        ajaxParams.fileid = urlId[0].match(/\d+/)[0];
                } else {
                    ajaxParams.fileid = temporalImageID;
                }

                if (params.file !== undefined) {
                    ajaxParams.imagefilename = params.file;
                } else {
                    ajaxParams.imagefilename = temporalFileName;
                }
                if (params.url !== undefined) {
                    ajaxParams.originalimageurl = params.url;
                } else {
                    ajaxParams.originalimageurl = temporalImageURL;
                }

                cropper.getCroppedCanvas().toBlob((blob) => {
                    var reader = new FileReader();
                    reader.readAsDataURL(blob);
                    reader.onloadend = function() {
                        var imageBase64Data = reader.result;
                        ajaxParams.croppedimagedata = imageBase64Data;
                        ajax.call([
                            {
                                methodname: 'theme_snap_cover_image',
                                args: {params: ajaxParams},
                                done: function(response) {
                                    state1();
                                    if (!response.success && response.warning) {
                                        addCoverImageAlert('snap-alert-cover-image-upload-failed', response.warning);
                                    }
                                    var newUrl = getCroppedImageURL(response.imageurl);
                                    savedImageURL = 'url(' + newUrl + ')';
                                    temporalImageURL = 'none';
                                    temporalImageID = 0;
                                    temporalFileName = '';
                                    $('#page-header').css('background-image', savedImageURL);
                                    $('#page-header').addClass('mast-image');
                                    $('#page-header .breadcrumb-item a').addClass('mast-breadcrumb');
                                    $('#page-header').data('servercoverfile', $('#page-header').css('background-image'));
                                    $('#snap-changecoverimageconfirmation .ok').off("click");

                                },
                                fail: function(response) {
                                    state1();
                                    ajaxNotify.ifErrorShowBestMsg(response);
                                }
                            }
                        ], true, true);
                    };
                });

            });
        };

        /**
         *
         * @param {object} ajaxParams
         * @param {string} courseShortName
         * @param {int} categoryId
         * @param {int} siteMaxBytes
         */
        var coverImage = function(ajaxParams, courseShortName = null, categoryId = null, siteMaxBytes) {

            if (courseShortName === null && categoryId === null) {
                return;
            }

            ajax.call([
                {
                    methodname: 'theme_snap_file_manager_options',
                    args: [],
                    done: function(data) {
                        var fpoptions = JSON.parse(data.fpoptions);
                        // Take a backup of what the current background image url is (if any).
                        $('#page-header').data('servercoverfile', $('#page-header').css('background-image'));
                        $('#snap-coverimagecontrol').addClass('snap-js-enabled');
                        $('#snap-coverfiles').click(function() {
                            moodledialogue(courseShortName, categoryId, fpoptions, siteMaxBytes);
                        });
                        // Cancel button listener
                        $('#snap-changecoverimageconfirmation .cancel').click(function() {
                            moodledialogue(courseShortName, categoryId, fpoptions, siteMaxBytes);
                        });
                    },
                    fail: function() {
                        return;
                    }
                }
            ], true, true);
        };

        /**
         * @param {int} categoryId
         * @param {int} siteMaxBytes
         */
        var categoryCoverImage = function(categoryId, siteMaxBytes) {
            var ajaxParams = {imagefilename: null, imagedata: null, categoryid: categoryId,
                    courseshortname: null};
            coverImage(ajaxParams, null, categoryId, siteMaxBytes);
        };

        /**
         * @param {string} courseShortName
         * @param {int} siteMaxBytes
         */
        var courseCoverImage = function(courseShortName, siteMaxBytes) {
            var ajaxParams = {imagefilename: null, imagedata: null, categoryid: null,
                    courseshortname: courseShortName};

            coverImage(ajaxParams, courseShortName, null, siteMaxBytes);
        };
        return {courseImage: courseCoverImage, categoryImage: categoryCoverImage};
    }
);
