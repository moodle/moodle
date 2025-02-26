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
 * The helper module or AI Subsystem.
 *
 * @module     core_ai/helper
 * @copyright  2024 Huong Nguyen <huongnv13@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
export default class AIHelper {
    /**
     * Replace double line breaks with <br> and with </p><p> for paragraphs.
     * This is to handle the difference in response from the AI to what is expected by the editor.
     *
     * @param {String} text The text to replace.
     * @returns {String}
     */
    static replaceLineBreaks(text) {
        // Replace double line breaks with </p><p> for paragraphs
        const textWithParagraphs = text.replace(/\n{2,}|\r\n/g, '<br/><br/>');

        // Replace remaining single line breaks with <br> tags
        const textWithBreaks = textWithParagraphs.replace(/\n/g, '<br/>');

        // Add opening and closing <p> tags to wrap the entire content
        return `<p>${textWithBreaks}</p>`;
    }

    /**
     * Replace markdown formatting.
     * Even when asked not to, AI models will sometimes return markdown.
     *
     * @param {String} text The text to replace.
     * @returns {String}
     */
    static replaceMarkdown(text) {
        // Replace markdown bold formatting HTML equivalent.
        const textWithMarkdown = text.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        return textWithMarkdown;
    }

    /**
     * Format the response provided by the AI model.
     *
     * @param {String} text The text to format.
     * @returns {String}
     */
    static formatResponse(text) {
        let formattedText = this.replaceLineBreaks(text) ;
        formattedText = this.replaceMarkdown(formattedText);

        return formattedText;
    }
}
