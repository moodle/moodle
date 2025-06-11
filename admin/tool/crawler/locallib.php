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
 * Helper lib
 *
 * @package    tool_crawler
 * @copyright  2016 Brendan Heywood <brendan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_crawler\local\url;

defined('MOODLE_INTERNAL') || die();

/**
 * Renders a link as HTML.
 *
 * Outputs a link from the given link text to the URL detail page, with an added arrow linked to the link target itself. These are
 * followed by the URL in text form (intended for the user).
 *
 * If a redirect link is passed, renders this as well in an additional separate line, as a link to the redirection URL.
 *
 * The link text can be given either as plain text (which will then be properly escaped for HTML output) or as an HTML snippet (in
 * which case the caller must already have ensured that everything is properly escaped if necessary).
 *
 * @param string $url The URL to which the link points.
 * @param string $label The link text. Can be plain text or an HTML snippet; select mode with parameter $labelishtml.
 * @param string $redirect The final URL if a redirect was served.
 * @param string $labelishtml Whether the $label parameter contains an HTML snippet (if true) or plain text (if false). Defaults to
 *               plain text.
 * @return string HTML snippet which can be used in output.
 */
function tool_crawler_link($url, $label, $redirect = '', $labelishtml = false) {
    if (empty($label)) {
        // Ensure that label is always at least a string.
        $label = '';
    }
    if (!$labelishtml) {
        $label = htmlspecialchars($label, ENT_NOQUOTES | ENT_HTML401);
    }

    $canviewsitelevelreports = has_capability('moodle/site:config', context_system::instance());
    $html = $canviewsitelevelreports ? html_writer::link(new moodle_url('url.php', array('url' => $url)), $label) : $label;
    $html .= '<br><small>' . html_writer::link($url, htmlspecialchars($url, ENT_NOQUOTES | ENT_HTML401), ['target' => 'link']) . '</small>';

    if ($redirect) {
        $linkhtmlsnippet = html_writer::link($redirect, htmlspecialchars($redirect, ENT_NOQUOTES | ENT_HTML401));
        $html .= "<br>" . get_string('redirect', 'tool_crawler', array('redirectlink' => $linkhtmlsnippet));
    }

    return $html;
}

/**
 * Renders a priority level integer as readable HTML
 *
 * @param int $priority the priority level
 * @return string HTML snippet which can be used in output.
 */
function tool_crawler_priority_level($priority) {

    $html = '';
    if ($priority == TOOL_CRAWLER_PRIORITY_DEFAULT) {
        $html = html_writer::div(get_string('defaultpriority', 'tool_crawler'), 'priority');
    }
    if ($priority == TOOL_CRAWLER_PRIORITY_NORMAL) {
        $html = html_writer::div(get_string('normalpriority', 'tool_crawler'), 'priority');
    }
    if ($priority == TOOL_CRAWLER_PRIORITY_HIGH) {
        $html = html_writer::div(get_string('highpriority', 'tool_crawler'), 'priority');
    }
    if (!$html) {
        $html = html_writer::div(get_string('custompriority', 'tool_crawler', $priority), 'priority');
    }
    return $html;
}
/**
 * Get a html code chunk
 *
 * @param integer $row row
 * @return html chunk
 */
function tool_crawler_http_code($row) {
    if (isset($row->errormsg) && !is_null($row->errormsg)) {
        $msg = get_string('fetcherror', 'tool_crawler', ['errormessage' => $row->errormsg]);
    } else {
        $msg = isset($row->httpmsg) && !is_null($row->httpmsg) ? $row->httpmsg : get_string('symbolforunknown', 'tool_crawler');
    }
    $msg = htmlspecialchars($msg, ENT_NOQUOTES | ENT_HTML401);

    $code = $row->httpcode;
    $cc = substr($code, 0, 1);
    $code = "$msg<br><small class='link-$cc"."xx'>$code</small>";
    return $code;
}

/**
 * Formats information about a resource size for being presented to the user. This includes annotation about whether the size is
 * known or whether it may be inexact. This also includes locale-specific conversions and formatting if applicable.
 *
 * @param object $sizeinfo An object with at least the two properties `filesize` and `filesizestatus`. The latter must use one of
 *               the `TOOL_CRAWLER_FILESIZE_*` constants defined in `lib.php`. This object is usually a row obtained from the
 *               database.
 * @return string The formatted size string which can be shown to the user.
 */
function tool_crawler_displaysize($sizeinfo) {
    // Do not compare filesizestatus with ‚===‘ because the object from the database contains the int as string.
    if (!is_null($sizeinfo->filesizestatus) && $sizeinfo->filesizestatus == TOOL_CRAWLER_FILESIZE_UNKNOWN) {
        // Have tried to find out size, but still unknown due to aborted download.
        $size = get_string('symbolforunknown', 'tool_crawler');
    } else if (is_null($sizeinfo->filesize) || is_null($sizeinfo->filesizestatus)) {
        // Size unknown due to some error, or status of size unknown.
        $size = '';
    } else {
        // Something (or everything) is known about the resource size.
        $size = $sizeinfo->filesize * 1;
        $size = display_size($size);
        if ($sizeinfo->filesizestatus == TOOL_CRAWLER_FILESIZE_ATLEAST) {
            $size = get_string('greaterthansize', 'tool_crawler', ['size' => $size]);
        }
    }

    return $size;
}

/**
 * Formats a number according to the current user’s locale.
 *
 * @param float $number Numeric value to format.
 * @param int $decimals Number of decimals after the decimal separator. Defaults to 0.
 * @return string String with number formatted as per user’s locale.
 */
function tool_crawler_numberformat(float $number, int $decimals = 0) {
    $decsep = get_string('decsep', 'langconfig');
    $thousandssep = get_string('thousandssep', 'langconfig');

    return number_format($number, $decimals, $decsep, $thousandssep);
}

/**
 * Produces a filter for SQL queries which will limit a query to big links. The
 * two parts of the filter are returned in an associative array.
 *
 * NB: resources with an unknown size will pass the filter as “maybe big”.
 * However, resources which are shorter than the configured big file size, but
 * have an inexact length stored will not be returned marked as big.
 *
 * @param string $tablealias Name to which the `tool_crawler_url` table is
 *              aliased in the final SQL statement. If empty or `null`, the
 *              columns will be referenced without an explicit table name.
 *
 * @return array Associative array with entries for `wherecondition` and
 *              `params`. The value for `wherecondition` is a string which can
 *              be added to a `WHERE` clause in SQL; the condition, if
 *              non-empty, already has (round) brackets around it to allow for
 *              combination with other conditions. The value for `params` is an
 *              array with entries forming the SQL parameters that should be
 *              used in the query.
 */
function tool_crawler_sql_oversize_filter($tablealias = null) {
    if (is_string($tablealias) && $tablealias !== '') {
        $tbl = $tablealias . '.';
    } else {
        $tbl = '';
    }

    $where = "( {$tbl}filesize > ?
             OR ( {$tbl}filesize IS NULL
                  AND {$tbl}lastcrawled IS NOT NULL
                )
             OR {$tbl}filesizestatus = ?
              )";

    $bigfilesize = get_config('tool_crawler', 'bigfilesize');
    $params = array(
            $bigfilesize * 1000000,
            TOOL_CRAWLER_FILESIZE_UNKNOWN,
            );

    return array(
        'wherecondition' => $where,
        'params' => $params,
    );
}

/**
 * Generates a nice table for the URL detail page.
 *
 * @param array $data table
 * @return html output
 */
function tool_crawler_url_gen_table($data) {
    $table = new html_table();
    $table->head = array(
        get_string('lastcrawledtime', 'tool_crawler'),
        get_string('linktext', 'tool_crawler'),
        get_string('idattr', 'tool_crawler'),
        get_string('response', 'tool_crawler'),
        get_string('size', 'tool_crawler'),
        get_string('url', 'tool_crawler'),
        get_string('mimetype', 'tool_crawler'),
    );
    $datetimeformat = get_string('strftimerecentsecondshtml', 'tool_crawler');
    $table->data = array();
    foreach ($data as $row) {
        $title = trim($row->title);
        if ($title == "") {
            $title = get_string('unknown', 'tool_crawler');
        }
        $code = tool_crawler_http_code($row);
        $idattr = htmlspecialchars($row->idattr, ENT_NOQUOTES | ENT_HTML401);
        $data = array(
            userdate($row->lastcrawled, $datetimeformat),
            htmlspecialchars($row->text, ENT_NOQUOTES | ENT_HTML401),
            str_replace(' #', '<br>#', $idattr),
            $code,
            htmlspecialchars(tool_crawler_displaysize($row), ENT_NOQUOTES | ENT_HTML401),
            tool_crawler_link($row->target, $title, $row->redirect),
            htmlspecialchars($row->mimetype, ENT_NOQUOTES | ENT_HTML401),
        );
        $table->data[] = $data;
    }
    return html_writer::table($table);
}

/**
 * Generates and returns a full HTML page with details about a URL.
 *
 * @param string $url The URL.
 * @return string A HTML page about the URL.
 */
function tool_crawler_url_create_page($url) {
    global $PAGE, $OUTPUT, $DB;

    require_login(null, false);
    $context = context_system::instance();
    require_capability('moodle/site:config', $context);

    $navurl = new moodle_url('/admin/tool/crawler/url.php', array(
        'url' => $url
    ));
    $PAGE->set_context($context);
    $PAGE->set_url($navurl);
    $PAGE->set_pagelayout('admin');
    $PAGE->set_title(get_string('urldetails', 'tool_crawler') );

    $page = $OUTPUT->header();

    $page .= $OUTPUT->heading(get_string('urldetails', 'tool_crawler'));
    $urldetailshelp = get_string('urldetails_help', 'tool_crawler');
    $urldetailshelp = htmlspecialchars($urldetailshelp, ENT_NOQUOTES | ENT_HTML401);
    $urldetailshelp = preg_replace('/(\r\n?|\n)/', '<br>', $urldetailshelp);
    $page .= '<p>' . $urldetailshelp . '</p>';

    $urlrec = $DB->get_record('tool_crawler_url', array('urlhash' => url::hash_url($url)));
    $page .= '<h2>' . tool_crawler_link($url, $urlrec->title, $urlrec->redirect) . '</h2>';

    $page .= '<h3>' . htmlspecialchars(get_string('outgoingurls', 'tool_crawler'), ENT_NOQUOTES | ENT_HTML401) . '</h3>';

    $data  = $DB->get_records_sql("
         SELECT concat(l.a, '-', l.b) AS id,
                l.text,
                l.idattr,
                t.url target,
                t.title,
                t.redirect,
                t.httpmsg,
                t.errormsg,
                t.httpcode,
                t.filesize,
                t.filesizestatus,
                t.lastcrawled,
                t.mimetype
           FROM {tool_crawler_edge} l
           JOIN {tool_crawler_url} f ON f.id = l.a
           JOIN {tool_crawler_url} t ON t.id = l.b
          WHERE f.urlhash = ?
       ORDER BY f.lastcrawled DESC
    ", array(url::hash_url($url)));

    $page .= tool_crawler_url_gen_table($data);

    $page .= '<h3>' . htmlspecialchars(get_string('incomingurls', 'tool_crawler'), ENT_NOQUOTES | ENT_HTML401) . '</h3>';

    $data  = $DB->get_records_sql("
         SELECT concat(l.a, '-', l.b) AS id,
                l.text,
                l.idattr,
                f.url target,
                f.title,
                f.redirect,
                f.httpmsg,
                f.errormsg,
                f.httpcode,
                f.filesize,
                f.filesizestatus,
                f.lastcrawled,
                f.mimetype
           FROM {tool_crawler_edge} l
           JOIN {tool_crawler_url} f ON f.id = l.a
           JOIN {tool_crawler_url} t ON t.id = l.b
          WHERE t.urlhash = ?
       ORDER BY f.lastcrawled DESC
    ", array(url::hash_url($url)));

    $page .= tool_crawler_url_gen_table($data);

    $page .= $OUTPUT->footer();

    return $page;
}
