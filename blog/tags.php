<?php
require_once('../config.php');

$action = optional_param('action','',PARAM_ALPHA);

require_login();

if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

if (isguest()) {
    error(get_string('noguestpost', 'blog'));
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);

$error = '';

switch ($action) {
    case 'addofficial':
        // only approved uses can add official tags
        if (!has_capability('moodle/blog:manageofficialtags', $sitecontext)) {
            error('Can not add official tags tags');
        }
        if (data_submitted() and confirm_sesskey()) {
            $otag = trim(required_param('otag', PARAM_NOTAGS));

            if (get_record('tags', 'text', $otag)) {
                $error = get_string('tagalready');
                break;
            }
            $tag = new object();
            $tag->userid = $USER->id;
            $tag->text   = $otag;
            $tag->type   = 'official';
            if (!$tagid = insert_record('tags', $tag)) {
                error('Can not create tag!');
            }

            /// Write newly added tags back into window opener.
            echo '<script language="JavaScript" type="text/javascript">
            var o = opener.document.createElement("option");
            o.innerHTML = "<option>'.$tag->text.'</option>";
            o.value = '.$tagid.';
            opener.document.entry[\'otags[]\'].insertBefore(o, null);
            </script>';
        }

    break;

    case 'addpersonal':
        /// Everyone can add personal tags as long as they can write blog entries.
        if (!has_capability('moodle/blog:manageofficialtags', $sitecontext)
          and !has_capability('moodle/blog:create', $sitecontext)) {
            error('Can not add personal tags');
        }
        if (data_submitted() and confirm_sesskey()) {
            $ptag = trim(required_param('ptag', PARAM_NOTAGS));

            if (get_record('tags', 'text', $ptag)) {
                $error = get_string('tagalready');
                break;
            }
            $tag = new object();
            $tag->userid = $USER->id;
            $tag->text   = $ptag;
            $tag->type   = 'personal';
            if (!$tagid = insert_record('tags', $tag)) {
                error('Can not create tag!');
            }

            /// Write newly added tags back into window opener.
            echo '<script language="JavaScript" type="text/javascript">
            var o = opener.document.createElement("option");
            o.innerHTML = "<option>'.$tag->text.'</option>";
            o.value = '.$tagid.';
            opener.document.entry[\'ptags[]\'].insertBefore(o, null);
            </script>';
        }

    break;

    case 'delete':
        /// Delete a tag.
        if (data_submitted() and confirm_sesskey()) {
            $tagids = optional_param('tags', array(), PARAM_INT);

            if (empty($tagids) or !is_array($tagids)) {
                // TODO add error message here
                // $error = 'no data selected';
                break;
            }

            foreach ($tagids as $tagid) {

                if (!$tag = get_record('tags', 'id', $tagid)) {
                    continue; // page refreshed?
                }

                if ($tag->type == 'official' and !has_capability('moodle/blog:manageofficialtags', $sitecontext)) {
                    //can not delete
                    continue;
                }

                if ($tag->type == 'personal') {
                    if (has_capability('moodle/blog:managepersonaltags', $sitecontext)) {
                        //ok - can delete any personal tag
                    } else if (!has_capability('moodle/blog:create', $sitecontext) or $USER->id != $tag->userid) {
                        // no delete - you must own the tag and be able to create blog entries
                        continue;
                    }
                }


                if (!delete_records('tags', 'id', $tagid)) {
                    error('Can not delete tag');
                }
                if (!delete_records('blog_tag_instance', 'tagid', $tagid)) {
                    error('Can not delete blog tag instances');
                }

                /// Remove parent window option via javascript.
                echo '<script>
                var i=0;
                while (i < window.opener.document.entry[\'otags[]\'].length) {
                    if (window.opener.document.entry[\'otags[]\'].options[i].value == '.$tagid.') {
                        window.opener.document.entry[\'otags[]\'].removeChild(opener.document.entry[\'otags[]\'].options[i]);
                    }
                    i++;
                }

                var i=0;
                while (i < window.opener.document.entry[\'ptags[]\'].length) {
                    if (window.opener.document.entry[\'ptags[]\'].options[i].value == '.$tagid.') {
                        window.opener.document.entry[\'ptags[]\'].removeChild(opener.document.entry[\'ptags[]\'].options[i]);
                    }
                    i++;
                }

                </script>';
            }

        }
    break;

    default:
        /// Just display the tags form.
    break;
}


/// Print the table.
print_header();
include_once('tags.html');
print_footer();


?>
