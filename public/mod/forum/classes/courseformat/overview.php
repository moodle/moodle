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

namespace mod_forum\courseformat;

use cm_info;
use core\url;
use core_calendar\output\humandate;
use core\output\local\properties\text_align;
use core_courseformat\local\overview\overviewitem;
use core_courseformat\output\local\overview\overviewaction;

/**
 * Forum overview integration.
 *
 * @package    mod_forum
 * @copyright  2025 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class overview extends \core_courseformat\activityoverviewbase {

    /** @var \stdClass|null $forum The forum instance. */
    private ?\stdClass $forum;

    /** @var \stdClass|null $user The current user instance. */
    private ?\stdClass $user;

    /**
     * Constructor.
     *
     * @param cm_info $cm the course module instance.
     * @param \core\output\renderer_helper $rendererhelper the renderer helper.
     * @param \moodle_database $db the database helper.
     */
    public function __construct(
        cm_info $cm,
        /** @var \core\output\renderer_helper $rendererhelper the renderer helper */
        protected readonly \core\output\renderer_helper $rendererhelper,
        /** @var \moodle_database The database instance */
        protected readonly \moodle_database $db,
    ) {
        global $USER;

        parent::__construct($cm);

        $this->user = $USER;
        $this->forum = $this->get_forum_instance();

        if ($this->forum) {
            // Fill the subscription cache for this course and user combination.
            \mod_forum\subscriptions::fill_subscription_cache_for_course($this->forum->course, $this->user->id);
        }
    }

    #[\Override]
    public function get_due_date_overview(): ?overviewitem {

        if ($this->is_teacher()) {
            return null;
        }

        $duedate = null;
        if (isset($this->cm->customdata['duedate'])) {
            $duedate = $this->cm->customdata['duedate'];
        }

        if (empty($duedate)) {
            return new overviewitem(
                name: get_string('duedate', 'mod_forum'),
                value: null,
                content: '-',
            );
        }
        return new overviewitem(
            name: get_string('duedate', 'mod_forum'),
            value: $duedate,
            content: humandate::create_from_timestamp($duedate),
        );
    }

    #[\Override]
    public function get_extra_overview_items(): array {
        if (empty($this->forum)) {
            // User cannot view this forum.
            return [];
        }

        return [
            'forumtype' => $this->get_extra_forumtype_overview(),
            'submitted' => $this->get_extra_track_overview(),
            'subscribed' => $this->get_extra_subscribed_overview(),
            'emaildigest' => $this->get_extra_emaildigest_overview(),
            'discussions' => $this->get_extra_discussions_overview(),
        ];
    }

    #[\Override]
    public function get_actions_overview(): ?overviewitem {

        if (empty($this->forum)) {
            // User cannot view this forum.
            return null;
        }

        $totaldiscussions = forum_count_discussions($this->forum, $this->cm, $this->course);
        $discussionposts = forum_count_discussion_replies($this->forum->id);
        $totalreplies = array_reduce($discussionposts, function($sum, $post) {
            return $sum + $post->replies; // Add 1 to count the discussion post too.
        }, 0); // The '0' is the initial value of $sum.
        $totalreplies += $totaldiscussions; // Add the discussions to the replies count.

        $alertlabel = get_string('unreadposts', 'mod_forum');
        $unread = forum_tp_count_forum_unread_posts($this->cm, $this->course);
        $content = new overviewaction(
            url: new url('/mod/forum/view.php', ['id' => $this->cm->id]),
            text: $totalreplies,
            badgevalue: ($totalreplies > 0 && $unread > 0) ? $unread : null,
            badgetitle: ($totalreplies > 0 && $unread > 0) ? $alertlabel : null,
        );

        return new overviewitem(
            name: get_string('posts', 'mod_forum'),
            value: $totalreplies ? : '0',
            content: $content,
            alertcount: $unread,
            alertlabel: $alertlabel,
        );
    }

    /**
     * Get the forum type item.
     *
     * @return overviewitem|null The overview item (or null if the user cannot enable/disable track).
     */
    private function get_extra_forumtype_overview(): ?overviewitem {

        if (!$this->is_teacher()) {
            return null;
        }

        $allforumtypesnames = forum_get_forum_types_all();

        return new overviewitem(
            name: get_string('forumtype', 'mod_forum'),
            value: $this->forum->type,
            content: $allforumtypesnames[$this->forum->type],
        );
    }

    /**
     * Get the track toggle item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_track_overview(): overviewitem {
        global $CFG;

        $disabled = true;
        $tracked = false;
        $label = null;
        if (
            (intval($this->forum->trackingtype) == FORUM_TRACKING_FORCED)
            && ($CFG->forum_allowforcedreadtracking)
        ) {
            $label = get_string(
                'labelvalue',
                'core',
                [
                    'label' => get_string('trackforum', 'mod_forum'),
                    'value' => get_string('trackingon', 'mod_forum'),
                ],
            );
            $disabled = true;
            $tracked = true;
        } else if (intval($this->forum->trackingtype) === FORUM_TRACKING_OFF) {
            $label = get_string(
                'labelvalue',
                'core',
                [
                    'label' => get_string('trackforum', 'mod_forum'),
                    'value' => get_string('trackingoff', 'mod_forum'),
                ],
            );
            $disabled = true;
        } else if (forum_tp_can_track_forums($this->forum)) {
            $tracked = forum_tp_is_tracked($this->forum);
            $disabled = false;
            $label = get_string('trackforum', 'mod_forum');
        }

        $arialabel = $tracked
            ? get_string('untrackforforum', 'mod_forum', $this->forum->name)
            : get_string('trackforforum', 'mod_forum', $this->forum->name);

        $renderer = $this->rendererhelper->get_renderer('mod_forum');
        $extraattributes = [
            ['name' => 'data-type', 'value' => 'forum-track-toggle'],
            ['name' => 'data-action', 'value' => 'toggle'],
            ['name' => 'data-forumid', 'value' => $this->forum->id],
            ['name' => 'data-forumname', 'value' => $this->forum->name],
            ['name' => 'data-targetstate', 'value' => !$tracked],
            ['name' => 'aria-label', 'value' => $arialabel],
        ];
        $content = $renderer->render_from_template(
            'core/toggle',
            [
                'id' => 'forum-track-toggle-' . $this->forum->id,
                'checked' => $tracked,
                'disabled' => $disabled,
                'extraattributes' => $extraattributes,
                'label' => $label,
                'labelclasses' => 'visually-hidden',
            ],
        );

        $renderer->get_page()->requires->js_call_amd(
            'mod_forum/forum_overview_toggle',
            'init',
            ['#forum-track-toggle-' . $this->forum->id],
        );

        return new overviewitem(
            name: get_string('tracking', 'mod_forum'),
            value: $label,
            content: $content,
        );
    }

    /**
     * Get the subscribed toggle item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_subscribed_overview(): overviewitem {

        $disabled = false;
        $subscribed = false;
        $label = null;
        if (\mod_forum\subscriptions::is_forcesubscribed($this->forum)) {
            $disabled = true;
            $subscribed = true;
            $label = get_string('subscribed', 'mod_forum');
        } else if (
            \mod_forum\subscriptions::subscription_disabled($this->forum)
            && !has_capability('mod/forum:managesubscriptions', $this->context)
        ) {
            $disabled = true;
            $label = get_string('unsubscribed', 'mod_forum');
        } else if (!is_enrolled($this->context, $this->user, '', true)) {
            $disabled = true;
            $label = get_string('unsubscribed', 'mod_forum');
        } else {
            $subscribed = \mod_forum\subscriptions::is_subscribed($this->user->id, $this->forum);
            if ($subscribed) {
                $label = get_string('unsubscribe', 'mod_forum');
            } else {
                $label = get_string('subscribe', 'mod_forum');
            }
        }

        $arialabel = $subscribed
            ? get_string('unsubscribefromforum', 'mod_forum', $this->forum->name)
            : get_string('subscribetoforum', 'mod_forum', $this->forum->name);

        $renderer = $this->rendererhelper->get_renderer('mod_forum');
        $extraattributes = [
            ['name' => 'data-type', 'value' => 'forum-subscription-toggle'],
            ['name' => 'data-action', 'value' => 'toggle'],
            ['name' => 'data-forumid', 'value' => $this->forum->id],
            ['name' => 'data-forumname', 'value' => $this->forum->name],
            ['name' => 'data-targetstate', 'value' => !$subscribed],
            ['name' => 'aria-label', 'value' => $arialabel],
        ];

        $content = $renderer->render_from_template(
            'core/toggle',
            [
                'id' => 'forum-subscription-toggle-' . $this->forum->id,
                'checked' => $subscribed,
                'disabled' => $disabled,
                'extraattributes' => $extraattributes,
                'label' => $label,
                'labelclasses' => 'visually-hidden',
            ],
        );

        $renderer->get_page()->requires->js_call_amd(
            'mod_forum/forum_overview_toggle',
            'init',
            ['#forum-subscription-toggle-' . $this->forum->id],
        );

        return new overviewitem(
            name: get_string('subscribed', 'mod_forum'),
            value: $label,
            content: $content,
        );
    }

    /**
     * Get the email digest selector item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_emaildigest_overview(): overviewitem {

        $cansubscribe = \mod_forum\subscriptions::is_subscribable($this->forum);
        $canmanage = has_capability('mod/forum:managesubscriptions', $this->context);
        $issubscribed = \mod_forum\subscriptions::is_subscribed($this->user->id, $this->forum, null, $this->cm);
        if ($cansubscribe || $canmanage || $issubscribed) {
            if ($this->forum->maildigest === false) {
                $this->forum->maildigest = -1;
            }

            $renderer = $this->rendererhelper->get_renderer('mod_forum');
            $content = $renderer->render($renderer->render_digest_options($this->forum, $this->forum->maildigest));
        }
        $options = forum_get_user_digest_options();

        return new overviewitem(
            name: get_string('digesttype', 'mod_forum'),
            value: $options[$this->forum->maildigest] ?? '-',
            content: $content ?? '-',
        );
    }

    /**
     * Get the discussions item.
     *
     * @return overviewitem The overview item.
     */
    private function get_extra_discussions_overview(): overviewitem {

        $totaldiscussions = forum_count_discussions($this->forum, $this->cm, $this->course);

        return new overviewitem(
            name: get_string('discussions', 'mod_forum'),
            value: $totaldiscussions,
            content: $totaldiscussions,
            textalign: text_align::END,
        );
    }

    /**
     * Initialise the forum instance.
     *
     * @return \stdClass|null The forum instance or null if the user cannot view it.
     */
    private function get_forum_instance(): ?\stdClass {

        // User can't view this forum. Once the capability is renamed to forum:view, this check can be removed.
        if (!has_capability('mod/forum:viewdiscussion', $this->context)) {
            return null;
        }
        $forum = $this->cm->get_instance_record();

        // Initialise maildigest with the proper value.
        $forum->maildigest = $this->db->get_field(
            'forum_digests',
            'maildigest',
            ['userid' => $this->user->id, 'forum' => $forum->id],
        );

        return $forum;
    }

    /**
     * Whether the current user can be considered teacher or not (based on their capabilities).
     *
     * @return bool True if the user can be considered teacher; false otherwise.
     */
    private function is_teacher(): bool {
        return has_capability('mod/forum:rate', $this->context);
    }
}
