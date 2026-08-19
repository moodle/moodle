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
 * Jest tests for the ActivityIcon module-name and file-type resolution logic.
 *
 * @module     block_timeline/tests/ActivityIcon
 * @copyright  Meirza Arson <meirza.arson@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

import {render} from '@testing-library/react';
import {ActivityIcon} from '../src/views/ActivityIcon';

// Mock the design system component so tests assert on the resolved `icon` prop
// rather than depending on its real SVG-loading behaviour.
jest.mock('@moodlehq/design-system', () => ({
    ActivityIcon: (props: {icon: string; alt: string; container: string; size: string}) => (
        <span data-testid="ds-icon" data-icon={props.icon} data-container={props.container} data-size={props.size}>
            {props.alt}
        </span>
    ),
}), {virtual: true});

function iconOf(container: HTMLElement): string | null {
    return container.querySelector('[data-testid="ds-icon"]')?.getAttribute('data-icon') ?? null;
}

describe('ActivityIcon', () => {
    it('passes modulenames through unchanged when they already match a DS icon name', () => {
        const {container} = render(<ActivityIcon modulename="quiz" iconurl="/quiz.svg" />);
        expect(iconOf(container)).toBe('quiz');
    });

    it('maps assign to the DS "assignment" icon name', () => {
        const {container} = render(<ActivityIcon modulename="assign" iconurl="/assign.svg" />);
        expect(iconOf(container)).toBe('assignment');
    });

    it('maps data to the DS "database" icon name', () => {
        const {container} = render(<ActivityIcon modulename="data" iconurl="/data.svg" />);
        expect(iconOf(container)).toBe('database');
    });

    it('maps lti to the DS "external-tool" icon name', () => {
        const {container} = render(<ActivityIcon modulename="lti" iconurl="/lti.svg" />);
        expect(iconOf(container)).toBe('external-tool');
    });

    it('falls back to file-unknown when modulename is missing', () => {
        const {container} = render(<ActivityIcon modulename="" iconurl="/x.svg" />);
        expect(iconOf(container)).toBe('file-unknown');
    });

    it('falls back to file-unknown when modulename is the literal string "undefined"', () => {
        const {container} = render(<ActivityIcon modulename="undefined" iconurl="/x.svg" />);
        expect(iconOf(container)).toBe('file-unknown');
    });

    it('resolves a resource icon from the "f/<type>" segment of a path-form iconurl', () => {
        const {container} = render(
            <ActivityIcon modulename="resource" iconurl="https://x/theme/image.php/boost/core/1/f/pdf" />
        );
        expect(iconOf(container)).toBe('file-pdf');
    });

    it('resolves a resource icon from the "f%2F<type>" query form of iconurl', () => {
        const {container} = render(
            <ActivityIcon modulename="resource" iconurl="https://x/theme/image.php?image=f%2Fspreadsheet" />
        );
        expect(iconOf(container)).toBe('file-xls');
    });

    it('falls back to generic "file" for an unrecognised resource file type', () => {
        const {container} = render(
            <ActivityIcon modulename="resource" iconurl="https://x/theme/image.php/boost/core/1/f/mystery-type" />
        );
        expect(iconOf(container)).toBe('file');
    });

    it('falls back to generic "file" when the resource iconurl has no "f/<type>" segment', () => {
        const {container} = render(<ActivityIcon modulename="resource" iconurl="https://x/no-match-here" />);
        expect(iconOf(container)).toBe('file');
    });

    it('renders with container="none" and size="xl" so the caller controls presentation', () => {
        const {container} = render(<ActivityIcon modulename="quiz" iconurl="/quiz.svg" />);
        const icon = container.querySelector('[data-testid="ds-icon"]');
        expect(icon).toHaveAttribute('data-container', 'none');
        expect(icon).toHaveAttribute('data-size', 'xl');
    });

    it('passes alt text through to the design system icon', () => {
        const {getByText} = render(<ActivityIcon modulename="quiz" iconurl="/quiz.svg" alt="Quiz 1" />);
        expect(getByText('Quiz 1')).toBeInTheDocument();
    });
});
