<?php

/** Allow switching designs
* @link https://www.adminer.org/plugins/#use
* @author Jakub Vrana, https://www.vrana.cz/
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
*/
class AdminerMdlDesigns {

    /**
    * This function is responsible for returning an array of CSS URLs to be included in the Adminer interface.
    * It allows to add additional custom CSS styles to enhance the user experience.
    *
    * @return array An array of CSS URLs to be included in the Adminer interface.
    */
   function css() {
        global $PAGE, $CFG;
        $PAGE->set_context(\context_system::instance());
        $PAGE->set_url(new \moodle_url('/local/adminer/lib/run_adminer.php'));
        $cssurls = $PAGE->theme->css_urls($PAGE);
        $cssurls[] = $CFG->wwwroot . '/local/adminer/lib/plugins/additional.css';
        return $cssurls;
    }

    /**
     * This function is responsible for rendering a navigation link back to the Moodle home page.
     *
     * @return void This function does not return any value. It directly outputs the navigation link.
     */
    function navigation() {
        global $CFG, $OUTPUT;

        $title = get_string('backtohome');
        $url = new moodle_url('/');

        echo $OUTPUT->render_from_template('local_adminer/back_to_moodle', ['title' => $title, 'url' => $url]);
    }
}
