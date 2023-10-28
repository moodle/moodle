<?php

    require_once("../../config.php");
    require_once($CFG->dirroot."/auth/shibboleth/auth.php");

    $idp = optional_param('idp', null, PARAM_RAW);

    // Check for timed out sessions.
    if (!empty($SESSION->has_timed_out)) {
        $session_has_timed_out = true;
        $SESSION->has_timed_out = false;
    } else {
        $session_has_timed_out = false;
    }

    // Define variables used in page.
    $isvalid = true;
    $site = get_site();

    $loginsite = get_string("loginsite");

    $loginurl = (!empty($CFG->alternateloginurl)) ? $CFG->alternateloginurl : '';

    $config = get_config('auth_shibboleth');
    if (!empty($CFG->registerauth) or is_enabled_auth('none') or !empty($config->auth_instructions)) {
        $showinstructions = true;
    } else {
        $showinstructions = false;
    }

    $idplist = get_idp_list($config->organization_selection);
    if (isset($idp)) {
        if (isset($idplist[$idp])) {
            set_saml_cookie($idp);

            $targeturl = new moodle_url('/auth/shibboleth/index.php');
            $idpinfo = $idplist[$idp];

            // Redirect to SessionInitiator with entityID as argument.
            if (isset($idpinfo[1]) && !empty($idpinfo[1])) {
                $sso = $idpinfo[1];
            } else {
                $sso = '/Shibboleth.sso';
            }
            // For Shibboleth 1.x Service Providers.
            header('Location: ' . $sso . '?providerId=' . urlencode($idp) . '&target=' . urlencode($targeturl->out()));

        } else {
            $isvalid = false;
        }
    }

    $loginsite = get_string("loginsite");

    $PAGE->set_url('/auth/shibboleth/login.php');
    $PAGE->set_context(context_system::instance());
    $PAGE->navbar->add($loginsite);
    $PAGE->set_title($loginsite);
    $PAGE->set_heading($site->fullname);
    $PAGE->set_pagelayout('login');

    echo $OUTPUT->header();

    if (isloggedin() and !isguestuser()) {
        // Prevent logging when already logged in, we do not want them to relogin by accident because sesskey would be changed.
        echo $OUTPUT->box_start();
        $params = array('sesskey' => sesskey(), 'loginpage' => 1);
        $logout = new single_button(new moodle_url('/login/logout.php', $params), get_string('logout'), 'post');
        $continue = new single_button(new moodle_url('/'), get_string('cancel'), 'get');
        echo $OUTPUT->confirm(get_string('alreadyloggedin', 'error', fullname($USER)), $logout, $continue);
        echo $OUTPUT->box_end();
    } else {
        // Print login page.
        $selectedidp = '-';
        if (isset($_COOKIE['_saml_idp'])) {
            $idpcookie = generate_cookie_array($_COOKIE['_saml_idp']);
            do {
                $selectedidp = array_pop($idpcookie);
            } while (!isset($idplist[$selectedidp]) && count($idpcookie) > 0);
        }

        $idps = [];
        foreach ($idplist as $value => $data) {
            $name = reset($data);
            $selected = $value === $selectedidp;
            $idps[] = (object)[
                'name' => $name,
                'value' => $value,
                'selected' => $selected
            ];
        }

        // Whether the user can sign up.
        $cansignup = !empty($CFG->registerauth);
        // Default instructions.
        $instructions = format_text($config->auth_instructions);
        if (is_enabled_auth('none')) {
            $instructions = get_string('loginstepsnone');
        } else if ($cansignup) {
            if ($CFG->registerauth === 'email' && empty($instructions)) {
                $instructions = get_string('loginsteps');
            }
        }

        // Build the template context data.
        $templatedata = (object)[
            'adminemail' => get_admin()->email,
            'cansignup' => $cansignup,
            'guestlogin' => $CFG->guestloginbutton,
            'guestloginurl' => new moodle_url('/login/index.php'),
            'idps' => $idps,
            'instructions' => $instructions,
            'loginname' => $config->login_name ?? null,
            'logintoken' => \core\session\manager::get_login_token(),
            'loginurl' => new moodle_url('/auth/shibboleth/login.php'),
            'showinstructions' => $showinstructions,
            'signupurl' => new moodle_url('/login/signup.php'),
            'isvalid' => $isvalid
        ];

        // Render the login form.
        echo $OUTPUT->render_from_template('auth_shibboleth/login_form', $templatedata);
    }

    echo $OUTPUT->footer();
