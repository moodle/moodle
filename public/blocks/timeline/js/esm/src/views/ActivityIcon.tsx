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
 * Swizzlable wrapper around the design system ActivityIcon.
 *
 * Themes that need a custom icon can eject this component via the swizzle
 * manifest. All other code imports from @moodle/lms/block_timeline/views/ActivityIcon
 * so the override applies everywhere without touching call sites.
 *
 * @module     block_timeline/views/ActivityIcon
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {ActivityIcon as DSActivityIcon} from '@moodlehq/design-system';

// Moodle module names → DS ActivityIcon icon names.
// Names omitted here match the DS icon name directly (e.g. quiz, forum, wiki)
// and fall through to the modulename itself.
const MODULE_NAME_MAP: Record<string, string> = {
    assign:          'assignment',
    bigbluebuttonbn: 'bigbluebutton',
    data:            'database',
    h5pactivity:     'h5p',
    imscp:           'ims-package',
    label:           'text-and-media',
    lti:             'external-tool',
    scorm:           'scorm-package',
    qbank:           'file-database',
};

// Resource module icons vary per uploaded file's mimetype (file_file_icon() in
// lib/filelib.php), encoded in iconurl as an "f/<type>" path segment. Map Moodle's
// file type key to the closest DS file-* icon.
// ponytail: regex-sniffs an internal URL format rather than a stable field —
// upgrade to a dedicated filetype field on the calendar event_icon_exporter if this breaks.
const FILE_TYPE_MAP: Record<string, string> = {
    archive:     'file-archive',
    audio:       'file-audio',
    calc:        'file-spreadsheet',
    chart:       'file-graphic',
    database:    'file-database',
    document:    'file-doc',
    draw:        'file-draw',
    eps:         'file-eps',
    epub:        'file-epub',
    flash:       'file-flash',
    gif:         'file-gif',
    h5p:         'file-h5p',
    image:       'file-image',
    impress:     'file-presentation',
    isf:         'file-isf-flowchart',
    json:        'file-json',
    markup:      'file-code',
    math:        'file-math',
    moodle:      'file-moodle',
    oth:         'file-oth',
    pdf:         'file-pdf',
    powerpoint:  'file-ppt',
    psd:         'file-psd',
    publisher:   'file-pub',
    sourcecode:  'file-source-code',
    spreadsheet: 'file-xls',
    text:        'file-plain-text',
    unknown:     'file-unknown',
    video:       'file-video',
    writer:      'file-text-editor',
};

interface ActivityIconProps {
    modulename: string;
    iconurl: string;
    alt?: string;
}

/**
 * Extracts the "f/<type>" file-type segment Moodle embeds in resource icon
 * URLs (path form "/f/pdf" or query form "f%2Fpdf") and resolves it to a DS icon.
 *
 * @param iconurl the resource's icon URL, as returned by the calendar event_icon_exporter.
 */
function resolveResourceIcon(iconurl: string): string {
    const match = iconurl.match(/f(?:\/|%2f)([a-z0-9_-]+)/i);
    const filetype = match ? match[1].toLowerCase() : '';
    return FILE_TYPE_MAP[filetype] ?? 'file';
}

/**
 * Resolves a Moodle modulename (and, for resource activities, its iconurl) to a
 * DS ActivityIcon icon name.
 *
 * @param modulename Moodle module name, e.g. "assign", "resource"; may be missing.
 * @param iconurl the activity's icon URL, only inspected when modulename is "resource".
 */
function resolveIcon(modulename: string, iconurl: string): string {
    if (!modulename || modulename === 'undefined') {
        return 'file-unknown';
    }
    if (modulename === 'resource') {
        return resolveResourceIcon(iconurl);
    }
    return MODULE_NAME_MAP[modulename] ?? modulename;
}

/** Renders an activity's icon via the design system ActivityIcon. */
export function ActivityIcon({modulename, iconurl, alt = ''}: ActivityIconProps) {
    return <DSActivityIcon icon={resolveIcon(modulename, iconurl)} alt={alt} container="none" size="xl" />;
}
