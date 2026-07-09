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
 * Search input for the Timeline block.
 *
 * Matches the DOM structure of core/search_input_auto (used by the legacy nav-search.mustache).
 *
 * @module     block_timeline/nav/Search
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {useRef, useState, useCallback, useEffect, useId} from 'react';
import {getString} from '@moodle/lms/core/stringUtils';

const DEBOUNCE_MS = 1000;

interface SearchProps {
    onSearch: (value: string) => void;
    /** Called with true immediately on keystroke, false when the debounce resolves. */
    onSearching?: (pending: boolean) => void;
}

/**
 * Debounced search input matching the core/search_input_auto template structure.
 *
 * Label is visually-hidden (not aria-label on the input); the clear button is rendered
 * reactively rather than toggled via d-none.
 */
export default function Search({onSearch, onSearching}: SearchProps) {
    const uid = useId().replace(/:/g, '');
    const inputId = `searchinput-${uid}`;
    const labelId = `searchinput-label-${uid}`;
    const formId = `searchform-auto-${uid}`;

    const [value, setValue] = useState('');
    const [label, setLabel] = useState('');
    const [clearLabel, setClearLabel] = useState('');
    const timerRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    useEffect(() => {
        getString('searchevents', 'block_timeline').then(setLabel);
        getString('clearsearch', 'core').then(setClearLabel);
    }, []);

    /**
     * Debounces the search input so onSearch fires DEBOUNCE_MS after the user stops typing.
     *
     * @param e change event from the search input field.
     */
    const handleChange = useCallback((e: React.ChangeEvent<HTMLInputElement>) => {
        const next = e.target.value;
        setValue(next);
        onSearching?.(true);
        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }
        timerRef.current = setTimeout(() => {
            onSearching?.(false);
            onSearch(next);
        }, DEBOUNCE_MS);
    }, [onSearch, onSearching]);

    const handleClear = useCallback(() => {
        setValue('');
        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }
        onSearching?.(false);
        onSearch('');
    }, [onSearch, onSearching]);

    return (
        <div className="w-100">
            <div id={formId} className="d-flex flex-wrap align-items-center simplesearchform">
                <div className="input-group searchbar w-100" role="search" aria-labelledby={labelId}>
                    <label htmlFor={inputId} id={labelId}>
                        <span className="visually-hidden">{label}</span>
                    </label>
                    <input
                        type="text"
                        data-region="input"
                        data-action="search"
                        id={inputId}
                        className="form-control withclear rounded"
                        placeholder={label}
                        name="search"
                        value={value}
                        autoComplete="off"
                        onChange={handleChange}
                    />
                    {value && (
                        <button
                            className="btn btn-clear"
                            data-action="clearsearch"
                            type="button"
                            onClick={handleClear}
                        >
                            <i className="icon fa fa-xmark fa-fw" aria-hidden="true"></i>
                            <span className="visually-hidden">{clearLabel}</span>
                        </button>
                    )}
                </div>
            </div>
        </div>
    );
}
