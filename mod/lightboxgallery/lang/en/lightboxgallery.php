<?php
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
 * English strings for the lightboxgallery module
 *
 * @package   mod_lightboxgallery
 * @copyright 2011 John Kelsh
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['acceptablefiletypebriefing'] = 'If you wish to upload multiple files at a time, you can submit a zip file with images inside it and all valid images inside the zip archive will be added to the gallery.';
$string['addcomment'] = 'Add comment';
$string['addimage'] = 'Add images';
$string['addimage_help'] = 'Browse for an image on your local machine to add to the current gallery.

You can also select a zip archive containing multiple images, which will be extracted into the image directory after being uploaded.';
$string['autoresize'] = 'Automatically resize';
$string['autoresize_help'] = 'You can control if and how gallery images are resized. The following methods are available when configuring a gallery:

* Screen: images that are bigger than the users screen will be scaled down to fit inside the screen.
* Upload: images will be resized to specified dimensions when they are uploaded through the \'Add image\' option.

There is also an image resizing plugin included in the image editor, where you can manually resize images.';
$string['allowcomments'] = 'Allow comments';
$string['allowrss'] = 'Allow RSS feeds';
$string['allpluginsdisabled'] = 'Sorry, all the editing plugins are currently disabled.';
$string['backtogallery'] = 'Back to Gallery';
$string['captionfull'] = 'Display full caption text?';
$string['captionpos'] = 'Caption Position';
$string['commentadded'] = 'Your comment has been posted to the gallery';
$string['commentcount'] = '{$a} comments';
$string['commentdelete'] = 'Confirm comment deletion?';
$string['configdisabledplugins'] = 'Disabled plugins';
$string['configdisabledpluginsdesc'] = 'Select the image editing plugins you want to disable.';
$string['configenablerssfeeds'] = 'Enable RSS feeds';
$string['configenablerssfeedsdesc'] = 'Allow RSS feeds to be generated from galleries.';
$string['configimagelifetime'] = 'Image lifetime';
$string['configimagelifetimedesc'] = 'Length of time (in seconds) for images to remain in the browser cache.';
$string['configoverwritefiles'] = 'Overwrite files';
$string['configoverwritefilesdesc'] = 'Overwrite images when new images are uploaded with the same filename.';
$string['configstrictfilenames'] = 'Use strict filenames';
$string['configstrictfilenamesdesc'] = 'Force image editor to clean file names according to Moodle naming rules.';
$string['currentsize'] = 'Current size';
$string['dimensions'] = 'Dimensions';
$string['dirup'] = 'Up';
$string['dirdown'] = 'Down';
$string['dirleft'] = 'Left';
$string['dirright'] = 'Right';
$string['displayinggallery'] = 'Showing gallery: {$a}';
$string['editimage'] = 'Edit image';
$string['edit_choose'] = 'Choose...';
$string['edit_caption'] = 'Caption';
$string['edit_crop'] = 'Crop';
$string['edit_delete'] = 'Delete';
$string['edit_flip'] = 'Flip';
$string['edit_resize'] = 'Resize';
$string['edit_resizescale'] = 'Scale';
$string['edit_rotate'] = 'Rotate';
$string['edit_tag'] = 'Tag';
$string['edit_thumbnail'] = 'Thumbnail';
$string['errornofile'] = 'The requested file was not found: {$a}';
$string['errornoimages'] = 'No images were found in this gallery';
$string['errornosearchresults'] = 'Your search query returned no images';
$string['erroruploadimage'] = 'The file you upload must be an image file';
$string['eventgallerycommentcreated'] = 'Comment created';
$string['eventgallerysearched'] = 'Gallery searched';
$string['eventimageupdated'] = 'Image updated';
$string['eventviewed'] = 'Lightbox gallery viewed';
$string['extendedinfo'] = 'Show extended image info';
$string['imageadd'] = 'Add images';
$string['imagecount'] = 'Image count';
$string['imagecounta'] = '{$a} images';
$string['imagedirectory'] = 'Image directory';
$string['imagedirectory_help'] = 'Select the directory that contains the images you want to display in the gallery. When using the \'Add image\' gallery option, uploaded images will be places in this directory.';
$string['imagedownload'] = 'Download image';
$string['imageresized'] = 'Image resized: {$a}';
$string['images'] = 'Images';
$string['imagesperpage'] = 'Images per page';
$string['imagesperrow'] = 'Images per row';
$string['imageuploaded'] = 'Uploaded image: {$a}';
$string['invalidlightboxgalleryid'] = 'Invalid lightboxgallery ID';
$string['lightboxgallery'] = 'Lightbox Gallery';
$string['lightboxgallery:addcomment'] = 'Add comment to lightbox gallery';
$string['lightboxgallery:addinstance'] = 'Add a new lightbox gallery';
$string['lightboxgallery:addimage'] = 'Add image to lightbox gallery';
$string['lightboxgallery:edit'] = 'Edit a lightbox gallery';
$string['lightboxgallery:submit'] = 'Submit a lightbox gallery';
$string['lightboxgallery:viewcomments'] = 'View lightbox gallery comments';
$string['makepublic'] = 'Make public';
$string['metadata'] = 'Meta data';
$string['modulename'] = 'Lightbox Gallery';
$string['modulename_help'] = 'The Lightbox Gallery resource module enables participants to view a gallery of images.

This resource allows you to create \'Lightbox\' enabled image galleries within your Moodle course.

As a course teacher, you are able to create, edit and delete galleries. Small thumbnails will then be generated, which are used for the thumbnail view of the gallery.
Clicking on any of the thumbnails brings that image into focus, and allows you to scroll through the gallery at your leisure. Using the Lightbox scripts creates nice transition effects when loading and scrolling through the images.

If enabled, users are able to leave comments on your gallery.';
$string['modulenameplural'] = 'Lightbox Galleries';
$string['modulenameshort'] = 'Gallery';
$string['modulenameadd'] = 'Lightbox gallery';
$string['newgallerycomments'] = 'New gallery comments';
$string['nocomments'] = 'No comments';
$string['norssfeedavailable'] = 'Feed not available';
$string['position_bottom'] = 'Bottom';
$string['position_top'] = 'Top';
$string['pluginadministration'] = 'Lightbox Gallery administration';
$string['pluginname'] = 'Lightbox Gallery';
$string['privacy:metadata:lightboxgallery_comments'] = 'Information about the user\'s comments on a given lightboxgallery activity';
$string['privacy:metadata:lightboxgallery_comments:commenttext'] = 'Stores the text of the comment.';
$string['privacy:metadata:lightboxgallery_comments:gallery'] = 'The ID of the lightboxgallery activity the user is providing a comment for.';
$string['privacy:metadata:lightboxgallery_comments:userid'] = 'The user who made the comment.';
$string['privacy:metadata:lightboxgallery_comments:timemodified'] = 'The timestamp indicating when the lightboxgallery comment was modified by the user.';
$string['resizeto'] = 'Resize to';
$string['rsssubscribe'] = 'Gallery RSS feed';
$string['saveimage'] = 'Save {$a}';
$string['screen'] = 'Screen';
$string['search:activity'] = 'Lightboxgallery - activity information';
$string['selectflipmode'] = 'Select flip mode';
$string['selectrotation'] = 'Select rotation angle';
$string['selectthumbpos'] = 'Thumbnail offset (from centre)';
$string['setasindex'] = 'Set as index image';
$string['showall'] = 'Show all';
$string['tagscurrent'] = 'Current tags';
$string['tagsdisabled'] = 'Tagging editor is disabled';
$string['tagsimport'] = 'Import tags';
$string['tagsimportconfirm'] = 'Are you sure you want to import tags from every image in this gallery?';
$string['tagsimportfinish'] = 'Finished importing {$a->tags} tags from {$a->images} images';
$string['tagsiptc'] = 'IPTC tags';
$string['tagspopular'] = 'Popular tags';
$string['tagsrelated'] = 'Related tags';
$string['thumbnailoffset'] = 'Offset';
$string['zipextracted'] = 'Zip file extracted: {$a}';
$string['zipnonewfiles'] = 'No new images were found - make sure images are in the root directory of the archive';
