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
 * A course activity date (e.g. "Opens: Monday, 1 January 2024, 12:00 AM").
 *
 * @module     core_course/ActivityDate
 * @copyright  2026 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {Badge} from '@moodlehq/design-system';

export interface ActivityDateProps {
    /** The label of the date, e.g. "Opens:". */
    label: string;
    /** The formatted date. */
    value: string;
}

/**
 * Render the activity date.
 *
 * @param props Component props.
 * @returns The rendered activity date.
 */
export default function ActivityDate({label, value}: ActivityDateProps) {
    const content = (
        <>
            <span>{label}</span>
            {' '}
            <span>{value}</span>
        </>
    );
    // The design system Badge types `label` as a string, but renders it as children, so it accepts a node at runtime.
    // We need the label and the date in separate spans, so cast until the design system accepts a ReactNode label.
    return <Badge label={content as unknown as string} variant="secondary" subtle />;
}
