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
 * Renderer for use with the badges output
 *
 * @package    core
 * @subpackage badges
 * @copyright  2012 onwards Totara Learning Solutions Ltd {@link http://www.totaralms.com/}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author     Yuliya Bozhko <yuliya.bozhko@totaralms.com>
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/badgeslib.php');
require_once($CFG->libdir . '/tablelib.php');

/**
 * Standard HTML output renderer for badges
 */
class core_badges_renderer extends plugin_renderer_base {

    // Outputs badges list.
    public function print_badges_list($badges, $userid, $profile = false, $external = false) {
        global $USER, $CFG;
        foreach ($badges as $badge) {
            if (!$external) {
                $context = ($badge->type == BADGE_TYPE_SITE) ? context_system::instance() : context_course::instance($badge->courseid);
                $bname = $badge->name;
                $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f3', false);
            } else {
                $bname = '';
                $imageurl = '';
                if (!empty($badge->name)) {
                    $bname = s($badge->name);
                }
                if (!empty($badge->image)) {
                    if (is_object($badge->image)) {
                        if (!empty($badge->image->caption)) {
                            $badge->imagecaption = $badge->image->caption;
                        }
                        $imageurl = $badge->image->id;
                    } else {
                        $imageurl = $badge->image;
                    }
                }
                if (isset($badge->assertion->badge->name)) {
                    $bname = s($badge->assertion->badge->name);
                }
                if (isset($badge->imageUrl)) {
                    $imageurl = $badge->imageUrl;
                }
            }

            $name = html_writer::tag('span', $bname, array('class' => 'badge-name'));

            $imagecaption = $badge->imagecaption ?? '';
            $image = html_writer::empty_tag('img', ['src' => $imageurl, 'class' => 'badge-image', 'alt' => $imagecaption]);
            if (!empty($badge->dateexpire) && $badge->dateexpire < time()) {
                $image .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'badges', userdate($badge->dateexpire)),
                        'moodle',
                        array('class' => 'expireimage'));
                $name .= '(' . get_string('expired', 'badges') . ')';
            }

            $download = $status = $push = '';
            if (($userid == $USER->id) && !$profile) {
                $params = array(
                    'download' => $badge->id,
                    'hash' => $badge->uniquehash,
                    'sesskey' => sesskey()
                );
                $url = new moodle_url(
                    'mybadges.php',
                    $params
                );
                $notexpiredbadge = (empty($badge->dateexpire) || $badge->dateexpire > time());
                $userbackpack = badges_get_user_backpack();
                if (!empty($CFG->badges_allowexternalbackpack) && $notexpiredbadge && $userbackpack) {
                    $assertion = new moodle_url('/badges/assertion.php', array('b' => $badge->uniquehash));
                    $icon = new pix_icon('t/backpack', get_string('addtobackpack', 'badges'));
                    if (badges_open_badges_backpack_api($userbackpack->id) == OPEN_BADGES_V2) {
                        $addurl = new moodle_url('/badges/backpack-add.php', array('hash' => $badge->uniquehash));
                        $push = $this->output->action_icon($addurl, $icon);
                    } else if (badges_open_badges_backpack_api($userbackpack->id) == OPEN_BADGES_V2P1) {
                        $addurl = new moodle_url('/badges/backpack-export.php', array('hash' => $badge->uniquehash));
                        $push = $this->output->action_icon($addurl, $icon);
                    }
                }

                $download = $this->output->action_icon($url, new pix_icon('t/download', get_string('download')));
                if ($badge->visible) {
                    $url = new moodle_url('mybadges.php', array('hide' => $badge->issuedid, 'sesskey' => sesskey()));
                    $status = $this->output->action_icon($url, new pix_icon('t/hide', get_string('makeprivate', 'badges')));
                } else {
                    $url = new moodle_url('mybadges.php', array('show' => $badge->issuedid, 'sesskey' => sesskey()));
                    $status = $this->output->action_icon($url, new pix_icon('t/show', get_string('makepublic', 'badges')));
                }
            }

            if (!$profile) {
                $url = new moodle_url('badge.php', array('hash' => $badge->uniquehash));
            } else {
                if (!$external) {
                    $url = new moodle_url('/badges/badge.php', array('hash' => $badge->uniquehash));
                } else {
                    $hash = hash('md5', $badge->hostedUrl);
                    $url = new moodle_url('/badges/external.php', array('hash' => $hash, 'user' => $userid));
                }
            }
            $actions = html_writer::tag('div', $push . $download . $status, array('class' => 'badge-actions'));
            $items[] = html_writer::link($url, $image . $actions . $name, array('title' => $bname));
        }

        return html_writer::alist($items, array('class' => 'badges'));
    }

    // Recipients selection form.
    public function recipients_selection_form(user_selector_base $existinguc, user_selector_base $potentialuc) {
        $output = '';
        $formattributes = array();
        $formattributes['id'] = 'recipientform';
        $formattributes['action'] = $this->page->url;
        $formattributes['method'] = 'post';
        $output .= html_writer::start_tag('form', $formattributes);
        $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));

        $existingcell = new html_table_cell();
        $existingcell->text = $existinguc->display(true);
        $existingcell->attributes['class'] = 'existing';
        $actioncell = new html_table_cell();
        $actioncell->text  = html_writer::start_tag('div', array());
        $actioncell->text .= html_writer::empty_tag('input', array(
                    'type' => 'submit',
                    'name' => 'award',
                    'value' => $this->output->larrow() . ' ' . get_string('award', 'badges'),
                    'class' => 'actionbutton btn btn-secondary')
                );
        $actioncell->text .= html_writer::empty_tag('input', array(
                    'type' => 'submit',
                    'name' => 'revoke',
                    'value' => get_string('revoke', 'badges') . ' ' . $this->output->rarrow(),
                    'class' => 'actionbutton btn btn-secondary')
                );
        $actioncell->text .= html_writer::end_tag('div', array());
        $actioncell->attributes['class'] = 'actions';
        $potentialcell = new html_table_cell();
        $potentialcell->text = $potentialuc->display(true);
        $potentialcell->attributes['class'] = 'potential';

        $table = new html_table();
        $table->attributes['class'] = 'recipienttable boxaligncenter';
        $table->data = array(new html_table_row(array($existingcell, $actioncell, $potentialcell)));
        $output .= html_writer::table($table);

        $output .= html_writer::end_tag('form');
        return $output;
    }

    // Prints a badge overview infomation.
    public function print_badge_overview($badge, $context) {
        $display = "";
        $languages = get_string_manager()->get_list_of_languages();

        // Badge details.
        $display .= $this->heading(get_string('badgedetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('name')] = $badge->name;
        $dl[get_string('version', 'badges')] = $badge->version;
        $dl[get_string('language')] = $languages[$badge->language];
        $dl[get_string('description', 'badges')] = $badge->description;
        $dl[get_string('createdon', 'search')] = userdate($badge->timecreated);
        $dl[get_string('badgeimage', 'badges')] = print_badge_image($badge, $context, 'large');
        $dl[get_string('imageauthorname', 'badges')] = $badge->imageauthorname;
        $dl[get_string('imageauthoremail', 'badges')] =
            html_writer::tag('a', $badge->imageauthoremail, array('href' => 'mailto:' . $badge->imageauthoremail));
        $dl[get_string('imageauthorurl', 'badges')] =
            html_writer::link($badge->imageauthorurl, $badge->imageauthorurl, array('target' => '_blank'));
        $dl[get_string('imagecaption', 'badges')] = $badge->imagecaption;
        $display .= $this->definition_list($dl);

        // Issuer details.
        $display .= $this->heading(get_string('issuerdetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('issuername', 'badges')] = $badge->issuername;
        $dl[get_string('contact', 'badges')] = html_writer::tag('a', $badge->issuercontact, array('href' => 'mailto:' . $badge->issuercontact));
        $display .= $this->definition_list($dl);

        // Issuance details if any.
        $display .= $this->heading(get_string('issuancedetails', 'badges'), 3);
        if ($badge->can_expire()) {
            if ($badge->expiredate) {
                $display .= get_string('expiredate', 'badges', userdate($badge->expiredate));
            } else if ($badge->expireperiod) {
                if ($badge->expireperiod < 60) {
                    $display .= get_string('expireperiods', 'badges', round($badge->expireperiod, 2));
                } else if ($badge->expireperiod < 60 * 60) {
                    $display .= get_string('expireperiodm', 'badges', round($badge->expireperiod / 60, 2));
                } else if ($badge->expireperiod < 60 * 60 * 24) {
                    $display .= get_string('expireperiodh', 'badges', round($badge->expireperiod / 60 / 60, 2));
                } else {
                    $display .= get_string('expireperiod', 'badges', round($badge->expireperiod / 60 / 60 / 24, 2));
                }
            }
        } else {
            $display .= get_string('noexpiry', 'badges');
        }

        // Criteria details if any.
        $display .= $this->heading(get_string('bcriteria', 'badges'), 3);
        if ($badge->has_criteria()) {
            $display .= self::print_badge_criteria($badge);
        } else {
            $display .= get_string('nocriteria', 'badges');
            if (has_capability('moodle/badges:configurecriteria', $context)) {
                $display .= $this->output->single_button(
                    new moodle_url('/badges/criteria.php', array('id' => $badge->id)),
                    get_string('addcriteria', 'badges'), 'POST', array('class' => 'activatebadge'));
            }
        }

        // Awards details if any.
        if (has_capability('moodle/badges:viewawarded', $context)) {
            $display .= $this->heading(get_string('awards', 'badges'), 3);
            if ($badge->has_awards()) {
                $url = new moodle_url('/badges/recipients.php', array('id' => $badge->id));
                $a = new stdClass();
                $a->link = $url->out();
                $a->count = count($badge->get_awards());
                $display .= get_string('numawards', 'badges', $a);
            } else {
                $display .= get_string('noawards', 'badges');
            }

            if (has_capability('moodle/badges:awardbadge', $context) &&
                $badge->has_manual_award_criteria() &&
                $badge->is_active()) {
                $display .= $this->output->single_button(
                        new moodle_url('/badges/award.php', array('id' => $badge->id)),
                        get_string('award', 'badges'), 'POST', array('class' => 'activatebadge'));
            }
        }

        $display .= self::print_badge_endorsement($badge);
        $display .= self::print_badge_related($badge);
        $display .= self::print_badge_alignments($badge);

        return html_writer::div($display, null, array('id' => 'badge-overview'));
    }

    // Prints action icons for the badge.
    public function print_badge_table_actions($badge, $context) {
        $actions = "";

        if (has_capability('moodle/badges:configuredetails', $context) && $badge->has_criteria()) {
            // Activate/deactivate badge.
            if ($badge->status == BADGE_STATUS_INACTIVE || $badge->status == BADGE_STATUS_INACTIVE_LOCKED) {
                // "Activate" will go to another page and ask for confirmation.
                $url = new moodle_url('/badges/action.php');
                $url->param('id', $badge->id);
                $url->param('activate', true);
                $url->param('sesskey', sesskey());
                $return = new moodle_url(qualified_me());
                $url->param('return', $return->out_as_local_url(false));
                $actions .= $this->output->action_icon($url, new pix_icon('t/show', get_string('activate', 'badges'))) . " ";
            } else {
                $url = new moodle_url(qualified_me());
                $url->param('lock', $badge->id);
                $url->param('sesskey', sesskey());
                $actions .= $this->output->action_icon($url, new pix_icon('t/hide', get_string('deactivate', 'badges'))) . " ";
            }
        }

        // Award badge manually.
        if ($badge->has_manual_award_criteria() &&
                has_capability('moodle/badges:awardbadge', $context) &&
                $badge->is_active()) {
            $url = new moodle_url('/badges/award.php', array('id' => $badge->id));
            $actions .= $this->output->action_icon($url, new pix_icon('t/award', get_string('award', 'badges'))) . " ";
        }

        // Edit badge.
        if (has_capability('moodle/badges:configuredetails', $context)) {
            $url = new moodle_url('/badges/edit.php', array('id' => $badge->id, 'action' => 'badge'));
            $actions .= $this->output->action_icon($url, new pix_icon('t/edit', get_string('edit'))) . " ";
        }

        // Duplicate badge.
        if (has_capability('moodle/badges:createbadge', $context)) {
            $url = new moodle_url('/badges/action.php', array('copy' => '1', 'id' => $badge->id, 'sesskey' => sesskey()));
            $actions .= $this->output->action_icon($url, new pix_icon('t/copy', get_string('copy'))) . " ";
        }

        // Delete badge.
        if (has_capability('moodle/badges:deletebadge', $context)) {
            $url = new moodle_url(qualified_me());
            $url->param('delete', $badge->id);
            $actions .= $this->output->action_icon($url, new pix_icon('t/delete', get_string('delete'))) . " ";
        }

        return $actions;
    }

    /**
     * Render an issued badge.
     *
     * @param \core_badges\output\issued_badge $ibadge
     * @return string
     */
    protected function render_issued_badge(\core_badges\output\issued_badge $ibadge) {
        $data = $ibadge->export_for_template($this);
        return parent::render_from_template('core_badges/issued_badge', $data);
    }

    /**
     * Render an issued badge.
     *
     * @param \core_badges\output\badgeclass $badge
     * @return string
     */
    protected function render_badgeclass(\core_badges\output\badgeclass $badge) {
        $data = $badge->export_for_template($this);
        return parent::render_from_template('core_badges/issued_badge', $data);
    }

    /**
     * Render an external badge.
     *
     * @param \core_badges\output\external_badge $ibadge
     * @return string
     */
    protected function render_external_badge(\core_badges\output\external_badge $ibadge) {
        $data = $ibadge->export_for_template($this);
        return parent::render_from_template('core_badges/issued_badge', $data);
    }

    /**
     * Render a collection of user badges.
     *
     * @param \core_badges\output\badge_user_collection $badges
     * @return string
     */
    protected function render_badge_user_collection(\core_badges\output\badge_user_collection $badges) {
        global $CFG, $USER, $SITE;
        $backpack = $badges->backpack;
        $mybackpack = new moodle_url('/badges/mybackpack.php');

        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);

        // Set backpack connection string.
        $backpackconnect = '';
        if (!empty($CFG->badges_allowexternalbackpack) && is_null($backpack)) {
            $backpackconnect = $this->output->box(get_string('localconnectto', 'badges', $mybackpack->out()), 'noticebox');
        }
        // Search box.
        $searchform = $this->output->box($this->helper_search_form($badges->search), 'boxwidthwide boxaligncenter');

        // Download all button.
        $actionhtml = $this->output->single_button(
                    new moodle_url('/badges/mybadges.php', array('downloadall' => true, 'sesskey' => sesskey())),
                    get_string('downloadall'), 'POST', array('class' => 'activatebadge'));
        $downloadall = $this->output->box('', 'col-md-3');
        $downloadall .= $this->output->box($actionhtml, 'col-md-9');
        $downloadall = $this->output->box($downloadall, 'row ml-5');

        // Local badges.
        $localhtml = html_writer::start_tag('div', array('id' => 'issued-badge-table', 'class' => 'generalbox'));
        $sitename = format_string($SITE->fullname, true, array('context' => context_system::instance()));
        $heading = get_string('localbadges', 'badges', $sitename);
        $localhtml .= $this->output->heading_with_help($heading, 'localbadgesh', 'badges');
        if ($badges->badges) {
            $countmessage = $this->output->box(get_string('badgesearned', 'badges', $badges->totalcount));

            $htmllist = $this->print_badges_list($badges->badges, $USER->id);
            $localhtml .= $backpackconnect . $countmessage . $searchform;
            $localhtml .= $htmlpagingbar . $htmllist . $htmlpagingbar . $downloadall;
        } else {
            $localhtml .= $searchform . $this->output->notification(get_string('nobadges', 'badges'), 'info');
        }
        $localhtml .= html_writer::end_tag('div');

        // External badges.
        $externalhtml = "";
        if (!empty($CFG->badges_allowexternalbackpack)) {
            $externalhtml .= html_writer::start_tag('div', array('class' => 'generalbox'));
            $externalhtml .= $this->output->heading_with_help(get_string('externalbadges', 'badges'), 'externalbadges', 'badges');
            if (!is_null($backpack)) {
                if ($backpack->totalcollections == 0) {
                    $externalhtml .= get_string('nobackpackcollectionssummary', 'badges', $backpack);
                } else {
                    if ($backpack->totalbadges == 0) {
                        $externalhtml .= get_string('nobackpackbadgessummary', 'badges', $backpack);
                    } else {
                        $externalhtml .= get_string('backpackbadgessummary', 'badges', $backpack);
                        $externalhtml .= '<br/><br/>' . $this->print_badges_list($backpack->badges, $USER->id, true, true);
                    }
                }
            } else {
                $externalhtml .= get_string('externalconnectto', 'badges', $mybackpack->out());
            }

            $externalhtml .= html_writer::end_tag('div');
            $attr = ['class' => 'btn btn-secondary'];
            $label = get_string('backpackbadgessettings', 'badges');
            $backpacksettings = html_writer::link(new moodle_url('/badges/mybackpack.php'), $label, $attr);
            $actionshtml = $this->output->box('', 'col-md-3');
            $actionshtml .= $this->output->box($backpacksettings, 'col-md-9');
            $actionshtml = $this->output->box($actionshtml, 'row ml-5');
            $externalhtml .= $actionshtml;
        }

        return $localhtml . $externalhtml;
    }

    /**
     * Render a collection of badges.
     *
     * @param \core_badges\output\badge_collection $badges
     * @return string
     */
    protected function render_badge_collection(\core_badges\output\badge_collection $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'table table-bordered table-striped';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $badges->sort, $badges->dir);
        $sortbyawarded = $this->helper_sortable_heading(get_string('awardedtoyou', 'badges'),
                'dateissued', $badges->sort, $badges->dir);
        $table->head = array(
                    get_string('badgeimage', 'badges'),
                    $sortbyname,
                    get_string('description', 'badges'),
                    get_string('bcriteria', 'badges'),
                    $sortbyawarded
                );
        $table->colclasses = array('badgeimage', 'name', 'description', 'criteria', 'awards');

        foreach ($badges->badges as $badge) {
            $badgeimage = print_badge_image($badge, $this->page->context, 'large');
            $name = $badge->name;
            $description = $badge->description;
            $criteria = self::print_badge_criteria($badge);
            if ($badge->dateissued) {
                $icon = new pix_icon('i/valid',
                            get_string('dateearned', 'badges',
                                userdate($badge->dateissued, get_string('strftimedatefullshort', 'core_langconfig'))));
                $badgeurl = new moodle_url('/badges/badge.php', array('hash' => $badge->uniquehash));
                $awarded = $this->output->action_icon($badgeurl, $icon, null, null, true);
            } else {
                $awarded = "";
            }
            $row = array($badgeimage, $name, $description, $criteria, $awarded);
            $table->data[] = $row;
        }

        $htmltable = html_writer::table($table);

        return $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    /**
     * Render a table of badges.
     *
     * @param \core_badges\output\badge_management $badges
     * @return string
     */
    protected function render_badge_management(\core_badges\output\badge_management $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');

        // New badge button.
        $htmlnew = '';
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'table table-bordered table-striped';

        $sortbyname = $this->helper_sortable_heading(get_string('name'),
                'name', $badges->sort, $badges->dir);
        $sortbystatus = $this->helper_sortable_heading(get_string('status', 'badges'),
                'status', $badges->sort, $badges->dir);
        $table->head = array(
                $sortbyname,
                $sortbystatus,
                get_string('bcriteria', 'badges'),
                get_string('awards', 'badges'),
                get_string('actions')
            );
        $table->colclasses = array('name', 'status', 'criteria', 'awards', 'actions');

        foreach ($badges->badges as $b) {
            $style = !$b->is_active() ? array('class' => 'dimmed') : array();
            $forlink =  print_badge_image($b, $this->page->context) . ' ' .
                        html_writer::start_tag('span') . $b->name . html_writer::end_tag('span');
            $name = html_writer::link(new moodle_url('/badges/overview.php', array('id' => $b->id)), $forlink, $style);
            $status = $b->statstring;
            $criteria = self::print_badge_criteria($b, 'short');

            if (has_capability('moodle/badges:viewawarded', $this->page->context)) {
                $awards = html_writer::link(new moodle_url('/badges/recipients.php', array('id' => $b->id)), $b->awards);
            } else {
                $awards = $b->awards;
            }

            $actions = self::print_badge_table_actions($b, $this->page->context);

            $row = array($name, $status, $criteria, $awards, $actions);
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlnew . $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    /**
     * Prints tabs for badge editing.
     *
     * @deprecated since Moodle 4.0
     * @todo MDL-73426 Final deprecation.
     * @param integer $badgeid The badgeid to edit.
     * @param context $context The current context.
     * @param string $current The currently selected tab.
     * @return string
     */
    public function print_badge_tabs($badgeid, $context, $current = 'overview') {
        global $DB;
        debugging("print_badge_tabs() is deprecated. " .
            "This is replaced with the manage_badge_action_bar tertiary navigation.", DEBUG_DEVELOPER);

        $badge = new badge($badgeid);
        $row = array();

        $row[] = new tabobject('overview',
                    new moodle_url('/badges/overview.php', array('id' => $badgeid)),
                    get_string('boverview', 'badges')
                );

        if (has_capability('moodle/badges:configuredetails', $context)) {
            $row[] = new tabobject('badge',
                        new moodle_url('/badges/edit.php', array('id' => $badgeid, 'action' => 'badge')),
                        get_string('bdetails', 'badges')
                    );
        }

        if (has_capability('moodle/badges:configurecriteria', $context)) {
            $row[] = new tabobject('criteria',
                        new moodle_url('/badges/criteria.php', array('id' => $badgeid)),
                        get_string('bcriteria', 'badges')
                    );
        }

        if (has_capability('moodle/badges:configuremessages', $context)) {
            $row[] = new tabobject('message',
                        new moodle_url('/badges/edit.php', array('id' => $badgeid, 'action' => 'message')),
                        get_string('bmessage', 'badges')
                    );
        }

        if (has_capability('moodle/badges:viewawarded', $context)) {
            $awarded = $DB->count_records_sql('SELECT COUNT(b.userid)
                                               FROM {badge_issued} b INNER JOIN {user} u ON b.userid = u.id
                                               WHERE b.badgeid = :badgeid AND u.deleted = 0', array('badgeid' => $badgeid));
            $row[] = new tabobject('awards',
                        new moodle_url('/badges/recipients.php', array('id' => $badgeid)),
                        get_string('bawards', 'badges', $awarded)
                    );
        }

        if (has_capability('moodle/badges:configuredetails', $context)) {
            $row[] = new tabobject('bendorsement',
                new moodle_url('/badges/endorsement.php', array('id' => $badgeid)),
                get_string('bendorsement', 'badges')
            );
        }

        if (has_capability('moodle/badges:configuredetails', $context)) {
            $sql = "SELECT COUNT(br.badgeid)
                      FROM {badge_related} br
                     WHERE (br.badgeid = :badgeid OR br.relatedbadgeid = :badgeid2)";
            $related = $DB->count_records_sql($sql, ['badgeid' => $badgeid, 'badgeid2' => $badgeid]);
            $row[] = new tabobject('brelated',
                new moodle_url('/badges/related.php', array('id' => $badgeid)),
                get_string('brelated', 'badges', $related)
            );
        }

        if (has_capability('moodle/badges:configuredetails', $context)) {
            $alignments = $DB->count_records_sql("SELECT COUNT(bc.id)
                      FROM {badge_alignment} bc WHERE bc.badgeid = :badgeid", array('badgeid' => $badgeid));
            $row[] = new tabobject('alignment',
                new moodle_url('/badges/alignment.php', array('id' => $badgeid)),
                get_string('balignment', 'badges', $alignments)
            );
        }

        echo $this->tabtree($row, $current);
    }

    /**
     * Prints badge status box.
     *
     * @param badge $badge
     * @return Either the status box html as a string or null
     */
    public function print_badge_status_box(badge $badge) {
        if (has_capability('moodle/badges:configurecriteria', $badge->get_context())) {

            if (!$badge->has_criteria()) {
                $criteriaurl = new moodle_url('/badges/criteria.php', array('id' => $badge->id));
                $status = get_string('nocriteria', 'badges');
                if ($this->page->url != $criteriaurl) {
                    $action = $this->output->single_button(
                        $criteriaurl,
                        get_string('addcriteria', 'badges'), 'POST', array('class' => 'activatebadge'));
                } else {
                    $action = '';
                }

                $message = $status . $action;
            } else {
                $status = get_string('statusmessage_' . $badge->status, 'badges');
                if ($badge->is_active()) {
                    $action = $this->output->single_button(new moodle_url('/badges/action.php',
                                array('id' => $badge->id, 'lock' => 1, 'sesskey' => sesskey(),
                                      'return' => $this->page->url->out_as_local_url(false))),
                            get_string('deactivate', 'badges'), 'POST', array('class' => 'activatebadge'));
                } else {
                    $action = $this->output->single_button(new moodle_url('/badges/action.php',
                                array('id' => $badge->id, 'activate' => 1, 'sesskey' => sesskey(),
                                      'return' => $this->page->url->out_as_local_url(false))),
                            get_string('activate', 'badges'), 'POST', array('class' => 'activatebadge'));
                }

                $message = $status . $this->output->help_icon('status', 'badges') . $action;

            }

            $style = $badge->is_active() ? 'generalbox statusbox active' : 'generalbox statusbox inactive';
            return $this->output->box($message, $style);
        }

        return null;
    }

    /**
     * Returns information about badge criteria in a list form.
     *
     * @param badge $badge Badge objects
     * @param string $short Indicates whether to print full info about this badge
     * @return string $output HTML string to output
     */
    public function print_badge_criteria(badge $badge, $short = '') {
        $agg = $badge->get_aggregation_methods();
        if (empty($badge->criteria)) {
            return get_string('nocriteria', 'badges');
        }

        $overalldescr = '';
        $overall = $badge->criteria[BADGE_CRITERIA_TYPE_OVERALL];
        if (!$short && !empty($overall->description)) {
            $overalldescr = $this->output->box(
                format_text($overall->description, $overall->descriptionformat, array('context' => $badge->get_context())),
                'criteria-description'
                );
        }

        // Get the condition string.
        $condition = '';
        if (count($badge->criteria) != 2) {
            $condition = get_string('criteria_descr_' . $short . BADGE_CRITERIA_TYPE_OVERALL, 'badges',
                                      core_text::strtoupper($agg[$badge->get_aggregation_method()]));
        }

        unset($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]);

        $items = array();
        // If only one criterion left, make sure its description goe to the top.
        if (count($badge->criteria) == 1) {
            $c = reset($badge->criteria);
            if (!$short && !empty($c->description)) {
                $overalldescr = $this->output->box(
                    format_text($c->description, $c->descriptionformat, array('context' => $badge->get_context())),
                    'criteria-description'
                    );
            }
            if (count($c->params) == 1) {
                $items[] = get_string('criteria_descr_single_' . $short . $c->criteriatype , 'badges') .
                           $c->get_details($short);
            } else {
                $items[] = get_string('criteria_descr_' . $short . $c->criteriatype, 'badges',
                        core_text::strtoupper($agg[$badge->get_aggregation_method($c->criteriatype)])) .
                        $c->get_details($short);
            }
        } else {
            foreach ($badge->criteria as $type => $c) {
                $criteriadescr = '';
                if (!$short && !empty($c->description)) {
                    $criteriadescr = $this->output->box(
                        format_text($c->description, $c->descriptionformat, array('context' => $badge->get_context())),
                        'criteria-description'
                        );
                }
                if (count($c->params) == 1) {
                    $items[] = get_string('criteria_descr_single_' . $short . $type , 'badges') .
                               $c->get_details($short) . $criteriadescr;
                } else {
                    $items[] = get_string('criteria_descr_' . $short . $type , 'badges',
                            core_text::strtoupper($agg[$badge->get_aggregation_method($type)])) .
                            $c->get_details($short) .
                            $criteriadescr;
                }
            }
        }

        return $overalldescr . $condition . html_writer::alist($items, array(), 'ul');;
    }

    /**
     * Prints criteria actions for badge editing.
     *
     * @param badge $badge
     * @return string
     */
    public function print_criteria_actions(badge $badge) {
        $output = '';
        if (!$badge->is_active() && !$badge->is_locked()) {
            $accepted = $badge->get_accepted_criteria();
            $potential = array_diff($accepted, array_keys($badge->criteria));

            if (!empty($potential)) {
                foreach ($potential as $p) {
                    if ($p != 0) {
                        $select[$p] = get_string('criteria_' . $p, 'badges');
                    }
                }
                $output .= $this->output->single_select(
                    new moodle_url('/badges/criteria_settings.php', array('badgeid' => $badge->id, 'add' => true)),
                    'type',
                    $select,
                    '',
                    array('' => 'choosedots'),
                    null,
                    array('label' => get_string('addbadgecriteria', 'badges'))
                );
            } else {
                $output .= $this->output->box(get_string('nothingtoadd', 'badges'), 'clearfix');
            }
        }

        return $output;
    }

    /**
     * Renders a table with users who have earned the badge.
     * Based on stamps collection plugin.
     *
     * @param \core_badges\output\badge_recipients $recipients
     * @return string
     */
    protected function render_badge_recipients(\core_badges\output\badge_recipients $recipients) {
        $paging = new paging_bar($recipients->totalcount, $recipients->page, $recipients->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter boxwidthwide';

        $sortbyfirstname = $this->helper_sortable_heading(get_string('firstname'),
                'firstname', $recipients->sort, $recipients->dir);
        $sortbylastname = $this->helper_sortable_heading(get_string('lastname'),
                'lastname', $recipients->sort, $recipients->dir);
        if ($this->helper_fullname_format() == 'lf') {
            $sortbyname = $sortbylastname . ' / ' . $sortbyfirstname;
        } else {
            $sortbyname = $sortbyfirstname . ' / ' . $sortbylastname;
        }

        $sortbydate = $this->helper_sortable_heading(get_string('dateawarded', 'badges'),
                'dateissued', $recipients->sort, $recipients->dir);

        $table->head = array($sortbyname, $sortbydate, '');

        foreach ($recipients->userids as $holder) {
            $fullname = fullname($holder);
            $fullname = html_writer::link(
                            new moodle_url('/user/profile.php', array('id' => $holder->userid)),
                            $fullname
                        );
            $awarded  = userdate($holder->dateissued);
            $badgeurl = html_writer::link(
                            new moodle_url('/badges/badge.php', array('hash' => $holder->uniquehash)),
                            get_string('viewbadge', 'badges')
                        );

            $row = array($fullname, $awarded, $badgeurl);
            $table->data[] = $row;
        }

        $htmltable = html_writer::table($table);

        return $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Helper methods
    // Reused from stamps collection plugin
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Renders a text with icons to sort by the given column
     *
     * This is intended for table headings.
     *
     * @param string $text    The heading text
     * @param string $sortid  The column id used for sorting
     * @param string $sortby  Currently sorted by (column id)
     * @param string $sorthow Currently sorted how (ASC|DESC)
     *
     * @return string
     */
    protected function helper_sortable_heading($text, $sortid = null, $sortby = null, $sorthow = null) {
        $out = html_writer::tag('span', $text, array('class' => 'text'));

        if (!is_null($sortid)) {
            if ($sortby !== $sortid || $sorthow !== 'ASC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'ASC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_asc', get_string('sortbyx', 'core', s($text)), null, array('class' => 'iconsort')));
            }
            if ($sortby !== $sortid || $sorthow !== 'DESC') {
                $url = new moodle_url($this->page->url);
                $url->params(array('sort' => $sortid, 'dir' => 'DESC'));
                $out .= $this->output->action_icon($url,
                        new pix_icon('t/sort_desc', get_string('sortbyxreverse', 'core', s($text)), null, array('class' => 'iconsort')));
            }
        }
        return $out;
    }
    /**
     * Tries to guess the fullname format set at the site
     *
     * @return string fl|lf
     */
    protected function helper_fullname_format() {
        $fake = new stdClass();
        $fake->lastname = 'LLLL';
        $fake->firstname = 'FFFF';
        $fullname = get_string('fullnamedisplay', '', $fake);
        if (strpos($fullname, 'LLLL') < strpos($fullname, 'FFFF')) {
            return 'lf';
        } else {
            return 'fl';
        }
    }
    /**
     * Renders a search form
     *
     * @param string $search Search string
     * @return string HTML
     */
    protected function helper_search_form($search) {
        global $CFG;
        require_once($CFG->libdir . '/formslib.php');

        $mform = new MoodleQuickForm('searchform', 'POST', $this->page->url);

        $mform->addElement('hidden', 'sesskey', sesskey());

        $el[] = $mform->createElement('text', 'search', get_string('search'), array('size' => 20));
        $mform->setDefault('search', $search);
        $el[] = $mform->createElement('submit', 'submitsearch', get_string('search'));
        $el[] = $mform->createElement('submit', 'clearsearch', get_string('clear'));
        $mform->addGroup($el, 'searchgroup', get_string('searchname', 'badges'), ' ', false);

        ob_start();
        $mform->display();
        $out = ob_get_clean();

        return $out;
    }

    /**
     * Renders a definition list
     *
     * @param array $items the list of items to define
     * @param array
     */
    protected function definition_list(array $items, array $attributes = array()) {
        $output = html_writer::start_tag('dl', $attributes);
        foreach ($items as $label => $value) {
            $output .= html_writer::tag('dt', $label);
            $output .= html_writer::tag('dd', $value);
        }
        $output .= html_writer::end_tag('dl');
        return $output;
    }

    /**
     * Outputs list en badges.
     *
     * @param badge $badge Badge object.
     * @return string $output content endorsement to output.
     */
    protected function print_badge_endorsement(badge $badge) {
        $output = '';
        $endorsement = $badge->get_endorsement();
        $dl = array();
        $output .= $this->heading(get_string('endorsement', 'badges'), 3);
        if (!empty($endorsement)) {
            $dl[get_string('issuername', 'badges')] = $endorsement->issuername;
            $dl[get_string('issueremail', 'badges')] =
                html_writer::tag('a', $endorsement->issueremail, array('href' => 'mailto:' . $endorsement->issueremail));
            $dl[get_string('issuerurl', 'badges')] = html_writer::link($endorsement->issuerurl, $endorsement->issuerurl,
                array('target' => '_blank'));
            $dl[get_string('dateawarded', 'badges')] = userdate($endorsement->dateissued);
            $dl[get_string('claimid', 'badges')] = html_writer::link($endorsement->claimid, $endorsement->claimid,
            array('target' => '_blank'));
            $dl[get_string('claimcomment', 'badges')] = $endorsement->claimcomment;
            $output .= $this->definition_list($dl);
        } else {
            $output .= get_string('noendorsement', 'badges');
        }
        return $output;
    }

    /**
     * Print list badges related.
     *
     * @param badge $badge Badge objects.
     * @return string $output List related badges to output.
     */
    protected function print_badge_related(badge $badge) {
        $output = '';
        $relatedbadges = $badge->get_related_badges();
        $output .= $this->heading(get_string('relatedbages', 'badges'), 3);
        if (!empty($relatedbadges)) {
            $items = array();
            foreach ($relatedbadges as $related) {
                $relatedurl = new moodle_url('/badges/overview.php', array('id' => $related->id));
                $items[] = html_writer::link($relatedurl->out(), $related->name, array('target' => '_blank'));
            }
            $output .= html_writer::alist($items, array(), 'ul');
        } else {
            $output .= get_string('norelated', 'badges');
        }
        return $output;
    }

    /**
     * Print list badge alignments.
     *
     * @param badge $badge Badge objects.
     * @return string $output List alignments to output.
     */
    protected function print_badge_alignments(badge $badge) {
        $output = '';
        $output .= $this->heading(get_string('alignment', 'badges'), 3);
        $alignments = $badge->get_alignments();
        if (!empty($alignments)) {
            $items = array();
            foreach ($alignments as $alignment) {
                $urlaligment = new moodle_url('alignment.php',
                    array('id' => $badge->id, 'alignmentid' => $alignment->id)
                );
                $items[] = html_writer::link($urlaligment, $alignment->targetname, array('target' => '_blank'));
            }
            $output .= html_writer::alist($items, array(), 'ul');
        } else {
            $output .= get_string('noalignment', 'badges');
        }
        return $output;
    }

    /**
     * Renders a table for related badges.
     *
     * @param \core_badges\output\badge_related $related list related badges.
     * @return string list related badges to output.
     */
    protected function render_badge_related(\core_badges\output\badge_related $related) {
        $currentbadge = new badge($related->currentbadgeid);
        $languages = get_string_manager()->get_list_of_languages();
        $paging = new paging_bar($related->totalcount, $related->page, $related->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter boxwidthwide';
        $table->head = array(
            get_string('name'),
            get_string('version', 'badges'),
            get_string('language', 'badges'),
            get_string('type', 'badges')
        );
        if (!$currentbadge->is_active() && !$currentbadge->is_locked()) {
            array_push($table->head, '');
        }

        foreach ($related->badges as $badge) {
            $badgeobject = new badge($badge->id);
            $style = array('title' => $badgeobject->name);
            if (!$badgeobject->is_active()) {
                $style['class'] = 'dimmed';
            }
            $context = ($badgeobject->type == BADGE_TYPE_SITE) ?
                context_system::instance() : context_course::instance($badgeobject->courseid);
            $forlink = print_badge_image($badgeobject, $context) . ' ' .
                html_writer::start_tag('span') . $badgeobject->name . html_writer::end_tag('span');
            $name = html_writer::link(new moodle_url('/badges/overview.php', array('id' => $badgeobject->id)), $forlink, $style);

            $row = array(
                $name,
                $badge->version,
                $badge->language ? $languages[$badge->language] : '',
                $badge->type == BADGE_TYPE_COURSE ? get_string('badgesview', 'badges') : get_string('sitebadges', 'badges')
            );
            if (!$currentbadge->is_active() && !$currentbadge->is_locked()) {
                $action = $this->output->action_icon(
                    new moodle_url('/badges/related_action.php', [
                        'badgeid' => $related->currentbadgeid,
                        'relatedid' => $badge->id,
                        'sesskey' => sesskey(),
                        'action' => 'remove'
                    ]),
                    new pix_icon('t/delete', get_string('delete')));
                $actions = html_writer::tag('div', $action, array('class' => 'badge-actions'));
                array_push($row, $actions);
            }
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    /**
     * Renders a table with alignment.
     *
     * @param core_badges\output\badge_alignments $alignments List alignments.
     * @return string List alignment to output.
     */
    protected function render_badge_alignments(\core_badges\output\badge_alignments $alignments) {
        $currentbadge = new badge($alignments->currentbadgeid);
        $paging = new paging_bar($alignments->totalcount, $alignments->page, $alignments->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'generaltable boxaligncenter boxwidthwide';
        $table->head = array('Name', 'URL', '');

        foreach ($alignments->alignments as $item) {
            $urlaligment = new moodle_url('alignment.php',
                array(
                    'id' => $currentbadge->id,
                    'alignmentid' => $item->id,
                )
            );
            $row = array(
                html_writer::link($urlaligment, $item->targetname),
                html_writer::link($item->targeturl, $item->targeturl, array('target' => '_blank'))
            );
            if (!$currentbadge->is_active() && !$currentbadge->is_locked()) {
                $delete = $this->output->action_icon(
                    new moodle_url('/badges/alignment_action.php', [
                        'id' => $currentbadge->id,
                        'alignmentid' => $item->id,
                        'sesskey' => sesskey(),
                        'action' => 'remove'
                    ]),
                    new pix_icon('t/delete', get_string('delete'))
                );
                $edit = $this->output->action_icon(
                    new moodle_url('alignment.php',
                        array(
                            'id' => $currentbadge->id,
                            'alignmentid' => $item->id,
                            'action' => 'edit'
                        )
                    ), new pix_icon('t/edit', get_string('edit')));
                $actions = html_writer::tag('div', $edit . $delete, array('class' => 'badge-actions'));
                array_push($row, $actions);
            }
            $table->data[] = $row;
        }
        $htmltable = html_writer::table($table);

        return $htmlpagingbar . $htmltable . $htmlpagingbar;
    }

    /**
     * Defer to template.
     *
     * @param \core_badges\output\external_backpacks_page $page
     * @return bool|string
     */
    public function render_external_backpacks_page(\core_badges\output\external_backpacks_page $page) {
        $data = $page->export_for_template($this);
        return parent::render_from_template('core_badges/external_backpacks_page', $data);
    }

    /**
     * Get the result of a backpack validation with its settings. It returns:
     * - A informative message if the backpack version is different from OBv2.
     * - A warning with the error if it's not possible to connect to this backpack.
     * - A successful message if the connection has worked.
     *
     * @param  int    $backpackid The backpack identifier.
     * @return string A message with the validation result.
     */
    public function render_test_backpack_result(int $backpackid): string {
        // Get the backpack.
        $backpack = badges_get_site_backpack($backpackid);

        // Add the header to the result.
        $result = $this->heading(get_string('testbackpack', 'badges', $backpack->backpackweburl));

        if ($backpack->apiversion != OPEN_BADGES_V2) {
            // Only OBv2 supports this validation.
            $result .= get_string('backpackconnectionnottested', 'badges');
        } else {
            $message = badges_verify_backpack($backpackid);
            if (empty($message)) {
                $result .= get_string('backpackconnectionok', 'badges');
            } else {
                $result .= $message;
            }
        }

        return $result;
    }

    /**
     * Render the tertiary navigation for the page.
     *
     * @param \core_badges\output\base_action_bar $actionbar
     * @return bool|string
     */
    public function render_tertiary_navigation(\core_badges\output\base_action_bar $actionbar) {
        return $this->render_from_template($actionbar->get_template(), $actionbar->export_for_template($this));
    }
}
