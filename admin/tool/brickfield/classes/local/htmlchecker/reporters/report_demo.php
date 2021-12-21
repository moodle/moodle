<?php
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

namespace tool_brickfield\local\htmlchecker\reporters;

use tool_brickfield\local\htmlchecker\brickfield_accessibility;
use tool_brickfield\local\htmlchecker\brickfield_accessibility_reporter;

/**
 * Returns the entire document marked-up to highlight problems.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class report_demo extends brickfield_accessibility_reporter {

    /**
     * @var array An array of the classnames to be associated with items
     */
    public $classnames = [
        brickfield_accessibility::BA_TEST_SEVERE => 'testlevel_severe',
        brickfield_accessibility::BA_TEST_MODERATE => 'testlevel_moderate',
        brickfield_accessibility::BA_TEST_SUGGESTION => 'testlevel_suggestion',
    ];

    /**
     * The get_report method - we iterate through every test item and
     * add additional attributes to build the report UI.
     * @return string A fully-formed HTML document.
     */
    public function get_report(): string {
        $problems = $this->guideline->get_report();
        if (is_array($problems)) {
            foreach ($problems as $testname => $test) {
                if (!isset($this->options->display_level) ||
                    ($this->options->display_level >= $test['severity'] && is_array($test))) {
                    foreach ($test as $problem) {
                        if (is_object($problem) && property_exists($problem, 'element') && is_object($problem->element)) {
                            $existing = $problem->element->getAttribute('style');
                            $problem->element->setAttribute('style',
                                $existing .'; border: 2px solid red;');
                            if (isset($this->options->image_url)) {
                                $image = $this->dom->createElement('img');
                                $image = $problem->element->parentNode->insertBefore($image, $problem->element);
                                $image->setAttribute('alt', $testname);
                                if ($problem->message) {
                                    $image->setAttribute('title', $problem->message);
                                }
                                $image->setAttribute('src', $this->options->image_url[$test['severity']]);
                            }
                        }
                    }
                }
            }
        }
        return $this->complete_urls($this->dom->saveHTML(), implode('/', $this->path));
    }


    /**
     * Finds the final postion of a needle in the haystack.
     *
     * @param mixed $haystack
     * @param mixed $needle
     * @param mixed $occurance
     * @param int $pos
     * @return false|int
     */
    public function strnpos($haystack, $needle, $occurance, int $pos = 0) {
        for ($i = 1; $i <= $occurance; $i++) {
            $pos = strpos($haystack, $needle, $pos) + 1;
        }
        return $pos - 1;
    }

    /**
     * A helper function for completeURLs. Parses a URL into an the scheme, host, and path
     * @param string $url The URL to parse
     * @return array An array that includes the scheme, host, and path of the URL
     */
    public function parse_url(string $url): array {
        $pattern = "/^(?:(http[s]?):\/\/(?:(.*):(.*)@)?([^\/]+))?((?:[\/])?(?:[^\.]*?)?(?:[\/])?)?(?:([^\/^\.]+)\." .
            "([^\?]+))?(?:\?(.+))?$/i";
        preg_match($pattern, $url, $matches);

        $uriparts["scheme"] = $matches[1];
        $uriparts["host"] = $matches[4];
        $uriparts["path"] = $matches[5];

        return $uriparts;
    }

    /**
     * Turns all relative links to absolute links so that the page can be rendered correctly.
     * @param string $html A complete HTML document
     * @param string $url The absolute URL to the document
     * @return string A HTML document with all the relative links converted to absolute links
     */
    public function complete_urls(string $html, string $url) {
        $uriparts = $this->parse_url($url);
        $path = trim($uriparts["path"], "/");
        $hosturl = trim($uriparts["host"], "/");

        $host = $uriparts["scheme"]."://".$hosturl."/".$path."/";
        $hostnopath = $uriparts["scheme"]."://".$hosturl."/";

        // Proxifies local META redirects.
        $html = preg_replace('@<META HTTP-EQUIV(.*)URL=/@',
            "<META HTTP-EQUIV\$1URL=".$_SERVER['PHP_SELF']."?url=".$hostnopath, $html);

        // Make sure the host doesn't end in '//'.
        $host = rtrim($host, '/')."/";

        // Replace '//' with 'http://'.
        $pattern = "#(?<=\"|'|=)\/\/#"; // The '|=' is experimental as it's probably not necessary.
        $html = preg_replace($pattern, "http://", $html);

        // Fully qualifies '"/'.
        $html = preg_replace("#\"\/#", "\"".$host, $html);

        // Fully qualifies "'/".
        $html = preg_replace("#\'\/#", "\'".$host, $html);

        // Matches [src|href|background|action]="/ because in the following pattern the '/' shouldn't stay.
        $html = preg_replace("#(src|href|background|action)(=\"|='|=(?!'|\"))\/#i", "\$1\$2".$hostnopath, $html);
        $html = preg_replace("#(href|src|background|action)(=\"|=(?!'|\")|=')(?!http|ftp|https|\"|'|javascript:|mailto:)#i",
            "\$1\$2".$host, $html);

        // Points all form actions back to the proxy.
        $html = preg_replace('/<form.+?action=\s*(["\']?)([^>\s"\']+)\\1[^>]*>/i',
            "<form action=\"{$_SERVER['PHP_SELF']}\"><input type=\"hidden\" name=\"original_url\" value=\"$2\">", $html);

        // Matches '/[any assortment of chars or nums]/../'.
        $html = preg_replace("#\/(\w*?)\/\.\.\/(.*?)>#ims", "/\$2>", $html);

        // Matches '/./'.
        $html = preg_replace("#\/\.\/(.*?)>#ims", "/\$1>", $html);

        // Handles CSS2 imports.
        if (strpos($html, "import url(\"http") == false && (strpos($html, "import \"http") == false)
            && strpos($html, "import url(\"www") == false && (strpos($html, "import \"www") == false)) {
            $pattern = "#import .(.*?).;#ims";
            $mainurl = substr($host, 0, $this->strnpos($host, "/", 3));
            $replace = "import '".$mainurl."\$1';";
            $html = preg_replace($pattern, $replace, $html);
        }
        return $html;
    }
}
