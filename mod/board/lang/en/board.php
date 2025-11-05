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
 * Language strings for mod_board.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['Cancel'] = 'Cancel';
$string['Ok'] = 'Ok';
$string['acceptedfiletypeforbackground'] = 'Accepted filetypes for background images.';
$string['acceptedfiletypeforbackground_desc'] = 'Select the filetypes for background images to be supported.';
$string['acceptedfiletypeforcontent'] = 'Accepted filetypes for content images.';
$string['acceptedfiletypeforcontent_desc'] = 'Select the filetypes for content to be supported.';
$string['acceptedfiletypeforgeneral'] = 'Accepted filetypes for general content files.';
$string['acceptedfiletypeforgeneral_desc'] = 'Type list of allowed file extensions for general content files separated by commas, for example "txt,pdf,docx".';
$string['addcomment'] = 'Add comment';
$string['addrating'] = 'Rating posts';
$string['addrating_all'] = 'by All';
$string['addrating_none'] = 'Disabled';
$string['addrating_students'] = 'by Students';
$string['addrating_teachers'] = 'by Teachers';
$string['allowed_singleuser_modes'] = 'Enabled single user modes';
$string['allowed_singleuser_modes_desc'] = 'Allow/Disallow usage of certain single user modes. Does not affect already created boards';
$string['allowyoutube'] = 'Allow YouTube';
$string['allowyoutube_desc'] = 'If activated a button to add an embedded YouTube Video is supported.';
$string['aria_addmedia'] = 'Add {type} for post {post} from column {column}';
$string['aria_addmedianew'] = 'Add {type} for new post from column {column}';
$string['aria_canceledit'] = 'Cancel post edit for post {post} from column {column}';
$string['aria_cancelnew'] = 'Cancel new post for column {column}';
$string['aria_choosefileedit'] = 'Select file for post {post} from column {column}';
$string['aria_choosefilenew'] = 'Select file for new post from column {column}';
$string['aria_column_locked'] = 'Column {$a} locked';
$string['aria_column_unlocked'] = 'Column {$a} unlocked';
$string['aria_deleteattachment'] = 'Delete attachment for post {post} from column {column}';
$string['aria_deletecolumn'] = 'Delete column {column}';
$string['aria_deletepost'] = 'Delete post {post} from column {column}';
$string['aria_editpost'] = 'Edit post {post}';
$string['aria_movecolumn'] = 'Move column {column}';
$string['aria_movepost'] = 'Move post {post}';
$string['aria_newcolumn'] = 'Add new column';
$string['aria_newpost'] = 'Add new post to column {column}';
$string['aria_postedit'] = 'Save post edit for post {post} from column {column}';
$string['aria_postnew'] = 'Save new post for column {column}';
$string['aria_ratepost'] = 'Rate post {post} from column {column}';
$string['aria_updatecolumn'] = 'Update column {column}';
$string['background_color'] = 'Background color';
$string['background_color_help'] = 'Should be a valid hex colour, such as #00cc99';
$string['background_image'] = 'Background Image';
$string['blanktargetenabled'] = 'This board has been configured to launch all its URL / web links in a new window or tab.';
$string['board:addinstance'] = 'Add a new board resource';
$string['board:deleteallcomments'] = 'View and delete all comments on posts';
$string['board:manageboard'] = 'Manage columns and manage all posts.';
$string['board:managetemplates'] = 'Manage board templates.';
$string['board:post'] = 'Manage own posts and potentially rate posts.';
$string['board:postcomment'] = 'Create and view comments on posts';
$string['board:view'] = 'View board content.';
$string['board_column_locked'] = 'This column is locked and cannot be edited.';
$string['boardhasnotes'] = 'This board already has posts, changing the user mode is not allowed';
$string['boardsettings'] = 'Board settings';
$string['brickfieldlogo'] = 'Powered by Brickfield logo';
$string['cancel_button_text'] = 'Cancel';
$string['choose_file'] = 'Choose Image File';
$string['close_button_text'] = 'Close';
$string['column_colours'] = 'Column Colours';
$string['column_colours_desc'] = 'The colours used at the top of each column. These are hex colors and should be placed once per line as 3 or 6 characters. If any of these values are not equal to a colour then the defaults will be used.';
$string['column_deleted_text'] = 'The column in which you are posting was deleted.';
$string['comment'] = 'Comment';
$string['comments'] = '{$a} Comments';
$string['completiondetail:notes'] = 'Add notes: {$a}';
$string['completionnotes'] = 'Require students this number of notes to complete the activity';
$string['completionnotesgroup'] = 'Require notes';
$string['default_column_heading'] = 'Heading';
$string['delete'] = 'Delete';
$string['deletecomment'] = 'Delete comment';
$string['embed_allowed'] = 'Allow board embedding';
$string['embed_allowed_desc'] = 'If activated, the board embedding feature is available in the activity settings.';
$string['embed_height'] = 'Embed height';
$string['embed_height_desc'] = 'Height to use for the iframe when embedding the board within the course. This should be a valid CSS value, e.g. px, rem, %, etc...';
$string['embed_width'] = 'Embed width';
$string['embed_width_desc'] = 'Width to use for the iframe when embedding the board within the course. This should be a valid CSS value, e.g. px, rem, %, etc...';
$string['embedboard'] = 'Embed the board into the course page';
$string['enableblanktarget'] = 'Enable blank target';
$string['enableblanktarget_help'] = 'When enabled all links will open in a new tab/window.';
$string['error_notemplates'] = 'No templates found.';
$string['event_add_column'] = 'Column added';
$string['event_add_column_desc'] = 'The user with id \'{$a->userid}\' created board column with id \'{$a->objectid}\' and name \'{$a->name}\'.';
$string['event_add_comment'] = 'Comment added';
$string['event_add_comment_desc'] = 'The user with id \'{$a->userid}\' added a comment with id \'{$a->objectid}\', content \'{$a->content}\' on post with id \'{$a->noteid}\'.';
$string['event_add_note'] = 'Post added';
$string['event_add_note_desc'] = 'The user with id \'{$a->userid}\' created board post with id \'{$a->objectid}\', heading \'{$a->heading}\', content \'{$a->content}\', media \'{$a->media}\' on column id \'{$a->columnid}\', group id \'{$a->groupid}\'.';
$string['event_delete_column'] = 'Column deleted';
$string['event_delete_column_desc'] = 'The user with id \'{$a->userid}\' deleted board column with id \'{$a->objectid}\'.';
$string['event_delete_comment'] = 'Comment deleted';
$string['event_delete_comment_desc'] = 'The user with id \'{$a->userid}\' deleted post comment with id \'{$a->objectid}\' from post with id \'{$a->noteid}\'.';
$string['event_delete_note'] = 'Post deleted';
$string['event_delete_note_desc'] = 'The user with id \'{$a->userid}\' deleted board post with id \'{$a->objectid}\' from column id \'{$a->columnid}\'.';
$string['event_move_note'] = 'Post moved';
$string['event_move_note_desc'] = 'The user with id \'{$a->userid}\' moved board post with id \'{$a->objectid}\' to column id \'{$a->columnid}\'.';
$string['event_rate_note'] = 'Post rated';
$string['event_rate_note_desc'] = 'The user with id \'{$a->userid}\' rated board post with id \'{$a->objectid}\' to rating \'{$a->rating}\'.';
$string['event_update_column'] = 'Column updated';
$string['event_update_column_desc'] = 'The user with id \'{$a->userid}\' updated board column with id \'{$a->objectid}\' to \'{$a->name}\'.';
$string['event_update_note'] = 'Post updated';
$string['event_update_note_desc'] = 'The user with id \'{$a->userid}\' updated board post with id \'{$a->objectid}\' to heading \'{$a->heading}\', content \'{$a->content}\', media \'{$a->media}\' on column id \'{$a->columnid}\'.';
$string['export'] = 'Export';
$string['export_backtoboard'] = 'Back to Board';
$string['export_board'] = 'Export Board';
$string['export_comment'] = 'Comment';
$string['export_comments'] = 'Export Comments';
$string['export_comments_description'] = 'Please choose the which comments you would like to export.';
$string['export_comments_include_deleted'] = 'You can choose to export all comments including those that have been deleted.';
$string['export_comments_include_deleted_button'] = 'Export Comments (including deleted)';
$string['export_content'] = 'Text';
$string['export_deleted'] = 'Deleted';
$string['export_email'] = 'Email';
$string['export_firstname'] = 'Firstname';
$string['export_heading'] = 'Post Heading';
$string['export_info'] = 'Media Title';
$string['export_lastname'] = 'Lastname';
$string['export_submissions'] = 'Export Submissions';
$string['export_timecreated'] = 'Date created';
$string['export_url'] = 'Media URL';
$string['form_body'] = 'Content';
$string['form_general_file'] = 'File';
$string['form_image_file'] = 'Image file';
$string['form_mediatype'] = 'Media';
$string['form_title'] = 'Post title';
$string['hideheaders'] = 'Hide column headers from students';
$string['hidename'] = 'Hide embedded board name (needed on some themes)';
$string['history'] = 'Board history';
$string['history_refresh'] = 'Board refresh timer';
$string['history_refresh_desc'] = 'Timeout in seconds between automatic board refreshes. If set to 0 or empty then the board will only refresh during board actions (add/update/etc)';
$string['historyinfo'] = 'The Board history table is only used to store temporary records, which are used by javascript processes to refresh board views, and are then deleted immediately.';
$string['include_deleted'] = 'Include deleted';
$string['invalid_file_extension'] = 'File extension not accepted for upload.';
$string['invalid_file_size_max'] = 'File size too big to be accepted.';
$string['invalid_file_size_min'] = 'File size too small to be accepted.';
$string['invalid_youtube_url'] = 'Invalid YouTube URL';
$string['limited_markdown_checkbox'] = 'Show content formatting examples';
$string['limited_markdown_examples'] = '<pre>
# This is a heading

This is a regular paragraph spanning several
lines. Paragraphs are separated by an empty line.

- list item with *italic* text
- list item with **bold** text
- list item with ***bold and italic*** text

1. first numbered list item
2. second numbered list item
</pre>';
$string['media_selection'] = 'Media selection';
$string['media_selection_buttons'] = 'Buttons';
$string['media_selection_desc'] = 'Configure how the media selection for posts will be displayed as.';
$string['media_selection_dropdown'] = 'Dropdown';
$string['modal_title_edit'] = 'Edit post for column {column}';
$string['modal_title_new'] = 'New post for column {column}';
$string['modulename'] = 'Board';
$string['modulename_help'] = 'The board activity enables students to engage in discussions and thought-sharing anonymously, via a post-it board.

Students can create posts to display content, such as text, images, URL links, or embedded videos.

The teacher can create boards with a number of different configurations, including:

* Rating posts.
* Commenting on posts.
* Group or single-user modes.
* Completion options.
* Teacher download options.

Boards may be used

* As introductions / icebreakers.
* As crowdsourcing content.
* As discussion whiteboards.
* For teacher feedback on sessions.';
$string['modulenameplural'] = 'Boards';
$string['move_column_to_aftercolumn'] = 'Move column after column {$a}';
$string['move_column_to_firstplace'] = 'Move column to first place';
$string['move_to_afterpost'] = 'Move after post {$a}';
$string['move_to_firstitemcolumn'] = 'Move to column {$a}';
$string['new_column_icon'] = 'New column icon';
$string['new_column_icon_desc'] = 'Icon displayed on the new button for columns.';
$string['new_note_icon'] = 'New post icon';
$string['new_note_icon_desc'] = 'Icon displayed on the new button for posts.';
$string['nopermission'] = 'You do not have permission to view this board.';
$string['note_changed_text'] = "The post you are editing has changed.";
$string['note_changed_title'] = 'Confirm';
$string['note_deleted_text'] = 'The post you were editing was deleted.';
$string['nousers'] = 'This Board activity has no users enrolled';
$string['opensinnewwindow'] = 'Opens in new window';
$string['option_empty'] = 'None';
$string['option_file'] = 'File';
$string['option_image'] = 'Image';
$string['option_image_info'] = 'Image title';
$string['option_image_url'] = 'Image URL';
$string['option_link'] = 'Link';
$string['option_link_info'] = 'Link title';
$string['option_link_url'] = 'Link URL';
$string['option_youtube'] = 'Video (YouTube)';
$string['option_youtube_info'] = 'Video title';
$string['option_youtube_url'] = 'YouTube URL';
$string['pluginadministration'] = 'Board module administration';
$string['pluginname'] = 'Board';
$string['post_button_text'] = 'Post';
$string['post_max_length'] = 'Post maximum length';
$string['post_max_length_desc'] = 'The maximum allowed content length. Anything over this length will be trimmed.';
$string['postbydate'] = 'Post by date';
$string['postbyenabled'] = 'Limit students posting by date';
$string['posts'] = 'Posts';
$string['privacy:metadata:board_comments'] = 'Comments for each board post.';
$string['privacy:metadata:board_comments:content'] = 'The content of the comment on the post';
$string['privacy:metadata:board_comments:noteid'] = 'The ID of the related post';
$string['privacy:metadata:board_comments:timecreated'] = 'The time when the post comment was created';
$string['privacy:metadata:board_comments:userid'] = 'The ID of the user who added the comment on the post';
$string['privacy:metadata:board_history'] = 'Temporary board history records information, used by javascript processes to refresh board views, and then deleted immediately.';
$string['privacy:metadata:board_history:action'] = 'The action performed';
$string['privacy:metadata:board_history:boardid'] = 'The ID of the board';
$string['privacy:metadata:board_history:content'] = 'The JSON data of the action performed';
$string['privacy:metadata:board_history:timecreated'] = 'The time the action was performed';
$string['privacy:metadata:board_history:userid'] = 'The ID of the user who performed the action';
$string['privacy:metadata:board_note_ratings'] = 'Information about the individual ratings for each board post.';
$string['privacy:metadata:board_note_ratings:noteid'] = 'The ID of the related post';
$string['privacy:metadata:board_note_ratings:timecreated'] = 'The time when the post rating was created';
$string['privacy:metadata:board_note_ratings:userid'] = 'The ID of the user who created the post rating';
$string['privacy:metadata:board_notes'] = 'Information about the individual posts for each board.';
$string['privacy:metadata:board_notes:columnid'] = 'The column location of the post';
$string['privacy:metadata:board_notes:content'] = 'The content of the post';
$string['privacy:metadata:board_notes:heading'] = 'The heading of the post';
$string['privacy:metadata:board_notes:info'] = 'The media information of the post';
$string['privacy:metadata:board_notes:timecreated'] = 'The time when the post was created';
$string['privacy:metadata:board_notes:url'] = 'The media URL of the post';
$string['privacy:metadata:board_notes:userid'] = 'The ID of the user who created the post';
$string['privacystatement'] = '<strong>Board Privacy Statement:</strong> Please note that editing teachers, and those with appropriate permissions, are able to view exported data as to which users have created these Board posts and comments.';
$string['rate_note_text'] = 'Are you sure you want to rate this post?';
$string['rate_note_title'] = "Confirm";
$string['rate_remove_note_text'] = 'Are you sure you want to remove the rating for this post?';
$string['ratings'] = 'Ratings';
$string['remove_column_text'] = 'Are you sure you want to delete this "{$a}" column and all the posts it contains?';
$string['remove_column_title'] = 'Confirm';
$string['remove_note_text'] = "Are you sure you want to delete this post and all the data it contains, as this will affect all other users as well?";
$string['remove_note_title'] = 'Confirm';
$string['selectuser'] = 'Select user';
$string['selectuserplease'] = 'Please select a user';
$string['settings'] = 'Board settings';
$string['settings:addattachmenttolog'] = 'Add attachment to log';
$string['settings:addcolumnnametolog'] = 'Add column name to log';
$string['settings:addcommenttolog'] = 'Add comment to log';
$string['settings:addheadingtolog'] = 'Add heading to log';
$string['settings:addnotetolog'] = 'Add note to log';
$string['settings:addratingtolog'] = 'Add rating to log';
$string['settings:enableprivacystatement'] = 'Enable privacy statement';
$string['settings:enableprivacystatement_desc'] = 'Show privacy statement as part of Board description.';
$string['settings_heading_logging'] = 'Logging';
$string['settings_heading_logging_info'] = 'By default, logging will contain content, such as the text of a note, comments, column names, and so on.<br>For data privacy purposes, logging may be configured to exclude some information that has been submitted by users.';
$string['singleusermode'] = 'Single user mode';
$string['singleusermode_desc'] = 'In single user users can only add post on their own board, if private users can not view the boards of other users, if public user boards are available through a dropdown.';
$string['singleusermodenone'] = 'Disabled';
$string['singleusermodenotembed'] = 'Board does not allow a single user board to be embedded. Please change your settings.';
$string['singleusermodeprivate'] = 'Single user mode (private)';
$string['singleusermodepublic'] = 'Single user mode (public)';
$string['sortby'] = 'Sort by';
$string['sortbydate'] = 'Creation date';
$string['sortbynone'] = 'None';
$string['sortbyrating'] = 'Rating';
$string['template'] = 'Board template';
$string['template_apply'] = 'Apply template';
$string['template_columns'] = 'Columns';
$string['template_create'] = 'Add template';
$string['template_delete'] = 'Delete template';
$string['template_description'] = 'Template description';
$string['template_export'] = 'Export template';
$string['template_import'] = 'Import template';
$string['template_update'] = 'Update template';
$string['templates'] = 'Board templates';
$string['userscanedit'] = 'Allow all users to edit the placement of their own posts.';
$string['viewboard'] = 'View board activity';
$string['warning'] = 'Notification';
