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

namespace tool_mfa\output;

use core\context\system;
use tool_mfa\local\factor\object_factor;
use tool_mfa\local\form\login_form;
use \html_writer;
use tool_mfa\plugininfo\factor;

/**
 * MFA renderer.
 *
 * @package     tool_mfa
 * @author      Mikhail Golenkov <golenkovm@gmail.com>
 * @copyright   Catalyst IT
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends \plugin_renderer_base {

    /**
     * Returns the state of the factor as a badge.
     *
     * @param string $state
     * @return string
     */
    public function get_state_badge(string $state): string {

        switch ($state) {
            case factor::STATE_PASS:
                return html_writer::tag('span', get_string('state:pass', 'tool_mfa'), ['class' => 'badge bg-success text-white']);

            case factor::STATE_FAIL:
                return html_writer::tag('span', get_string('state:fail', 'tool_mfa'), ['class' => 'badge bg-danger text-white']);

            case factor::STATE_NEUTRAL:
                return html_writer::tag('span', get_string('state:neutral', 'tool_mfa'),
                    ['class' => 'badge bg-warning text-dark']);

            case factor::STATE_UNKNOWN:
                return html_writer::tag('span', get_string('state:unknown', 'tool_mfa'),
                    ['class' => 'badge bg-secondary text-dark']);

            case factor::STATE_LOCKED:
                return html_writer::tag('span', get_string('state:locked', 'tool_mfa'), ['class' => 'badge bg-danger text-white']);

            default:
                return html_writer::tag('span', get_string('pending', 'tool_mfa'), ['class' => 'badge bg-secondary text-dark']);
        }
    }

    /**
     * Returns a list of factors which a user can add.
     *
     * @return string
     */
    public function available_factors(): string {
        global $USER;
        $factors = factor::get_enabled_factors();
        $data = [];

        foreach ($factors as $factor) {

            // Allow all factors with setup and button.
            // Make an exception for email factor as this is currently set up by admins only and required on this list.
            if ((!$factor->has_setup() || !$factor->show_setup_buttons()) && !$factor instanceof \factor_email\factor) {
                continue;
            }

            $userfactors = $factor->get_active_user_factors($USER);
            $active = !empty($userfactors) ?? false;
            $button = null;
            $icon = $factor->get_icon();
            $params = [
                'action' => 'setup',
                'factor' => $factor->name,
            ];

            if (!$active) {
                // Not active yet and requires set up.
                $info = $factor->get_info();

                if ($factor->show_setup_buttons()) {
                    $params['action'] = 'setup';
                    $button = new \single_button(
                        url: new \moodle_url('action.php', $params),
                        label: $factor->get_setup_string(),
                        method: 'post',
                        type: \single_button::BUTTON_PRIMARY,
                        attributes: [
                            'aria-label' => get_string('setupfactor', 'factor_' . $factor->name),
                        ],
                    );
                    $button = $button->export_for_template($this->output);
                }

            } else {
                // Active and can be managed.
                $factorid = reset($userfactors)->id;
                $info = $factor->get_manage_info($factorid);

                if ($factor->show_setup_buttons()) {
                    $params['action'] = 'manage';
                    $button = new \single_button(
                        url: new \moodle_url('action.php', $params),
                        label: $factor->get_manage_string(),
                        method: 'post',
                        type: \single_button::BUTTON_PRIMARY,
                        attributes: [
                            'aria-label' => get_string('managefactor', 'factor_' . $factor->name),
                        ],
                    );
                    $button = $button->export_for_template($this->output);
                }
            }

            // Prepare data for template.
            $data['factors'][] = [
                'active' => $active,
                'label' => $factor->get_display_name(),
                'name' => $factor->name,
                'info' => $info,
                'icon' => $icon,
                'button' => $button,
            ];
        }

        return $this->render_from_template('tool_mfa/mfa_selector', $data);
    }

    /**
     * @deprecated since Moodle 4.4
     */
    #[\core\attribute\deprecated(null, reason: 'It is no longer used', since: '4.4', mdl: 'MDL-79920', final: true)]
    public function setup_factor(): void {
        \core\deprecation::emit_deprecation([self::class, __FUNCTION__]);
    }

    /**
     * Show a table displaying a users active factors.
     *
     * @param string|null $filterfactor The factor name to filter on.
     * @return string $html
     * @throws \coding_exception
     */
    public function active_factors(?string $filterfactor = null): string {
        global $USER, $CFG;

        require_once($CFG->dirroot . '/iplookup/lib.php');

        $html = '';

        $headers = get_strings([
            'devicename',
            'added',
            'lastused',
            'replace',
            'remove',
        ], 'tool_mfa');

        $table = new \html_table();
        $table->id = 'active_factors';
        $table->attributes['class'] = 'generaltable table table-bordered table-hover';
        $table->head  = [
            $headers->devicename,
            $headers->added,
            $headers->lastused,
            $headers->replace,
            $headers->remove,
        ];
        $table->colclasses = [
            'text-start',
            'text-start',
            'text-start',
            'text-center',
            'text-center',
        ];
        $table->data  = [];

        $factors = factor::get_enabled_factors();
        $hasmorethanone = factor::user_has_more_than_one_active_factors();

        foreach ($factors as $factor) {

            // Filter results to match the specified factor.
            if (!empty($filterfactor) && $factor->name !== $filterfactor) {
                continue;
            }

            $userfactors = $factor->get_active_user_factors($USER);

            if (!$factor->has_setup()) {
                continue;
            }

            foreach ($userfactors as $userfactor) {

                // Revoke option.
                if ($factor->has_revoke() && $hasmorethanone) {
                    $content = $headers->remove;
                    $attributes = [
                        'data-action' => 'revoke',
                        'data-factor' => $factor->name,
                        'data-factorid' => $userfactor->id,
                        'data-factorname' => $factor->get_display_name(),
                        'data-devicename' => $userfactor->label,
                        'aria-label' => get_string('revokefactor', 'tool_mfa'),
                        'class' => 'btn btn-primary mfa-action-button',
                    ];
                    $revokebutton = \html_writer::tag('button', $content, $attributes);
                } else {
                    $revokebutton = get_string('statusna');
                }

                // Replace option.
                if ($factor->has_replace()) {
                    $content = $headers->replace;
                    $attributes = [
                        'data-action' => 'replace',
                        'data-factor' => $factor->name,
                        'data-factorid' => $userfactor->id,
                        'data-factorname' => $factor->get_display_name(),
                        'data-devicename' => $userfactor->label,
                        'aria-label' => get_string('replacefactor', 'tool_mfa'),
                        'class' => 'btn btn-primary mfa-action-button',
                    ];
                    $replacebutton = \html_writer::tag('button', $content, $attributes);
                } else {
                    $replacebutton = get_string('statusna');
                }

                $timecreated  = $userfactor->timecreated == '-' ? '-'
                    : userdate($userfactor->timecreated,  get_string('strftimedatetime'));
                $lastverified = $userfactor->lastverified;
                if ($lastverified == 0) {
                    $lastverified = '-';
                } else if ($lastverified != '-') {
                    $lastverified = userdate($userfactor->lastverified, get_string('strftimedatetime'));
                    $lastverified .= '<br>';
                    $lastverified .= get_string('ago', 'core_message', format_time(time() - $userfactor->lastverified));
                }

                $row = new \html_table_row([
                    $userfactor->label,
                    $timecreated,
                    $lastverified,
                    $replacebutton,
                    $revokebutton,
                ]);
                $table->data[] = $row;
            }
        }
        // If table has no data, don't output.
        if (count($table->data) == 0) {
            return '';
        }
        $html .= \html_writer::table($table);
        $html .= '<br>';

        return $html;
    }

    /**
     * Generates notification text for display when user cannot login.
     *
     * @return string $notification
     */
    public function not_enough_factors(): string {
        global $CFG, $SITE;

        $notification = \html_writer::tag('h4', get_string('error:notenoughfactors', 'tool_mfa'));
        $notification .= \html_writer::tag('p', get_string('error:reauth', 'tool_mfa'));

        // Support link.
        $supportemail = $CFG->supportemail;
        if (!empty($supportemail)) {
            $subject = get_string('email:subject', 'tool_mfa',
                format_string($SITE->fullname, true, ['context' => system::instance()]));
            $maillink = \html_writer::link("mailto:$supportemail?Subject=$subject", $supportemail);
            $notification .= get_string('error:support', 'tool_mfa');
            $notification .= \html_writer::tag('p', $maillink);
        }

        // Support page link.
        $supportpage = $CFG->supportpage;
        if (!empty($supportpage)) {
            $linktext = \html_writer::link($supportpage, $supportpage);
            $notification .= $linktext;
        }
        $return = $this->output->notification($notification, 'notifyerror', false);

        // Logout button.
        $url = new \moodle_url('/admin/tool/mfa/auth.php', ['logout' => 1]);
        $btn = new \single_button($url, get_string('logout'), 'post', \single_button::BUTTON_PRIMARY);
        $return .= $this->render($btn);

        $return .= $this->get_support_link();

        return $return;
    }

    /**
     * Displays a table of all factors in use currently.
     *
     * @param int $lookback the period to view.
     * @return string the HTML for the table
     */
    public function factors_in_use_table(int $lookback): string {
        global $DB;

        $factors = factor::get_factors();

        // Setup 2 arrays, one with internal names, one pretty.
        $columns = [''];
        $displaynames = $columns;
        $colclasses = ['center', 'center', 'center', 'center', 'center'];

        // Force the first 4 columns to custom data.
        $displaynames[] = get_string('totalusers', 'tool_mfa');
        $displaynames[] = get_string('usersauthedinperiod', 'tool_mfa');
        $displaynames[] = get_string('nonauthusers', 'tool_mfa');
        $displaynames[] = get_string('nologinusers', 'tool_mfa');

        foreach ($factors as $factor) {
            $columns[] = $factor->name;
            $displaynames[] = get_string('pluginname', 'factor_'.$factor->name);
            $colclasses[] = 'right';
        }

        // Add total column to the end.
        $displaynames[] = get_string('total');
        $colclasses[] = 'center';

        $table = new \html_table();
        $table->head = $displaynames;
        $table->align = $colclasses;
        $table->attributes['class'] = 'generaltable table table-bordered w-auto table-hover';
        $table->attributes['style'] = 'width: auto; min-width: 50%; margin-bottom: 0;';

        // Manually handle Total users and MFA users.
        $alluserssql = "SELECT auth,
                            COUNT(id)
                        FROM {user}
                        WHERE deleted = 0
                        AND suspended = 0
                    GROUP BY auth";
        $allusersinfo = $DB->get_records_sql_menu($alluserssql);

        $noncompletesql = "SELECT u.auth, COUNT(u.id)
                             FROM {user} u
                        LEFT JOIN {tool_mfa_auth} mfaa ON u.id = mfaa.userid
                            WHERE u.lastlogin >= ?
                              AND (mfaa.lastverified < ?
                               OR mfaa.lastverified IS NULL)
                         GROUP BY u.auth";
        $noncompleteinfo = $DB->get_records_sql_menu($noncompletesql, [$lookback, $lookback]);

        $nologinsql = "SELECT auth, COUNT(id)
                         FROM {user}
                        WHERE deleted = 0
                          AND suspended = 0
                          AND lastlogin < ?
                     GROUP BY auth";
        $nologininfo = $DB->get_records_sql_menu($nologinsql, [$lookback]);

        $mfauserssql = "SELECT auth,
                            COUNT(DISTINCT tm.userid)
                        FROM {tool_mfa} tm
                        JOIN {user} u ON u.id = tm.userid
                        WHERE tm.lastverified >= ?
                        AND u.deleted = 0
                        AND u.suspended = 0
                    GROUP BY u.auth";
        $mfausersinfo = $DB->get_records_sql_menu($mfauserssql, [$lookback]);

        $factorsusedsql = "SELECT CONCAT(u.auth, '_', tm.factor) as id,
                                COUNT(*)
                            FROM {tool_mfa} tm
                            JOIN {user} u ON u.id = tm.userid
                            WHERE tm.lastverified >= ?
                            AND u.deleted = 0
                            AND u.suspended = 0
                            AND (tm.revoked = 0 OR (tm.revoked = 1 AND tm.timemodified > ?))
                        GROUP BY CONCAT(u.auth, '_', tm.factor)";
        $factorsusedinfo = $DB->get_records_sql_menu($factorsusedsql, [$lookback, $lookback]);

        // Auth rows.
        $authtypes = get_enabled_auth_plugins(true);
        foreach ($authtypes as $authtype) {
            $row = [];
            $row[] = \html_writer::tag('b', $authtype);

            // Setup the overall totals columns.
            $row[] = $allusersinfo[$authtype] ?? '-';
            $row[] = $mfausersinfo[$authtype] ?? '-';
            $row[] = $noncompleteinfo[$authtype] ?? '-';
            $row[] = $nologininfo[$authtype] ?? '-';

            // Create a running counter for the total.
            $authtotal = 0;

            // Now for each factor add the count from the factor query, and increment the running total.
            foreach ($columns as $column) {
                if (!empty($column)) {
                    // Get the information from the data key.
                    $key = $authtype . '_' . $column;
                    $count = $factorsusedinfo[$key] ?? 0;
                    $authtotal += $count;

                    $row[] = $count ? format_float($count, 0) : '-';
                }
            }

            // Append the total of all factors to final column.
            $row[] = $authtotal ? format_float($authtotal, 0) : '-';

            $table->data[] = $row;
        }

        // Total row.
        $totals = [0 => html_writer::tag('b', get_string('total'))];
        for ($colcounter = 1; $colcounter < count($row); $colcounter++) {
            $column = array_column($table->data, $colcounter);
            // Transform string to int forcibly, remove -.
            $column = array_map(function ($element) {
                return $element === '-' ? 0 : (int) $element;
            }, $column);
            $columnsum = array_sum($column);
            $colvalue = $columnsum === 0 ? '-' : $columnsum;
            $totals[$colcounter] = $colvalue;
        }
        $table->data[] = $totals;

        // Wrap in a div to cleanly scroll.
        return \html_writer::div(\html_writer::table($table), '', ['style' => 'overflow:auto;']);
    }

    /**
     * Displays a table of all factors in use currently.
     *
     * @return string the HTML for the table
     */
    public function factors_locked_table(): string {
        global $DB;

        $factors = factor::get_factors();

        $table = new \html_table();

        $table->attributes['class'] = 'generaltable table table-bordered w-auto table-hover';
        $table->attributes['style'] = 'width: auto; min-width: 50%';

        $table->head = [
            'factor' => get_string('factor', 'tool_mfa'),
            'active' => get_string('active'),
            'locked' => get_string('state:locked', 'tool_mfa'),
            'actions' => get_string('actions'),
        ];
        $table->align = [
            'left',
            'left',
            'right',
            'right',
        ];
        $table->data = [];
        $locklevel = (int) get_config('tool_mfa', 'lockout');

        foreach ($factors as $factor) {
            $sql = "SELECT COUNT(DISTINCT(userid))
                      FROM {tool_mfa}
                     WHERE factor = ?
                       AND lockcounter >= ?
                       AND revoked = 0";
            $lockedusers = $DB->count_records_sql($sql, [$factor->name, $locklevel]);
            $enabled = $factor->is_enabled() ? \html_writer::tag('b', get_string('yes')) : get_string('no');

            $actions = \html_writer::link( new \moodle_url($this->page->url,
                ['reset' => $factor->name, 'sesskey' => sesskey()]), get_string('performbulk', 'tool_mfa'));
            $lockedusers = \html_writer::link(new \moodle_url($this->page->url, ['view' => $factor->name]), $lockedusers);

            $table->data[] = [
                $factor->get_display_name(),
                $enabled,
                $lockedusers,
                $actions,
            ];
        }

        return \html_writer::table($table);
    }

    /**
     * Displays a table of all users with a locked instance of the given factor.
     *
     * @param object_factor $factor the factor class
     * @return string the HTML for the table
     */
    public function factor_locked_users_table(object_factor $factor): string {
        global $DB;

        $table = new \html_table();
        $table->attributes['class'] = 'generaltable table table-bordered w-auto table-hover';
        $table->attributes['style'] = 'width: auto; min-width: 50%';
        $table->head = [
            'userid' => get_string('userid', 'grades'),
            'fullname' => get_string('fullname'),
            'factorip' => get_string('ipatcreation', 'tool_mfa'),
            'lastip' => get_string('lastip'),
            'modified' => get_string('modified'),
            'actions' => get_string('actions'),
        ];
        $table->align = [
            'left',
            'left',
            'left',
            'left',
            'left',
            'right',
        ];
        $table->data = [];

        $locklevel = (int) get_config('tool_mfa', 'lockout');
        $sql = "SELECT mfa.id as mfaid, u.*, mfa.createdfromip, mfa.timemodified
                  FROM {tool_mfa} mfa
                  JOIN {user} u ON mfa.userid = u.id
                 WHERE factor = ?
                   AND lockcounter >= ?
                   AND revoked = 0";
        $records = $DB->get_records_sql($sql, [$factor->name, $locklevel]);

        foreach ($records as $record) {
            // Construct profile link.
            $proflink = \html_writer::link(new \moodle_url('/user/profile.php',
                ['id' => $record->id]), fullname($record));

            // IP link.
            $creatediplink = \html_writer::link(new \moodle_url('/iplookup/index.php',
                ['ip' => $record->createdfromip]), $record->createdfromip);
            $lastiplink = \html_writer::link(new \moodle_url('/iplookup/index.php',
                ['ip' => $record->lastip]), $record->lastip);

            // Deep link to logs.
            $logicon = $this->pix_icon('i/report', get_string('userlogs', 'tool_mfa'));
            $actions = \html_writer::link(new \moodle_url('/report/log/index.php', [
                'id' => 1, // Site.
                'user' => $record->id,
            ]), $logicon);

            $action = new \confirm_action(get_string('resetfactorconfirm', 'tool_mfa', fullname($record)));
            $actions .= $this->action_link(
                new \moodle_url($this->page->url, ['reset' => $factor->name, 'id' => $record->id, 'sesskey' => sesskey()]),
                $this->pix_icon('t/delete', get_string('resetconfirm', 'tool_mfa')),
                $action
            );

            $table->data[] = [
                $record->id,
                $proflink,
                $creatediplink,
                $lastiplink,
                userdate($record->timemodified, get_string('strftimedatetime', 'langconfig')),
                $actions,
            ];
        }

        return \html_writer::table($table);
    }

    /**
     * Returns a rendered support link.
     * If the MFA guidance page is enabled, this is returned.
     * Otherwise, the site support link is returned.
     * If neither support link is configured, an empty string is returned.
     *
     * @return string
     */
    public function get_support_link(): string {
        // Try the guidance page link first.
        if (get_config('tool_mfa', 'guidance')) {
            return $this->render_from_template('tool_mfa/guide_link', []);
        } else {
            return $this->output->supportemail([], true);
        }
    }

    /**
     * Renders an mform element from a template
     *
     * In certain situations, includes a script element which adds autosubmission behaviour.
     *
     * @param mixed $element element
     * @param bool $required if input is required field
     * @param bool $advanced if input is an advanced field
     * @param string|null $error error message to display
     * @param bool $ingroup True if this element is rendered as part of a group
     * @return mixed string|bool
     */
    public function mform_element(mixed $element, bool $required,
        bool $advanced, string|null $error, bool $ingroup): string|bool {
        $script = null;
        if ($element instanceof \tool_mfa\local\form\verification_field) {
            if ($this->page->pagelayout === 'secure') {
                $script = $element->secure_js();
            }
        }

        $result = parent::mform_element($element, $required, $advanced, $error, $ingroup);

        if (!empty($script) && $result !== false) {
            $result .= $script;
        }

        return $result;
    }

    /**
     * Renders the verification form.
     *
     * @param object_factor $factor The factor to render the form for.
     * @param login_form $form The login form object.
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     * @throws \moodle_exception
     */
    public function verification_form(object_factor $factor, login_form $form): string {
        $allloginfactors = factor::get_all_user_login_factors();
        $additionalfactors = [];
        $disabledfactors = [];
        $displaycount = 0;
        $disablefactor = false;

        foreach ($allloginfactors as $loginfactor) {
            if ($loginfactor->name != $factor->name) {
                $additionalfactor = [
                        'name' => $loginfactor->name,
                        'icon' => $loginfactor->get_icon(),
                        'loginoption' => get_string('loginoption', 'factor_' . $loginfactor->name),
                ];
                // We mark the factor as disabled if it is locked.
                // We store the disabled factors in a separate array so that they can be displayed at the bottom of the template.
                if ($loginfactor->get_state() == factor::STATE_LOCKED) {
                    $additionalfactor['loginoption'] = get_string('locked', 'tool_mfa', $additionalfactor['loginoption']);
                    $additionalfactor['disable'] = true;
                    $disabledfactors[] = $additionalfactor;
                } else {
                    $additionalfactors[] = $additionalfactor;
                }
                $displaycount++;
            }
        }

        // We merge the additional factors placing the disabled ones last.
        $alladitionalfactors = array_merge($additionalfactors, $disabledfactors);
        $hasadditionalfactors = $displaycount > 0;
        $authurl = new \moodle_url('/admin/tool/mfa/auth.php');

        // Set the form to better display vertically.
        $form->set_display_vertical();

        // Check if we need to display a remaining attempts message.
        $remattempts = $factor->get_remaining_attempts();
        $verificationerror = $form->get_element_error('verificationcode');
        if ($remattempts < get_config('tool_mfa', 'lockout') && !empty($verificationerror)) {
            // Update the validation error for the code form field to include the remaining attempts.
            $remattemptsstr = get_string('lockoutnotification', 'tool_mfa', $factor->get_remaining_attempts());
            $updatederror = $verificationerror . '&nbsp;' . $remattemptsstr;
            $form->set_element_error('verificationcode', $updatederror);
        }

        // If all attempts for this factor have been used, disable the form.
        // This forces the user to choose another factor or cancel their login.
        if ($remattempts <= 0) {
            $disablefactor = true;
            $form->freeze('verificationcode');

            // Handle the trust factor if present.
            if ($form->element_exists('factor_token_trust')) {
                $form->freeze('factor_token_trust');
            }
        }

        $context = [
                'logintitle' => get_string('logintitle', 'factor_'.$factor->name),
                'logindesc' => $factor->get_login_desc(),
                'factoricon' => $factor->get_icon(),
                'form' => $form->render(),
                'hasadditionalfactors' => $hasadditionalfactors,
                'additionalfactors' => $alladitionalfactors,
                'authurl' => $authurl->out(),
                'sesskey' => sesskey(),
                'supportlink' => $this->get_support_link(),
                'disablefactor' => $disablefactor
        ];
        return $this->render_from_template('tool_mfa/verification_form', $context);
    }
}
