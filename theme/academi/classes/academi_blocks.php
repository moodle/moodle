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
 * Extra additional blocks for the theme_academi.
 *
 * @package   theme_academi
 * @copyright 2023 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_academi;

/**
 * Extra additional blocks content class for the theme academi.
 */
class academi_blocks {

    /**
     * Return the html contents for the  Site features block.
     * @return type|string
     */
    public function sitefeatures() {
        global $OUTPUT, $PAGE;
        $status = theme_academi_get_setting('sitefblockstatus');
        $blocktitle = theme_academi_lang(theme_academi_get_setting('sitefeaturetitle'));
        $blockdesc = theme_academi_lang(theme_academi_get_setting('sitefeaturedesc'));
        $items = [];
        (int) $cs = 0;
        if ($status == 1) {
            $features = theme_academi_get_setting('numberofsitefeature');
            $block['class'] = 'icon-block';
            for ($i = 1; $i <= $features; $i++) {
                $sfbstatus = theme_academi_get_setting('sitefblock'.$i.'status');
                $sfbicon = theme_academi_get_setting('sitefblock'.$i.'icon');
                $sfbtitle = theme_academi_get_setting('sitefblock'.$i.'title');
                $sfbcontent = theme_academi_get_setting('sitefblock'.$i.'content');
                if ((!empty($sfbstatus)) && (!empty($sfbtitle) || !empty($sfbcontent) || !empty($sfbicon))) {
                    $cs = $cs + 1;
                }
            }
            switch ($cs) {
                case 4:
                    $colclass = 'col-md-6 col-lg-3';
                    break;
                case 3:
                    $colclass = 'col-md-6 col-lg-4';
                    break;
                case 2:
                    $colclass = 'col-md-6';
                    break;
                case 1:
                    $colclass = 'col-md-12';
                    break;
                default:
                    $colclass = 'col-md-6 col-lg-3';
                    break;
            }
            for ($i = 1; $i <= $features; $i++) {
                $sfbtitle = theme_academi_get_setting('sitefblock'.$i.'title');
                $sfbtitle = theme_academi_lang($sfbtitle);
                $sfbcontent = trim(theme_academi_get_setting('sitefblock'.$i.'content'));
                $sfbcontent = theme_academi_lang($sfbcontent);
                $sfbstatus = theme_academi_get_setting('sitefblock'.$i.'status');
                $sfbicon = theme_academi_get_setting('sitefblock'.$i.'icon');
                $sfbicon = theme_academi_lang($sfbicon);
                $sfbbody = (!empty($sfbtitle) || (!empty($sfbcontent)) || (!empty($sfbicon))) ? true : false;
                $sfurl = theme_academi_get_setting('sitefblock'.$i.'url');

                $items[] = [
                    'status' => !$sfbbody ? false : $sfbstatus,
                    'title' => $sfbtitle,
                    'content' => $sfbcontent,
                    'icon' => $sfbicon,
                    'sfbbody' => $sfbbody,
                    'url' => $sfurl,
                ];
            }
            $blockstatus = (empty($blocktitle) && empty($blockdesc)) ? false : $status;
            $blockisempty = (empty($blockstatus) && ($cs == 0)) ? false : $status;
            $block['sitefeatures'] = $status;
            $block['blockstatus'] = $blockstatus;
            $block['colclass'] = $colclass;
            $block['feature'] = $items;
            $block['blocktitle'] = $blocktitle;
            $block['blockdesc'] = $blockdesc;
            $block['blockisempty'] = $blockisempty;
            if (!$blockisempty) {
                $block['isblockempty'] = is_siteadmin() || $PAGE->user_is_editing() ? true : false;
            }
            return $OUTPUT->render_from_template('theme_academi/academi_blocks', $block);
        }
    }

    /**
     * Return the html contents for the marketingspot block.
     * @return type|string
     */
    public function marketingspot() {
        global $OUTPUT, $PAGE;
        $status = theme_academi_get_setting('mspotstatus');
        if ($status == 1) {
            $mspot['title'] = theme_academi_lang(theme_academi_get_setting('mspottitle'));
            $mspot['desc'] = theme_academi_lang(theme_academi_get_setting('mspotdesc'));
            $mspot['content'] = theme_academi_get_setting('mspotcontent', 'format_html');
            $mspot['media'] = theme_academi_get_setting('mspotmedia', 'file');
            $mspot['colclass'] = (empty($mspot['content']) || (empty($mspot['media']))) ? 'col-md-12' : 'col-lg-6';
            $mspot['mspot'] = $status;
            $mspot['mspotheadcontent'] = (empty($mspot['title']) && empty($mspot['desc'])) ? false : true;
            $blockisempty = empty($mspot['media']) && empty($mspot['content'])
                            && (empty($mspot['title'])) && (empty($mspot['desc'])) ? false : $status;
            $mspot['blockisempty'] = $blockisempty;
            if (!$blockisempty) {
                $mspot['isblockempty'] = is_siteadmin() || $PAGE->user_is_editing() ? true : false;
            }
            return $OUTPUT->render_from_template('theme_academi/academi_blocks', $mspot);
        }
    }

    /**
     * Return the html contents for the Jumbotron block.
     * @return type|string
     */
    public function jumbotron() {
        global $OUTPUT, $PAGE;
        $status = theme_academi_get_setting('jumbotronstatus');
        if ($status == 1 ) {
            $jumbotron['title'] = theme_academi_lang(theme_academi_get_setting('jumbotrontitle'));
            $jumbotron['desc'] = theme_academi_lang(theme_academi_get_setting('jumbotrondesc'));
            $jumbotron['btntext'] = theme_academi_lang(theme_academi_get_setting('jumbotronbtntext'));
            $jumbotron['buttonlink'] = theme_academi_get_setting('jumbotronbtnlink');
            $btntarget = theme_academi_get_setting('jumbotronbtntarget');
            $jumbotron['btntarget'] = ($btntarget == '1') ? '_blank' : '_self';
            $jumbotron['jumbotron'] = $status;
            $jumbotron['jumbotroncontent'] = empty($jumbotron['title']) && empty($jumbotron['desc']) ? false : true;
            $blockisempty = empty($jumbotron['title']) && empty($jumbotron['desc'])
                                && empty($jumbotron['btntext']) ? false : $status;
            $jumbotron['blockisempty'] = $blockisempty;
            if (!$blockisempty) {
                $jumbotron['isblockempty'] = is_siteadmin() || $PAGE->user_is_editing() ? true : false;
            }
            return $OUTPUT->render_from_template('theme_academi/academi_blocks', $jumbotron);
        }
    }
}
