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

/**
 * Theme helper to load a theme configuration.
 *
 * @package    theme_moove
 * @copyright  2022 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_moove\util;

use theme_config;

/**
 * Helper to load a theme configuration.
 *
 * @package    theme_moove
 * @copyright  2017 Willian Mano - http://conecti.me
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class settings {
    /**
     * @var \stdClass $theme The theme object.
     */
    protected $theme;
    /**
     * @var array $files Theme file settings.
     */
    protected $files = [
        'logo', 'logodark', 'loginbg',
        'sliderimage1', 'sliderimage2', 'sliderimage3', 'sliderimage4', 'sliderimage5', 'sliderimage6',
        'sliderimage7', 'sliderimage8', 'sliderimage9', 'sliderimage10', 'sliderimage11', 'sliderimage12',
        'marketing1icon', 'marketing2icon', 'marketing3icon', 'marketing4icon',
    ];

    /**
     * Class constructor
     */
    public function __construct() {
        $this->theme = theme_config::load('moove');
    }

    /**
     * Magic method to get theme settings
     *
     * @param string $name
     *
     * @return false|string|null
     */
    public function __get(string $name) {
        if (in_array($name, $this->files)) {
            return $this->theme->setting_file_url($name, $name);
        }

        if (empty($this->theme->settings->$name)) {
            return false;
        }

        return $this->theme->settings->$name;
    }

    /**
     * Get footer settings
     *
     * @return array
     */
    public function footer() {
        global $CFG;

        $templatecontext = [];

        $settings = [
            'facebook', 'twitter', 'linkedin', 'youtube', 'instagram', 'whatsapp', 'telegram', 'tiktok', 'pinterest',
            'website', 'mobile', 'mail',
        ];

        $templatecontext['hasfootercontact'] = false;
        $templatecontext['hasfootersocial'] = false;
        foreach ($settings as $setting) {
            $templatecontext[$setting] = $this->$setting;

            if (in_array($setting, ['website', 'mobile', 'mail']) && !empty($templatecontext[$setting])) {
                $templatecontext['hasfootercontact'] = true;
            }

            $socialsettings = [
                'facebook', 'twitter', 'linkedin', 'youtube', 'instagram', 'whatsapp', 'telegram', 'tiktok', 'pinterest',
            ];

            if (in_array($setting, $socialsettings) && !empty($templatecontext[$setting])) {
                $templatecontext['hasfootersocial'] = true;
            }
        }

        $templatecontext['enablemobilewebservice'] = $CFG->enablemobilewebservice;

        if ($CFG->enablemobilewebservice) {
            $iosappid = get_config('tool_mobile', 'iosappid');
            if (!empty($iosappid)) {
                $templatecontext['iosappid'] = $iosappid;
            }

            $androidappid = get_config('tool_mobile', 'androidappid');
            if (!empty($androidappid)) {
                $templatecontext['androidappid'] = $androidappid;
            }

            $setuplink = get_config('tool_mobile', 'setuplink');
            if (!empty($setuplink)) {
                $templatecontext['mobilesetuplink'] = $setuplink;
            }
        }

        return $templatecontext;
    }

    /**
     * Get frontpage settings
     *
     * @return array
     */
    public function frontpage() {
        return array_merge(
            $this->frontpage_slideshow(),
            $this->frontpage_marketingboxes(),
            $this->frontpage_numbers(),
            $this->faq()
        );
    }

    /**
     * Get config theme slideshow
     *
     * @return array
     */
    public function frontpage_slideshow() {
        $templatecontext['slidercount'] = $this->slidercount;

        // Corporate default slide images for each position.
        $defaultimages = [
            1 => new \moodle_url('/theme/moove/pix/slide_corporate_1.svg'),
            2 => new \moodle_url('/theme/moove/pix/slide_corporate_2.svg'),
            3 => new \moodle_url('/theme/moove/pix/slide_corporate_3.svg'),
        ];
        $fallbackimage = new \moodle_url('/theme/moove/pix/default_slide.jpg');

        for ($i = 1, $j = 0; $i <= $templatecontext['slidercount']; $i++, $j++) {
            $sliderimage = "sliderimage{$i}";
            $slidertitle = "slidertitle{$i}";
            $slidercap = "slidercap{$i}";
            $slidercapcontent = $this->$slidercap ?: null;

            $slidetitle = format_string($this->$slidertitle) ?: null;
            $slidecontent = format_text($slidercapcontent, FORMAT_MOODLE, ['noclean' => false]) ?: null;
            $image = $this->$sliderimage;

            // Use corporate default image for this slide position, or fallback.
            $defaultimage = isset($defaultimages[$i]) ? $defaultimages[$i] : $fallbackimage;

            $hascaption = isset($slidetitle) || isset($slidecontent);

            $templatecontext['slides'][$j]['key'] = $j;
            $templatecontext['slides'][$j]['active'] = $i === 1;
            $templatecontext['slides'][$j]['image'] = $image ?: $defaultimage->out();
            $templatecontext['slides'][$j]['title'] = $slidetitle;
            $templatecontext['slides'][$j]['caption'] = $slidecontent;
            $templatecontext['slides'][$j]['hascaption'] = $hascaption;
        }

        $templatecontext['slidersingleslide'] = $this->slidercount == 1;

        return $templatecontext;
    }

    /**
     * Get config theme marketing boxes
     *
     * @return array
     */
    public function frontpage_marketingboxes() {
        if ($templatecontext['displaymarketingbox'] = $this->displaymarketingbox) {
            $templatecontext['marketingheading'] = format_text($this->marketingheading, FORMAT_HTML);
            $templatecontext['marketingcontent'] = format_text($this->marketingcontent, FORMAT_HTML);

            // Corporate default icons for each marketing box position.
            $defaulticons = [
                1 => new \moodle_url('/theme/moove/pix/icon_compliance.svg'),
                2 => new \moodle_url('/theme/moove/pix/icon_leadership.svg'),
                3 => new \moodle_url('/theme/moove/pix/icon_technical.svg'),
                4 => new \moodle_url('/theme/moove/pix/icon_cybersecurity.svg'),
            ];
            $fallbackicon = new \moodle_url('/theme/moove/pix/default_markegingicon.svg');

            // Corporate default content for each marketing box.
            $defaultheadings = [
                1 => 'Compliance Training',
                2 => 'Leadership Development',
                3 => 'Technical Skills',
                4 => 'Cybersecurity Awareness',
            ];
            $defaultcontents = [
                1 => 'Stay compliant with mandatory training on ethics, anti-harassment, and data privacy.',
                2 => 'Build leadership skills with courses on coaching, strategy, and change management.',
                3 => 'Master project management, agile methodologies, and data analysis fundamentals.',
                4 => 'Protect company data with training on phishing, passwords, and security best practices.',
            ];

            for ($i = 1, $j = 0; $i < 5; $i++, $j++) {
                $marketingicon = 'marketing' . $i . 'icon';
                $marketingheading = 'marketing' . $i . 'heading';
                $marketingcontent = 'marketing' . $i . 'content';

                // Use corporate default icon for this position, or fallback.
                $defaulticon = isset($defaulticons[$i]) ? $defaulticons[$i] : $fallbackicon;

                $templatecontext['marketingboxes'][$j]['icon'] = $this->$marketingicon ?: $defaulticon->out();
                $templatecontext['marketingboxes'][$j]['heading'] = $this->$marketingheading ?
                    format_text($this->$marketingheading, FORMAT_HTML) : $defaultheadings[$i];
                $templatecontext['marketingboxes'][$j]['content'] = $this->$marketingcontent ?
                    format_text($this->$marketingcontent, FORMAT_HTML) : $defaultcontents[$i];
            }
        }

        return $templatecontext;
    }

    /**
     * Get config theme slideshow
     *
     * @return array
     */
    public function frontpage_numbers() {
        global $DB;

        if ($templatecontext['numbersfrontpage'] = $this->numbersfrontpage) {
            $templatecontext['numberscontent'] = $this->numbersfrontpagecontent ? format_text($this->numbersfrontpagecontent) : '';
            $templatecontext['numbersusers'] = $DB->count_records('user', ['deleted' => 0, 'suspended' => 0]) - 1;
            $templatecontext['numberscourses'] = $DB->count_records('course', ['visible' => 1]) - 1;
        }

        return $templatecontext;
    }

    /**
     * Get config theme slideshow
     *
     * @return array
     */
    public function faq() {
        $templatecontext['faqenabled'] = false;

        if ($this->faqcount) {
            for ($i = 1; $i <= $this->faqcount; $i++) {
                $faqquestion = 'faqquestion' . $i;
                $faqanswer = 'faqanswer' . $i;

                if (!$this->$faqquestion || !$this->$faqanswer) {
                    continue;
                }

                $templatecontext['faq'][] = [
                    'id' => $i,
                    'question' => format_text($this->$faqquestion),
                    'answer' => format_text($this->$faqanswer),
                    'active' => $i === 1,
                ];
            }

            if (!empty($templatecontext['faq'])) {
                $templatecontext['faqenabled'] = true;
            }
        }

        return $templatecontext;
    }
}
