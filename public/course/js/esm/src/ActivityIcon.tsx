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
 * The icon of a course activity card, rendered with the design system ActivityIcon.
 *
 * Mounted from core_courseformat/local/content/cm/cmicon, which keeps a server-rendered
 * fallback inside the mount point until this component takes over. Keep the size of that
 * fallback, including its size on small screens, in sync with this component.
 *
 * @module     core_course/ActivityIcon
 * @copyright  2026 Moodle Pty Ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useEffect, useState} from 'react';
import {ActivityIcon as DSActivityIcon} from '@moodlehq/design-system';

// Matches the small screens of the course card layout (media-breakpoint-down(sm) in theme_boost course.scss).
const smallScreenQuery = '(max-width: 575.98px)';

export type ActivityIconProps = {
    /** The design system icon name, resolved by core_courseformat\output\local\content\cm\cmicon. */
    icon: string;
    /** The accessible text of the icon. Empty when the icon is decorative. */
    alt?: string;
    /** The container around the icon: none while editing, default otherwise. */
    container?: 'none' | 'default' | 'large';
    /** The size of the icon. */
    size?: 'sm' | 'md' | 'lg' | 'xl';
    /** The size of the icon on small screens. Defaults to the size. */
    mobileSize?: 'sm' | 'md' | 'lg' | 'xl';
};

/**
 * Whether the screen is small, following changes of the window size.
 *
 * @returns True on small screens.
 */
const useSmallScreen = (): boolean => {
    const [isSmall, setIsSmall] = useState(() => globalThis.matchMedia(smallScreenQuery).matches);
    useEffect(() => {
        const query = globalThis.matchMedia(smallScreenQuery);
        const update = (event: MediaQueryListEvent) => {
            setIsSmall(event.matches);
        };

        setIsSmall(query.matches);
        query.addEventListener('change', update);
        return () => {
            query.removeEventListener('change', update);
        };
    }, []);
    return isSmall;
};

/**
 * Render the activity icon.
 *
 * @param props Component props.
 * @returns The rendered activity icon.
 */
export default function ActivityIcon({
    icon,
    alt = '',
    container = 'default',
    size = 'md',
    mobileSize = size,
}: ActivityIconProps) {
    const isSmallScreen = useSmallScreen();
    return <DSActivityIcon icon={icon} alt={alt} container={container} size={isSmallScreen ? mobileSize : size} />;
}
