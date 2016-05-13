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
                $imageurl = moodle_url::make_pluginfile_url($context->id, 'badges', 'badgeimage', $badge->id, '/', 'f1', false);
            } else {
                $bname = s($badge->assertion->badge->name);
                $imageurl = $badge->imageUrl;
            }

            $name = html_writer::tag('span', $bname, array('class' => 'badge-name'));

            $image = html_writer::empty_tag('img', array('src' => $imageurl, 'class' => 'badge-image'));
            if (!empty($badge->dateexpire) && $badge->dateexpire < time()) {
                $image .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'badges', userdate($badge->dateexpire)),
                        'moodle',
                        array('class' => 'expireimage'));
                $name .= '(' . get_string('expired', 'badges') . ')';
            }

            $download = $status = $push = '';
            if (($userid == $USER->id) && !$profile) {
                $url = new moodle_url('mybadges.php', array('download' => $badge->id, 'hash' => $badge->uniquehash, 'sesskey' => sesskey()));
                $notexpiredbadge = (empty($badge->dateexpire) || $badge->dateexpire > time());
                $backpackexists = badges_user_has_backpack($USER->id);
                if (!empty($CFG->badges_allowexternalbackpack) && $notexpiredbadge && $backpackexists) {
                    $assertion = new moodle_url('/badges/assertion.php', array('b' => $badge->uniquehash));
                    $action = new component_action('click', 'addtobackpack', array('assertion' => $assertion->out(false)));
                    $push = $this->output->action_icon(new moodle_url('#'), new pix_icon('t/backpack', get_string('addtobackpack', 'badges')), $action);
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
                    'class' => 'actionbutton')
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

        // Badge details.

        $display .= $this->heading(get_string('badgedetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('name')] = $badge->name;
        $dl[get_string('description', 'badges')] = $badge->description;
        $dl[get_string('createdon', 'search')] = userdate($badge->timecreated);
        $dl[get_string('badgeimage', 'badges')] = print_badge_image($badge, $context, 'large');
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
            $url = new moodle_url('/badges/edit.php', array('id' => $badge->id, 'action' => 'details'));
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

    // Outputs issued badge with actions available.
    protected function render_issued_badge(issued_badge $ibadge) {
        global $USER, $CFG, $DB, $SITE;
        $issued = $ibadge->issued;
        $userinfo = $ibadge->recipient;
        $badgeclass = $ibadge->badgeclass;
        $badge = new badge($ibadge->badgeid);
        $now = time();
        $expiration = isset($issued['expires']) ? $issued['expires'] : $now + 86400;

        $output = '';
        $output .= html_writer::start_tag('div', array('id' => 'badge'));
        $output .= html_writer::start_tag('div', array('id' => 'badge-image'));
        $output .= html_writer::empty_tag('img', array('src' => $badgeclass['image'], 'alt' => $badge->name));
        if ($expiration < $now) {
            $output .= $this->output->pix_icon('i/expired',
            get_string('expireddate', 'badges', userdate($issued['expires'])),
                'moodle',
                array('class' => 'expireimage'));
        }

        if ($USER->id == $userinfo->id && !empty($CFG->enablebadges)) {
            $output .= $this->output->single_button(
                        new moodle_url('/badges/badge.php', array('hash' => $issued['uid'], 'bake' => true)),
                        get_string('download'),
                        'POST');
            if (!empty($CFG->badges_allowexternalbackpack) && ($expiration > $now) && badges_user_has_backpack($USER->id)) {
                $assertion = new moodle_url('/badges/assertion.php', array('b' => $issued['uid']));
                $action = new component_action('click', 'addtobackpack', array('assertion' => $assertion->out(false)));
                $attributes = array(
                        'type'  => 'button',
                        'id'    => 'addbutton',
                        'value' => get_string('addtobackpack', 'badges'));
                $tobackpack = html_writer::tag('input', '', $attributes);
                $this->output->add_action_handler($action, 'addbutton');
                $output .= $tobackpack;
            }
        }
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('id' => 'badge-details'));
        // Recipient information.
        $output .= $this->output->heading(get_string('recipientdetails', 'badges'), 3);
        $dl = array();
        if ($userinfo->deleted) {
            $strdata = new stdClass();
            $strdata->user = fullname($userinfo);
            $strdata->site = format_string($SITE->fullname, true, array('context' => context_system::instance()));

            $dl[get_string('name')] = get_string('error:userdeleted', 'badges', $strdata);
        } else {
            $dl[get_string('name')] = fullname($userinfo);
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuerdetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('issuername', 'badges')] = $badge->issuername;
        if (isset($badge->issuercontact) && !empty($badge->issuercontact)) {
            $dl[get_string('contact', 'badges')] = obfuscate_mailto($badge->issuercontact);
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('badgedetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('name')] = $badge->name;
        $dl[get_string('description', 'badges')] = $badge->description;

        if ($badge->type == BADGE_TYPE_COURSE && isset($badge->courseid)) {
            $coursename = $DB->get_field('course', 'fullname', array('id' => $badge->courseid));
            $dl[get_string('course')] = $coursename;
        }
        $dl[get_string('bcriteria', 'badges')] = self::print_badge_criteria($badge);
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuancedetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('dateawarded', 'badges')] = userdate($issued['issuedOn']);
        if (isset($issued['expires'])) {
            if ($issued['expires'] < $now) {
                $dl[get_string('expirydate', 'badges')] = userdate($issued['expires']) . get_string('warnexpired', 'badges');

            } else {
                $dl[get_string('expirydate', 'badges')] = userdate($issued['expires']);
            }
        }

        // Print evidence.
        $agg = $badge->get_aggregation_methods();
        $evidence = $badge->get_criteria_completions($userinfo->id);
        $eids = array_map(create_function('$o', 'return $o->critid;'), $evidence);
        unset($badge->criteria[BADGE_CRITERIA_TYPE_OVERALL]);

        $items = array();
        foreach ($badge->criteria as $type => $c) {
            if (in_array($c->id, $eids)) {
                if (count($c->params) == 1) {
                    $items[] = get_string('criteria_descr_single_' . $type , 'badges') . $c->get_details();
                } else {
                    $items[] = get_string('criteria_descr_' . $type , 'badges',
                            core_text::strtoupper($agg[$badge->get_aggregation_method($type)])) . $c->get_details();
                }
            }
        }

        $dl[get_string('evidence', 'badges')] = get_string('completioninfo', 'badges') . html_writer::alist($items, array(), 'ul');
        $output .= $this->definition_list($dl);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    // Outputs external badge.
    protected function render_external_badge(external_badge $ibadge) {
        $issued = $ibadge->issued;
        $assertion = $issued->assertion;
        $issuer = $assertion->badge->issuer;
        $userinfo = $ibadge->recipient;
        $table = new html_table();
        $today = strtotime(date('Y-m-d'));

        $output = '';
        $output .= html_writer::start_tag('div', array('id' => 'badge'));
        $output .= html_writer::start_tag('div', array('id' => 'badge-image'));
        $output .= html_writer::empty_tag('img', array('src' => $issued->imageUrl));
        if (isset($assertion->expires)) {
            $expiration = !strtotime($assertion->expires) ? s($assertion->expires) : strtotime($assertion->expires);
            if ($expiration < $today) {
                $output .= $this->output->pix_icon('i/expired',
                        get_string('expireddate', 'badges', userdate($expiration)),
                        'moodle',
                        array('class' => 'expireimage'));
            }
        }
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('id' => 'badge-details'));

        // Recipient information.
        $output .= $this->output->heading(get_string('recipientdetails', 'badges'), 3);
        $dl = array();
        // Technically, we should alway have a user at this point, but added an extra check just in case.
        if ($userinfo) {
            if (!$ibadge->valid) {
                $notify = $this->output->notification(get_string('recipientvalidationproblem', 'badges'), 'notifynotice');
                $dl[get_string('name')] = fullname($userinfo) . $notify;
            } else {
                $dl[get_string('name')] = fullname($userinfo);
            }
        } else {
            $notify = $this->output->notification(get_string('recipientidentificationproblem', 'badges'), 'notifynotice');
            $dl[get_string('name')] = $notify;
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuerdetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('issuername', 'badges')] = s($issuer->name);
        $dl[get_string('issuerurl', 'badges')] = html_writer::tag('a', $issuer->origin, array('href' => $issuer->origin));

        if (isset($issuer->contact)) {
            $dl[get_string('contact', 'badges')] = obfuscate_mailto($issuer->contact);
        }
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('badgedetails', 'badges'), 3);
        $dl = array();
        $dl[get_string('name')] = s($assertion->badge->name);
        $dl[get_string('description', 'badges')] = s($assertion->badge->description);
        $dl[get_string('bcriteria', 'badges')] = html_writer::tag('a', s($assertion->badge->criteria), array('href' => $assertion->badge->criteria));
        $output .= $this->definition_list($dl);

        $output .= $this->output->heading(get_string('issuancedetails', 'badges'), 3);
        $dl = array();
        if (isset($assertion->issued_on)) {
            $issuedate = !strtotime($assertion->issued_on) ? s($assertion->issued_on) : strtotime($assertion->issued_on);
            $dl[get_string('dateawarded', 'badges')] = userdate($issuedate);
        }
        if (isset($assertion->expires)) {
            if ($expiration < $today) {
                $dl[get_string('expirydate', 'badges')] = userdate($expiration) . get_string('warnexpired', 'badges');
            } else {
                $dl[get_string('expirydate', 'badges')] = userdate($expiration);
            }
        }
        if (isset($assertion->evidence)) {
            $dl[get_string('evidence', 'badges')] = html_writer::tag('a', s($assertion->evidence), array('href' => $assertion->evidence));
        }
        $output .= $this->definition_list($dl);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    // Displays the user badges.
    protected function render_badge_user_collection(badge_user_collection $badges) {
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
        $downloadall = $this->output->single_button(
                    new moodle_url('/badges/mybadges.php', array('downloadall' => true, 'sesskey' => sesskey())),
                    get_string('downloadall'), 'POST', array('class' => 'activatebadge'));

        // Local badges.
        $localhtml = html_writer::start_tag('div', array('id' => 'issued-badge-table', 'class' => 'generalbox'));
        $heading = get_string('localbadges', 'badges', format_string($SITE->fullname, true, array('context' => context_system::instance())));
        $localhtml .= $this->output->heading_with_help($heading, 'localbadgesh', 'badges');
        if ($badges->badges) {
            $downloadbutton = $this->output->heading(get_string('badgesearned', 'badges', $badges->totalcount), 4, 'activatebadge');
            $downloadbutton .= $downloadall;

            $htmllist = $this->print_badges_list($badges->badges, $USER->id);
            $localhtml .= $backpackconnect . $downloadbutton . $searchform . $htmlpagingbar . $htmllist . $htmlpagingbar;
        } else {
            $localhtml .= $searchform . $this->output->notification(get_string('nobadges', 'badges'));
        }
        $localhtml .= html_writer::end_tag('div');

        // External badges.
        $externalhtml = "";
        if (!empty($CFG->badges_allowexternalbackpack)) {
            $externalhtml .= html_writer::start_tag('div', array('class' => 'generalbox'));
            $externalhtml .= $this->output->heading_with_help(get_string('externalbadges', 'badges'), 'externalbadges', 'badges');
            if (!is_null($backpack)) {
                if ($backpack->totalcollections == 0) {
                    $externalhtml .= get_string('nobackpackcollections', 'badges', $backpack);
                } else {
                    if ($backpack->totalbadges == 0) {
                        $externalhtml .= get_string('nobackpackbadges', 'badges', $backpack);
                    } else {
                        $externalhtml .= get_string('backpackbadges', 'badges', $backpack);
                        $externalhtml .= '<br/><br/>' . $this->print_badges_list($backpack->badges, $USER->id, true, true);
                    }
                }
            } else {
                $externalhtml .= get_string('externalconnectto', 'badges', $mybackpack->out());
            }

            $externalhtml .= html_writer::end_tag('div');
        }

        return $localhtml . $externalhtml;
    }

    // Displays the available badges.
    protected function render_badge_collection(badge_collection $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');
        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

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

    // Outputs table of badges with actions available.
    protected function render_badge_management(badge_management $badges) {
        $paging = new paging_bar($badges->totalcount, $badges->page, $badges->perpage, $this->page->url, 'page');

        // New badge button.
        $htmlnew = '';
        if (has_capability('moodle/badges:createbadge', $this->page->context)) {
            $n['type'] = $this->page->url->get_param('type');
            $n['id'] = $this->page->url->get_param('id');
            $htmlnew = $this->output->single_button(new moodle_url('newbadge.php', $n), get_string('newbadge', 'badges'));
        }

        $htmlpagingbar = $this->render($paging);
        $table = new html_table();
        $table->attributes['class'] = 'collection';

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

    // Prints tabs for badge editing.
    public function print_badge_tabs($badgeid, $context, $current = 'overview') {
        global $DB;

        $row = array();

        $row[] = new tabobject('overview',
                    new moodle_url('/badges/overview.php', array('id' => $badgeid)),
                    get_string('boverview', 'badges')
                );

        if (has_capability('moodle/badges:configuredetails', $context)) {
            $row[] = new tabobject('details',
                        new moodle_url('/badges/edit.php', array('id' => $badgeid, 'action' => 'details')),
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

        echo $this->tabtree($row, $current);
    }

    /**
     * Prints badge status box.
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
        if (count($badge->criteria) == 2) {
            $condition = '';
            if (!$short) {
                $condition = get_string('criteria_descr', 'badges');
            }
        } else {
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

    // Prints criteria actions for badge editing.
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

    // Renders a table with users who have earned the badge.
    // Based on stamps collection plugin.
    protected function render_badge_recipients(badge_recipients $recipients) {
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
}

/**
 * An issued badges for badge.php page
 */
class issued_badge implements renderable {
    /** @var issued badge */
    public $issued;

    /** @var badge recipient */
    public $recipient;

    /** @var badge class */
    public $badgeclass;

    /** @var badge visibility to others */
    public $visible = 0;

    /** @var badge class */
    public $badgeid = 0;

    /**
     * Initializes the badge to display
     *
     * @param string $hash Issued badge hash
     */
    public function __construct($hash) {
        global $DB;

        $assertion = new core_badges_assertion($hash);
        $this->issued = $assertion->get_badge_assertion();
        $this->badgeclass = $assertion->get_badge_class();

        $rec = $DB->get_record_sql('SELECT userid, visible, badgeid
                FROM {badge_issued}
                WHERE ' . $DB->sql_compare_text('uniquehash', 40) . ' = ' . $DB->sql_compare_text(':hash', 40),
                array('hash' => $hash), IGNORE_MISSING);
        if ($rec) {
            // Get a recipient from database.
            $namefields = get_all_user_name_fields(true, 'u');
            $user = $DB->get_record_sql("SELECT u.id, $namefields, u.deleted, u.email
                        FROM {user} u WHERE u.id = :userid", array('userid' => $rec->userid));
            $this->recipient = $user;
            $this->visible = $rec->visible;
            $this->badgeid = $rec->badgeid;
        }
    }
}

/**
 * An external badges for external.php page
 */
class external_badge implements renderable {
    /** @var issued badge */
    public $issued;

    /** @var User ID */
    public $recipient;

    /** @var validation of external badge */
    public $valid = true;

    /**
     * Initializes the badge to display
     *
     * @param object $badge External badge information.
     * @param int $recipient User id.
     */
    public function __construct($badge, $recipient) {
        global $DB;
        // At this point a user has connected a backpack. So, we are going to get
        // their backpack email rather than their account email.
        $namefields = get_all_user_name_fields(true, 'u');
        $user = $DB->get_record_sql("SELECT {$namefields}, b.email
                    FROM {user} u INNER JOIN {badge_backpack} b ON u.id = b.userid
                    WHERE userid = :userid", array('userid' => $recipient), IGNORE_MISSING);

        $this->issued = $badge;
        $this->recipient = $user;

        // Check if recipient is valid.
        // There is no way to be 100% sure that a badge belongs to a user.
        // Backpack does not return any recipient information.
        // All we can do is compare that backpack email hashed using salt
        // provided in the assertion matches a badge recipient from the assertion.
        if ($user) {
            if (validate_email($badge->assertion->recipient) && $badge->assertion->recipient == $user->email) {
                // If we have email, compare emails.
                $this->valid = true;
            } else if ($badge->assertion->recipient == 'sha256$' . hash('sha256', $user->email)) {
                // If recipient is hashed, but no salt, compare hashes without salt.
                $this->valid = true;
            } else if ($badge->assertion->recipient == 'sha256$' . hash('sha256', $user->email . $badge->assertion->salt)) {
                // If recipient is hashed, compare hashes.
                $this->valid = true;
            } else {
                // Otherwise, we cannot be sure that this user is a recipient.
                $this->valid = false;
            }
        } else {
            $this->valid = false;
        }
    }
}

/**
 * Badge recipients rendering class
 */
class badge_recipients implements renderable {
    /** @var string how are the data sorted */
    public $sort = 'lastname';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of badge recipients to display per page */
    public $perpage = 30;

    /** @var int the total number or badge recipients to display */
    public $totalcount = null;

    /** @var array internal list of  badge recipients ids */
    public $userids = array();
    /**
     * Initializes the list of users to display
     *
     * @param array $holders List of badge holders
     */
    public function __construct($holders) {
        $this->userids = $holders;
    }
}

/**
 * Collection of all badges for view.php page
 */
class badge_collection implements renderable {

    /** @var string how are the data sorted */
    public $sort = 'name';

    /** @var string how are the data sorted */
    public $dir = 'ASC';

    /** @var int page number to display */
    public $page = 0;

    /** @var int number of badges to display per page */
    public $perpage = BADGE_PERPAGE;

    /** @var int the total number of badges to display */
    public $totalcount = null;

    /** @var array list of badges */
    public $badges = array();

    /**
     * Initializes the list of badges to display
     *
     * @param array $badges Badges to render
     */
    public function __construct($badges) {
        $this->badges = $badges;
    }
}

/**
 * Collection of badges used at the index.php page
 */
class badge_management extends badge_collection implements renderable {
}

/**
 * Collection of user badges used at the mybadges.php page
 */
class badge_user_collection extends badge_collection implements renderable {
    /** @var array backpack settings */
    public $backpack = null;

    /** @var string search */
    public $search = '';

    /**
     * Initializes user badge collection.
     *
     * @param array $badges Badges to render
     * @param int $userid Badges owner
     */
    public function __construct($badges, $userid) {
        global $CFG;
        parent::__construct($badges);

        if (!empty($CFG->badges_allowexternalbackpack)) {
            $this->backpack = get_backpack_settings($userid, true);
        }
    }
}
