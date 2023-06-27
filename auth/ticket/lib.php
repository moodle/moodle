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
 * @package     auth_ticket
 * @category    auth
 * @author      Valery Fremaux <valery.fremaux@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL
 * @copyright   (C) 2010 ValEISTI (http://www.valeisti.fr)
 * @copyright   (C) 2012 onwards Valery Fremaux (http://www.mylearningfactory.com)
 *
 * Ticket related library
 */
defined('MOODLE_INTERNAL') || die;

/**
 * Simple sending to user with return ticket.
 * The return ticket allows auser receiving amail to enter immediately
 * the platform being connected automatically during a hold time.
 * the ticket is catched by a custom auth module that decodes generated ticket and
 * let user through.
 * Only recipients that have a valid Moodle account can use an access tickets.
 * The ticket is only valid on the given return URL and cannot be used for going
 * to another location, unless user's profile other mention.
 *
 * @param object $recipient
 * @param object $sender
 * @param string $title mail subject
 * @param string $notification raw content of the mail
 * @param string $notification_html html content of the mail
 * @param string $url return url of the ticket
 * @param string $purpose some textual comment on what the ticket was for
 * @param bool $term the ticket validity duration, may be 'short', 'long' or 'persistant'.
 */
function ticket_notify($recipient, $sender, $title, $notification, $notificationhtml, $url, $purpose = '', $term = 'short') {
    global $CFG;

    if (!empty($url)) {
        $ticket = ticket_generate($recipient, $purpose, $url, $term);
        $notificationhtml = str_replace('<%%TICKET%%>', $ticket, $notificationhtml);
    } else {
        // Get rid of placeholder if not used.
        $notification = str_replace('<%%TICKET%%>', '', $notificationhtml);
    }
    // Tickets only can be sent as HTML href values.
    $notification = str_replace('<%%TICKET%%>', '', $notification);

    // Todo send the email to user.
    if ($CFG->debugsmtp) {
        echo "Sending Mail Notification to " . fullname($recipient) .'<br/>'.$notificationhtml;
    }
    return email_to_user($recipient, $sender, $title, $notification, $notificationhtml);
}

/**
 * Sends a notification message to all users having the role in the given context.
 *
 * Note that general form of URL to propose a return ticket encoded url is : 
 * %WWWROOT%/login/index.php?ticket=%TICKET%
 *
 * @param int $roleid id of the role to search users on
 * @param object $context context in which find users with the role
 * @param object $sender user identity of the sender
 * @param string $title mail subject
 * @param string $notification raw content of the mail
 * @param string $notification_html html content of the mail
 * @param string $url return url of the ticket
 * @param string $purpose some textual comment on what the ticket was for
 * @param bool $checksendall if true, the function returns true if all the recipients were sucessfull
 * @param bool $term the ticket validity duration, may be 'short', 'long' or 'persistant'.
 * @return true if at least one email could be sent or all are sent depending on $checksendall.
 */
function ticket_notifyrole($roleid, $context, $sender, $title, $notification, $notificationhtml, $url, $purpose = '',
                           $checksendall = false, $term = 'short') {
    global $CFG, $DB;

    // Get all users assigned to that role in context.
    $role = $DB->get_record('role', array('id' => $roleid));
    $assigns = get_users_from_role_on_context($role, $context);

    $result = $checksendall;
    foreach ($assigns as $assign) {
        $fields = 'id, username,'.get_all_user_name_fields(true, '').', email, emailstop, mailformat';
        $user = $DB->get_record('user', array('id' => $assign->userid), $fields);
        $ticket = ticket_generate($user, $purpose, $url, $term);
        $notification = str_replace('<%%TICKET%%>', $ticket, $notification);
        $notificationhtml = str_replace('<%%TICKET%%>', $ticket, $notificationhtml);

        // Todo send the email to user.
        if ($CFG->debugsmtp) {
            echo "Sending Mail Notification to ".fullname($user).'<br/>'.$notification;
        } else {
            if ($checksendall) {
                $result = $result && email_to_user($user, $sender, $title, $notification, $notificationhtml);
            } else {
                $result = $result || email_to_user($user, $sender, $title, $notification, $notificationhtml);
            }
        }
    }

    return $result;
}

/**
 * Generates a direct access ticket for this user. three generation methods are provided.
 * - Internel : a weak method but does not relay on encryption libraries.
 * - des : uses the Mysql DES encryption function. Will NOT work on other databases.
 * - rsa : Uses Moodle MNET local key. Assumes we have initialized mnet. Care that the ticket may be
 * rejected if the key changes. This will have impact on 'persistance' or 'long' term tickets.
 *
 * @param object $user a user object
 * @param string $reason the reason of the ticket
 * @param string $url the access URL the user will be redirected to after validating his return ticket.
 * @param string $method the encryption algorithm, 'des' or 'rsa', or 'internal'.
 * @param string $term the validity delay range in 'short', 'long', or 'persistance'.
 * @return string an encrypted ticket
 */
function ticket_generate($user, $reason, $url, $method = null, $term = 'short') {
    global $CFG, $DB, $SITE;

    $config = get_config('auth_ticket');

    if (is_null($method)) {
        $method = $config->encryption;
    }

    if (empty($user->username)) {
        return;
    }

    $ticket = new StdClass();
    $ticket->username = $user->username;
    if (!empty($reason)) {
        $ticket->reason = $reason;
    }
    if (!empty($url)) {
        $ticket->wantsurl = ''.$url; // Ensure we stringify.
    }
    $ticket->term = $term;
    $ticket->date = time();

    $keyinfo = json_encode($ticket);

    if ($method == 'internal') {

        $key = $config->internalseed;
        if (empty($config->internalseed)) {
            $key = md5($SITE->fullname);
        }

        while (strlen($key) < strlen($keyinfo)) {
            // Pad key onto itself to get a key larger than the text.
            $key .= $key;
        }

        $encrypted = '';

        // Iterate through each character
        for ($i = 0; $i < strlen($keyinfo); $i++) {
                $encrypted .= $keyinfo{$i} ^ $key{$i};
        }
    } else if ($method == 'rsa') {

        include_once($CFG->dirroot.'/mnet/lib.php');
        $keypair = mnet_get_keypair();

        if (!openssl_public_encrypt($keyinfo, $encrypted, $keypair['publickey'])) {
            print_error("Failed making encoded ticket");
        }
    } else {
        $pkey = '';
        if (!empty($CFG->passwordsaltmain)) {
            $pkey = substr(base64_encode($CFG->passwordsaltmain), 0, 16);
        }
        $sql = "
            SELECT
                HEX(AES_ENCRYPT(?, ?)) as result
        ";

        if ($result = $DB->get_record_sql($sql, array($keyinfo, $pkey))) {
            $encrypted = $result->result;
        } else {
            $encrypted = 'encryption error';
        }
    }

    return base64_encode($encrypted); // Make sure we can emit this ticket through an URL.
}

/**
 * Decodes a direct access ticket for this user.
 *
 * @param string $encrypted the received ticket
 * @param string $method the decrypt method. Supports 'des' using DB internal function or 'rsa' using openssl layer.
 * @return a decoded ticket object
 */
function ticket_decode($encrypted, $method = null) {
    global $CFG, $DB, $SITE;

    $config = get_config('auth_ticket');

    if (is_null($method)) {
        $method = $config->encryption;
    }

    $encrypted = base64_decode($encrypted);

    if ($method == 'internal') {
        $key = $config->internalseed;
        if (empty($config->internalseed)) {
            $key = md5($SITE->fullname);
        }

        while (strlen($key) < strlen($encrypted)) {
            // Pad key onto itself to get a key larger than the text.
            $key .= $key;
        }

        $decrypted = '';

        // Iterate through each character
        for ($i = 0; $i < strlen($encrypted); $i++) {
            $decrypted .= $encrypted{$i} ^ $key{$i};
        }
    } else if ($method == 'rsa') {
        // Using RSA.

        include_once($CFG->dirroot.'/mnet/lib.php');
        $keypair = mnet_get_keypair();

        if (!openssl_private_decrypt(urldecode($encrypted), $decrypted, $keypair['privatekey'])) {
            print_error('decoderror', 'auth_ticket', $method);
        }
    } else {
        $pkey = substr(base64_encode(@$CFG->passwordsaltmain), 0, 16);
        $sql = "
            SELECT
                AES_DECRYPT(UNHEX(?), ?) as result
        ";

        if ($result = $DB->get_record_sql($sql, array($encrypted, $pkey))) {
            $decrypted = $result->result;
        } else {
            $decrypted = 'encryption error';
        }
    }

    if (!$ticket = json_decode(str_replace('/', "\\/", $decrypted))) {
        print_error('ticketerror', 'auth_ticket', '', $method);
    }

    return $ticket;
}

/**
 * checks conditions for ticket internal data validity and initiate the $USER if ticket is valid.
 * @param string $ticket
 * @return true if ticket is accepted.
 */
function ticket_accept($ticket, &$gotourl = null) {
    global $DB;

    $config = get_config('auth_ticket');

    switch ($ticket->term) {
        case 'short' :
            if ($ticket->date < time() - $config->shortvaliditydelay) {
                return false;
            }
            break;

        case 'long' :
            if ($ticket->date < time() - $config->longvaliditydelay) {
                return false;
            }
            break;

        case 'persistant' :
            if ($config->persistantvaliditydelay == 0) {
                return true;
            }
            if ($ticket->date < time() - $config->persistantvaliditydelay) {
                return false;
            }
            assert(1);
    }

    if (empty($ticket->username)) {
        return false;
    }

    if (!$user = $DB->get_record('user', array('username' => $ticket->username))) {
        return false;
    }

    $USER = $user;

    $gotourl = @$ticket->wantsurl;

    return true;
}