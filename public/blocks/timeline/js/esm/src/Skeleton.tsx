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
 * Skeleton shimmer placeholders for the Timeline block loading states.
 *
 * Mirrors the legacy placeholder-event-list-item.mustache and
 * course-item-loading-placeholder.mustache templates exactly.
 * Uses the bg-pulse-grey animation class from the theme.
 *
 * @module     block_timeline/Skeleton
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Single shimmer row — mirrors placeholder-event-list-item.mustache.
 * Uses inline styles to match the original template exactly, independent of theme cache.
 */
export function PlaceholderEventItem() {
    return (
        <li className="list-group-item px-2">
            <div className="row">
                <div className="col-8 pe-0">
                    <div className="d-flex flex-row align-items-center" style={{height: '32px'}}>
                        <div className="bg-pulse-grey rounded-circle" style={{height: '32px', width: '32px'}} />
                        <div style={{flex: 1}} className="ps-2">
                            <div className="bg-pulse-grey w-100" style={{height: '15px'}} />
                            <div className="bg-pulse-grey w-75 mt-1" style={{height: '10px'}} />
                        </div>
                    </div>
                </div>
                <div className="col-4 pe-3">
                    <div className="d-flex flex-row justify-content-end" style={{height: '32px', paddingTop: '2px'}}>
                        <div className="bg-pulse-grey w-75" style={{height: '15px'}} />
                    </div>
                </div>
            </div>
        </li>
    );
}

/**
 * Dates-view loading state — mirrors the event-list-loading-placeholder region.
 *
 * 5 shimmer event rows followed by a button-sized skeleton block.
 */
export function DatesViewSkeleton() {
    return (
        <div data-region="event-list-loading-placeholder">
            <ul className="ps-0 list-group list-group-flush">
                {Array.from({length: 5}).map((_, i) => (
                    <PlaceholderEventItem key={i} />
                ))}
            </ul>
            <div className="pt-3 pb-2 d-flex justify-content-between">
                <div className="w-25 bg-pulse-grey" style={{height: '35px'}} />
            </div>
        </div>
    );
}

/**
 * Single shimmer course block — mirrors course-item-loading-placeholder.mustache.
 * Uses inline styles to match the original template exactly, independent of theme cache.
 */
function PlaceholderCourseItem() {
    return (
        <li className="list-group-item mt-3 p-0 px-2 border-0">
            <div className="w-50 bg-pulse-grey mt-1 mb-2" style={{height: '20px'}} />
            <div>
                <ul className="ps-0 list-group list-group-flush">
                    {Array.from({length: 5}).map((_, i) => (
                        <PlaceholderEventItem key={i} />
                    ))}
                </ul>
                <div className="pt-3 pb-2 d-flex justify-content-between">
                    <div className="w-25 bg-pulse-grey" style={{height: '35px'}} />
                </div>
            </div>
        </li>
    );
}

/**
 * Courses-view loading state — 2 shimmer course blocks.
 */
export function CoursesViewSkeleton() {
    return (
        <ul className="ps-0 list-group list-group-flush">
            <PlaceholderCourseItem />
            <PlaceholderCourseItem />
        </ul>
    );
}
