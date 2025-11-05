<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace mod_board\local;

/**
 * Modal form helper trait.
 *
 * @package    mod_board
 * @copyright  2025 Petr Skoda
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait ajax_form_trait {
    /**
     * The constructor function calls the abstract function definition() and it will then
     * process and clean and attempt to validate incoming data.
     *
     * It will call your custom validate method to validate data and will also check any rules
     * you have specified in definition using addRule
     *
     * The name of the form (id attribute of the form) is automatically generated depending on
     * the name you gave the class extending moodleform. You should call your class something
     * like
     *
     * @param mixed $action the action attribute for the form. If empty defaults to auto detect the
     *              current url. If a moodle_url object then outputs params as hidden variables.
     * @param mixed $customdata if your form defintion method needs access to data such as $course
     *              $cm, etc. to construct the form definition then pass it in this array. You can
     *              use globals for somethings.
     * @param string $method if you set this to anything other than 'post' then _GET and _POST will
     *               be merged and used as incoming data to the form.
     * @param string $target target frame for form submission. You will rarely use this. Don't use
     *               it if you don't need to as the target attribute is deprecated in xhtml strict.
     * @param mixed $attributes you can pass a string of html attributes here or an array.
     *               Special attribute 'data-random-ids' will randomise generated elements ids. This
     *               is necessary when there are several forms on the same page.
     *               Special attribute 'data-double-submit-protection' set to 'off' will turn off
     *               double-submit protection JavaScript - this may be necessary if your form sends
     *               downloadable files in response to a submit button, and can't call
     *               \core_form\util::form_download_complete();
     * @param bool $editable
     * @param array $ajaxformdata Forms submitted via ajax, must pass their data here, instead of relying on _GET and _POST.
     */
    public function __construct(
        $action = null,
        $customdata = null,
        $method = 'post',
        $target = '',
        $attributes = null,
        $editable = true,
        $ajaxformdata = null
    ) {
        if (self::is_ajax_request()) {
            // Note that there might be multiple chained forms in one ajax script.
            if (!defined('MOD_BOARD_AJAX_FORM_INITIALISED')) {
                global $OUTPUT;
                echo $OUTPUT->header();
                self::start_output_collection();
                define('MOD_BOARD_AJAX_FORM_INITIALISED', true);
            }
            // There may be a form on the current page already, so use random html element ids.
            $attributes = (array)$attributes;
            $attributes['data-random-ids'] = 1;
        }
        parent::__construct($action, $customdata, $method, $target, $attributes, $editable, $ajaxformdata);
    }

    /**
     * Is the form accessed from an ajax script?
     * @return bool
     */
    final public static function is_ajax_request(): bool {
        return defined('AJAX_SCRIPT') && AJAX_SCRIPT;
    }

    /**
     * Start collecting form javascript and html.
     *
     * @return void
     */
    private static function start_output_collection(): void {
        global $PAGE;

        // This dark magic matches the fragments API hackery in Moodle core.

        define('PREFERRED_RENDERER_TARGET', RENDERER_TARGET_GENERAL);
        $PAGE->start_collecting_javascript_requirements();
        ob_start();
    }

    /**
     * End collecting form javascript and html.
     *
     * @return array
     */
    private static function stop_output_collection(): array {
        global $PAGE;

        if (!defined('MOD_BOARD_AJAX_FORM_INITIALISED')) {
            throw new \core\exception\coding_exception('modal form is not collecting output');
        }

        $html = ob_get_contents();
        ob_end_clean();

        $result = [
            'html' => $html,
            'javascript' => $PAGE->requires->get_end_code(),
        ];

        return $result;
    }

    /**
     * Called after form cancelled.
     *
     * NOTE: this should not happen in modal forms.
     *
     * @param \moodle_url $redirecturl
     * @return never
     */
    final public static function ajax_form_cancelled(\moodle_url $redirecturl): never {
        $data = [
            'status' => 'cancelled',
            'redirecturl' => $redirecturl->out(false),
        ];
        echo json_encode(['data' => $data]);
        die;
    }

    /**
     * Called after form is submitted.
     *
     * @param \moodle_url $redirecturl
     * @param array $callbackdata data passed to JS submission callback from formSubmittedAction
     * @return never
     */
    final public static function ajax_form_submitted(\moodle_url $redirecturl, array $callbackdata = []): never {
        $data = [
            'status' => 'submitted',
            'redirecturl' => $redirecturl->out(false),
            'callbackdata' => $callbackdata,
        ];
        echo json_encode(['data' => $data]);
        die;
    }

    /**
     * Called when form is to be rendered.
     *
     * @param string $dialogtitle title of form dialog
     * @param string|null $submittext submit button text
     * @param string|null $canceltext cancel button text
     * @param string|null $submitarialabel
     * @param string|null $cancelarialabel
     * @return never
     */
    final public static function ajax_form_render(
        string $dialogtitle,
        ?string $submittext = null,
        ?string $canceltext = null,
        ?string $submitarialabel = null,
        ?string $cancelarialabel = null
    ): never {
        ['html' => $html, 'javascript' => $javascript] = self::stop_output_collection();
        $data = [
            'status' => 'render',
            'html' => $html,
            'javascript' => $javascript,
            'dialogtitle' => $dialogtitle,
            'submittext' => $submittext ?? get_string('submit'),
            'canceltext' => $canceltext ?? get_string('cancel'),
            'submitarialabel' => $submitarialabel ?? '',
            'cancelarialabel' => $cancelarialabel ?? '',
        ];
        echo json_encode(['data' => $data]);
        die;
    }
}
