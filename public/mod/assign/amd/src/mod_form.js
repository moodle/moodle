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
 * Event handler to update derived form fields.
 *
 * @module     mod_assign/mod_form
 * @copyright  2026 Catalyst IT Australia Pty Ltd
 * @author     Benjamin Walker <benjaminwalker@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const Selectors = {
    form: 'form[action*="modedit.php"]',
    fields: {
        markerCount: '#id_markercount',
        optionalMarkerCount: '#id_optionalmarkercount',
    },
    derived: {
        totalMarkerCount: 'input[name="totalmarkercount"]',
    },
};

/**
 * Update derived hidden fields in real time.
 */
export const init = () => {
    const form = document.querySelector(Selectors.form);
    if (!form) {
        return;
    }

    const updateDerivedFields = () => {
        const totalMarkerCount = form.querySelector(Selectors.derived.totalMarkerCount);
        if (totalMarkerCount) {
            const markerCount = Number(form.querySelector(Selectors.fields.markerCount)?.value ?? 1);
            const optionalMarkerCount = Number(form.querySelector(Selectors.fields.optionalMarkerCount)?.value ?? 0);

            totalMarkerCount.value = markerCount + optionalMarkerCount;
            totalMarkerCount.dispatchEvent(new Event('change', {bubbles: true}));
        }
    };

    // Run once on load.
    updateDerivedFields();

    // Run on any relevant change.
    Object.values(Selectors.fields).forEach(selector => {
        form.querySelector(selector)?.addEventListener('change', updateDerivedFields);
    });
};
