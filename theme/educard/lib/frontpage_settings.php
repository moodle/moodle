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
 * Educard theme general settings.
 *
 * @package   theme_educard
 * @copyright 2022 ThemesAlmond  - http://themesalmond.com
 * @author    ThemesAlmond - Developer Team
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Front page section.
 *
 * @param int $homeid home page id.
 */
function theme_educard_frontpage_section($homeid) {
    $theme = theme_config::load('educard');
    // Announcements.
    //$templatecontext['announcements'] = true;
    $templatecontext['announcementsbar'] = $theme->settings->announcementsbar;
    if ($theme->settings->announcementsbar) {
        $templatecontext = array_merge($templatecontext, theme_educard_announcements());
    }
    // Pages setting.
    if ($homeid == "p1") {
        $templatecontext['pages'] = true;
        $templatecontext['frontpagepage1'] = true;
        $templatecontext = array_merge($templatecontext, theme_educard_frontpage_all());
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagepage01());
        if ($templatecontext['block20enabled']) {
            $templatecontext = array_merge($templatecontext, theme_educard_frontpageblock20(0));
        }
        return $templatecontext;
    }
    if ($homeid == "p2") {
        $templatecontext['pages'] = true;
        $templatecontext['frontpagepage2'] = true;
        $templatecontext = array_merge($templatecontext, theme_educard_frontpage_all());
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagepage02());
        $templatecontext['block20enabled'] = $theme->settings->block20enabled;
        if (!empty($theme->settings->block20enabled)) {
            $templatecontext = array_merge($templatecontext, theme_educard_frontpageblock20(0));
        }
        return $templatecontext;
    }
    // If there is a front page number, create that page.
    $frontpagechoice = $theme->settings->frontpagechoice;
    if ( $homeid > 0 && $homeid < 9 ) {
        $frontpagechoice = $homeid;
    }
    // Front page sections.
    $templatecontext['frontpage'.$frontpagechoice] = $frontpagechoice;
    if ($frontpagechoice) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpage_ready($frontpagechoice));
        return $templatecontext;
    } else {
        // Create customized front page.
        $templatecontext['frontpage0'] = true;
        $templatecontext = array_merge($templatecontext, theme_educard_frontpage_all());
        $j = 0;
        // Frontpage .
        for ($i = 1; $i <= 10; $i++) {
            $yap = "frontpagesection1_"."$i";
            $slide = "slidesection1_"."$i";
            $block = substr($theme->settings->$yap, 0, 2);
            $desing = substr($theme->settings->$yap, 3, 1);
            $blockenabled = "block".$block."enabled";
            if (!empty($theme->settings->$blockenabled)) {
                $function = "theme_educard_frontpageblock".$block;
                $templatecontext['say10'][$j]['blockno'.$block] = "blockno".$block;
                $templatecontext['say10'][$j]['desing'.$block.'-'.$desing] = "desing".$block."-".$desing;
                $templatecontext['say10'][$j]['slide'.$block.'-'.$desing] = $theme->settings->$slide;
                $templatecontext = array_merge($templatecontext, $function($desing));
                $j++;
            }
        }
        // Banner and slider.
        $templatecontext = array_merge($templatecontext, theme_educard_frontpage_banner_and_slider());
        // Footer.
        $templatecontext['block20enabled'] = $theme->settings->block20enabled;
        if (!empty($theme->settings->block20enabled)) {
            $templatecontext = array_merge($templatecontext, theme_educard_frontpageblock20(0));
        }
        return $templatecontext;
    }
}

/**
 * Front page ready pages.
 *
 * @param int $frontpagechoice front page id.
 */
function theme_educard_frontpage_ready($frontpagechoice) {
    $theme = theme_config::load('educard');
    $templatecontext['frontpage'] = true;
    // General default.
    $templatecontext = array_merge($templatecontext, theme_educard_frontpage_all());
    // Banner and slider.
    if ($frontpagechoice == 1) {
        // Slider.
        $templatecontext['sliderenabled'] = true;
        $templatecontext = array_merge($templatecontext, theme_educard_slideshow(1));
        $blockid = "16-1,09-1,03-1,07-1,08-1,06-1,20-1";
    } else if ($frontpagechoice == 2) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagebanner01());
        $templatecontext['bannerheadingenabled'] = true;
        $blockid = "12-2,07-2,06-2,10-2,11-2,20-1";
    } else if ($frontpagechoice == 3) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagebanner03());
        $templatecontext['bannerheadingenabled'] = true;
        $blockid = "03-1,09-1,02-1,07-3,06-3,20-1";
    } else if ($frontpagechoice == 4) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagebanner04());
        $templatecontext['bannerheadingenabled'] = true;
        $blockid = "04-1,02-2,08-1,09-1,07-1,20-1";
    } else if ($frontpagechoice == 5) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagebanner02());
        $templatecontext['bannerheadingenabled'] = true;
        $blockid = "04-2,09-2,07-3,11-2,03-2,01-3,20-1";
    } else if ($frontpagechoice == 6) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagebanner06());
        $templatecontext['bannerheadingenabled'] = true;
        $blockid = "02-3,13-1,20-1";
    } else if ($frontpagechoice == 7) {
        // Slider.
        $templatecontext['sliderenabled'] = true;
        $templatecontext = array_merge($templatecontext, theme_educard_slideshow(2));
        $blockid = "01-2,09-4,03-2,07-3,02-1,08-2,06-3,19-1,20-1";
    } else if ($frontpagechoice == 8) {
        $templatecontext = array_merge($templatecontext, theme_educard_frontpagebanner05());
        $templatecontext['bannerheadingenabled'] = true;
        $blockid = "12-1,03-1,09-3,07-1,06-1,08-1,19-1,201-";
    }
    // Section block.
    $blocks = explode(",", $blockid);
    foreach ($blocks as $block) {
        $blockid = substr($block, 0, 2);
        $dsn = substr($block, 3);
        $blockenabled = "block".$blockid."enabled";
        if (!empty($theme->settings->$blockenabled)) {
            $function = "theme_educard_frontpageblock".$blockid;
            $templatecontext['block'.$blockid.'enabled'] = true;
            $templatecontext = array_merge($templatecontext, $function($dsn));
        }
    }
    return $templatecontext;
}

/**
 * Front page all default.
 */
function theme_educard_frontpage_all() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    // Front page navbar ?
    $templatecontext['frontpagenavchoice'.$theme->settings->frontpagenavchoice] = $theme->settings->frontpagenavchoice;
    // Navbar logo control.
    $templatecontext['logovar'] = false;
    $templatecontext['compactlogovar'] = false;
    if ($OUTPUT->get_compact_logo_url()) {
        $templatecontext['compactlogovar'] = true;
    }
    if ($OUTPUT->get_logo_url()) {
        $templatecontext['logovar'] = true;
    }
    // Front page navbar logo select.
    switch ($theme->settings->headerlogo) {
        case 'Logo':
            $templatecontext['headerlogo'] = true;
            break;
        case 'Compact logo':
            $templatecontext['headerlogocompact'] = true;
            break;
        case 'None':
            $templatecontext['nonelogo'] = true;
            break;
    }
    $templatecontext['colorpalette'] = $theme->settings->colorpalette;
    if (!empty($theme->settings->frontpagenavlink)) {
        $templatecontext['frontpagenavlink'] = theme_educard_header_links($theme->settings->frontpagenavlink, false);
        $templatecontext['frontpagenavlink-m'] = theme_educard_links_mobil($theme->settings->frontpagenavlink);
    } else {
        $templatecontext['frontpagenavlink'] = "";
    }
    // Front page color.
    $templatecontext = array_merge($templatecontext, theme_educard_frontpagecolor());
    return $templatecontext;
}
/**
 * Front page all default.
 */
function theme_educard_frontpage_banner_and_slider() {
    $theme = theme_config::load('educard');
    // Banners.
    $templatecontext['bannerheadingenabled'] = $theme->settings->bannerheadingenabled;
    $templatecontext['banner'.$theme->settings->bannerheadingchoice] = $theme->settings->bannerheadingchoice;
    if (!empty($theme->settings->bannerheadingenabled)) {
        $functionname = 'theme_educard_frontpagebanner0'.$theme->settings->bannerheadingchoice;
        $templatecontext = array_merge($templatecontext, $functionname());
    }
    // Slider.
    $templatecontext['sliderenabled'] = $theme->settings->sliderenabled;
    if (!empty($theme->settings->sliderenabled)) {
        $templatecontext = array_merge($templatecontext, theme_educard_slideshow(0));
    }
    return $templatecontext;
}

/**
 * Front page color.
 */
function theme_educard_frontpagecolor() {
    $theme = theme_config::load('educard');
    $templatecontext['colorsetup'] = $theme->settings->frontpagecolor;
    if (empty($theme->settings->sitecolor)) {
        $templatecontext['sitecolor'] = $theme->settings->frontpagecolor;
    } else {
        $templatecontext['sitecolor'] = $theme->settings->sitecolor;
    }
    if (empty($theme->settings->sitecolor2)) {
        $templatecontext['sitecolor2'] = $templatecontext['sitecolor'];
    } else {
        $templatecontext['sitecolor2'] = $theme->settings->sitecolor2;
    }
    if (empty($theme->settings->navbarcolor)) {
        $templatecontext['navbarcolor'] = "#ffffff";
        $templatecontext['navbardarkcolor'] = "#555555";
    } else {
        $templatecontext['navbarcolor'] = $theme->settings->navbarcolor;
    }
    return $templatecontext;
}
/**
 * Front page course img.
 *
 * @param int $id course id.
 * @param bool $ctrl switch.
 */
function educard_get_course_image($id, $ctrl) {
    global $OUTPUT, $CFG;
    $url = '';
    require_once( $CFG->libdir . '/filelib.php' );
    $context = context_course::instance( $id );
    $fs = get_file_storage();
    $files = $fs->get_area_files( $context->id, 'course', 'overviewfiles', 0 );
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url( $f->get_contextid(),
                $f->get_component(), $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    if (empty($url) && $ctrl) {
        $url = $OUTPUT->get_generated_image_for_id($id);
    }
    return $url;
}
/**
 * Front page blog img.
 *
 * @param int $id blog id.
 *
 */
function educard_get_blog_post_image($id) {
    global $OUTPUT, $CFG;
    $url = '';
    require_once( $CFG->libdir . '/filelib.php' );
    $syscontext = context_system::instance();
    $fs = get_file_storage();
    $files = $fs->get_area_files($syscontext->id, 'blog', 'attachment', $id);
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url( $f->get_contextid(),
                $f->get_component(), $f->get_filearea(), $id, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    if (empty($url)) {
        $url = $OUTPUT->get_generated_image_for_id($id);
    }
    return $url;
}
/**
 * Front page user img.
 *
 * @param int $id user id.
 *
 */
function educard_get_user_image($id) {
    global $OUTPUT, $CFG;
    $url = '';
    require_once( $CFG->libdir . '/filelib.php' );
    $context = context_user::instance( $id );
    $fs = get_file_storage();
    $files = $fs->get_area_files( $context->id, 'user', 'icon', 'educard', false);
    foreach ($files as $f) {
        if ($f->is_valid_image()) {
            $url = moodle_url::make_pluginfile_url( $f->get_contextid(), $f->get_component(),
                $f->get_filearea(), null, $f->get_filepath(), $f->get_filename(), false );
        }
    }
    if (empty($url)) {
        $url = $OUTPUT->get_generated_image_for_id($id);
    }
    return $url;
}
/**
 * Front page category.
 *
 * @param int $id category id.
 *
 */
function theme_educard_frontpageblockcategory($id) {
    GLOBAL  $DB;
    $category = $DB->get_record('course_categories', ['id' => $id]);
    if (!empty($category)) {
        $categoryname = $category->name;
    }
    return $categoryname;
}
/**
 * Front page links.
 *
 * @param string $links theme link footer.
 *
 */
function theme_educard_links($links) {
    $weblink = $links;
    $content = "";
    $target = "_self";
    $websettings = explode("\n", $weblink);
    foreach ($websettings as $key => $settingval) {
        if (!empty(trim($settingval))) {
            $expset = explode("|", $settingval);
            $target = "_self";
            $lurl = "#";
            $ltxt = "";
            $lang = current_language();
            $langcnt = false;
            if (isset($expset[3]) && !empty($expset[3]) ) {
                if ( trim($lang) == trim($expset[3])) {
                    $langcnt = true;
                }
            } else {
                $langcnt = true;
            }
            if ($langcnt) {
                if (isset($expset[0]) && isset($expset[1]) && isset($expset[4])) {
                    if (isset($expset[1]) && !empty($expset[1])) {
                        list($ltxt, $lurl, $comment, $lang, $target) = $expset;
                    }
                    $target = trim($target);
                    if ($target == "_self" || $target == "_blank" || $target == "_parent" || $target == "_top") {
                        $target = trim($target);
                    } else {
                        $target = "_self";
                    }
                } else if (isset($expset[0]) && isset($expset[1])) {
                    list($ltxt, $lurl) = $expset;
                } else {
                    $ltxt = $expset[0];
                    $lurl = "#";
                }
                $ltxt = trim($ltxt);
                $lurl = trim($lurl);
                if (empty($ltxt)) {
                    continue;
                }

                $content .= '<li><a class="text-decoration-none" href="'.$lurl.'" target="'.$target.'">'.$ltxt.'</a></li>';
            }
        }
    }
    return $content;
}
/**
 * Front page links.
 *
 * @param string $links header link.
 * @param string $mobil header mobil.
 *
 */
function theme_educard_header_links($links, $mobil) {
    $weblink = trim($links);
    $content = "";
    $lurl = "";
    $ltxt = "";
    $i = 0;
    $websettings = explode("\n", $weblink);
    if ($mobil) {
        $content .= "<div class= 'hynavbar-nav'>";
    } else {
        $content .= "<div class= 'navbar-nav'>";
    }
    foreach ($websettings as $key => $settingval) {
        if (!empty(trim($settingval))) {
            $expset = explode("|", $settingval);
            $j = uniqid();
            $lang = current_language();
            $langcnt = false;
            if (isset($expset[3]) && !empty($expset[3]) ) {
                if ( trim($lang) == trim($expset[3])) {
                    $langcnt = true;
                }
            } else {
                $langcnt = true;
            }
            if ($langcnt) {
                if (isset($expset[0]) && !empty($expset[0]) ) {
                    if (substr($expset[0], 0, 1) <> "-") {
                        if ($i != 0) {
                            $content .= "</div></div>";
                        }
                        $ltxt = trim($expset[0]);
                        $blank = "_self";
                        if (isset($expset[4])) {
                            $blank = trim($expset[4]);
                        }
                        if (isset($expset[1]) && !empty($expset[1])) {
                            $lurl = trim($expset[1]);
                            $content .= "<div class='nav-item'>";
                            $content .= "<a class='nav-item nav-link' href='".$lurl."' target='".$blank."'>".$ltxt."</a><div>";
                        } else {
                            $content .= "<div class='dropdown nav-item' id='dropdown-custom-".$j."'>";
                            $content .= "<a class='dropdown-toggle nav-link' id='drop-down-".$j."' data-toggle='dropdown' ";
                            $content .= "aria-haspopup='true' aria-expanded='false' href='#' title='".$ltxt."' ";
                            $content .= "aria-controls='drop-down-menu-".$j."'>".$ltxt."</a>";
                            $content .= "<div class='dropdown-menu' role='menu'
                                    id='drop-down-menu-".$j."' aria-labelledby='drop-down-".$j."'>";
                        }
                    } else {
                        $blank = "_self";
                        if (isset($expset[4])) {
                            $blank = trim($expset[4]);
                        }
                        if (empty($expset[1])) {
                            $expset[1] = "#";
                        }
                        if (isset($expset[1])) {
                            list($ltxt, $lurl) = $expset;
                            $ltxt = trim(substr($ltxt, 1, strlen($ltxt)));
                            $lurl = trim($lurl);
                            $content .= "<a class='dropdown-item' role='menuitem'
                                    href='".$lurl."' target='".$blank."'  title='".$ltxt. "'>".$ltxt."</a>";
                        }
                    }
                } else {
                    $ltxt = $expset[0];
                }
                $i++;
            }
        }
    }
    $content .= "</div></div>";
    $content .= "</div>";
    return $content;
}
/**
 * Theme header links.
 *
 * @param string $links theme header links.
 */
function theme_educard_links_mobil($links) {
    $weblink = trim($links);
    $content = "";
    $lurl = "";
    $ltxt = "";
    $i = 0;
    $websettings = explode("\n", $weblink);
    foreach ($websettings as $settingval) {
        if (!empty(trim($settingval))) {
            $expset = explode("|", $settingval);
            $lang = current_language();
            $langcnt = false;
            if (isset($expset[3]) && !empty($expset[3]) ) {
                if ( trim($lang) == trim($expset[3])) {
                    $langcnt = true;
                }
            } else {
                $langcnt = true;
            }
            if ($langcnt) {
                if (isset($expset[0]) && !empty($expset[0]) ) {
                    if (substr($expset[0], 0, 1) <> "-") {
                        if ($i != 0) {
                            $content .= "</ul></li>";
                        }
                        $ltxt = trim($expset[0]);
                        $blank = "_self";
                        if (isset($expset[4])) {
                            $blank = trim($expset[4]);
                        }
                        if (isset($expset[1]) && !empty($expset[1])) {
                            $lurl = trim($expset[1]);
                            $content .= "<li class='menu-item menu-item-has-children'>";
                            $content .= "<a href='".$lurl."' target='".$blank."'>".$ltxt."</a>";
                            $content .= "<ul class='sub-menu'>";
                        } else {
                            $content .= "<li class='menu-item menu-item-has-children'>";
                            $content .= "<a href='#'>".$ltxt."</a>";
                            $content .= "<ul class='sub-menu'>";
                        }
                    } else {
                        $blank = "_self";
                        if (isset($expset[4])) {
                            $blank = trim($expset[4]);
                        }
                        if (empty($expset[1])) {
                            $expset[1] = "#";
                        }
                        if (isset($expset[1])) {
                            list($ltxt, $lurl) = $expset;
                            $ltxt = trim(substr($ltxt, 1, strlen($ltxt)));
                            $lurl = trim($lurl);
                            $content .= "<li class='menu-item'>";
                            $content .= "<a href='".$lurl."' target='".$blank."' title='".$ltxt. "'>".$ltxt."</a>";
                            $content .= "</li>";
                        }
                    }
                } else {
                    $ltxt = $expset[0];
                }
                $i++;
            }
        }
    }
    if ($i != 0) {
        $content .= "</ul>";
    }
    return $content;
}
/**
 * Front page random color.
 */
function theme_educard_random_color() {
    /*
    * Any of the following methods can be used to find random color.
    * $randcolor = "#".str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT);.
    * $randcolor = "#".substr(md5(rand()), 0, 6);.
    * $randcolor = '#'.substr(str_shuffle('ABCDEF0123456789'), 0, 6);.
    */
    $randcolor = "rgba(".rand(0, 255).", ".rand(0, 255).", ".rand(0, 255).")";
    return $randcolor;
}
/**
 * Front page bg img or color.
 *
 */
function theme_educard_block_bg_img_color() {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    $scss = "";
    // Block 1.
    if (!empty($theme->settings->block01color)) {
        $scss .= '.block01-bg-color { ';
        $scss .= "background-color: " . $theme->settings->block01color ." !important;\n";
        $scss .= '}';
    }
    for ($dsn = 1; $dsn <= 3; $dsn++) {
        $image = $theme->setting_file_url("imgblock01background", "imgblock01background");
        if (empty($image)) {
            if (!empty($theme->settings->frontpageimglink)) {
                $image = $theme->settings->frontpageimglink."block01/d".$dsn."/1.jpg";
            } else {
                $image = null;
            }
        }
        if (!empty($image)) {
            $scss .= '.block01-'.$dsn.'-bg-img { ';
            $scss .= "background: url(" . $image .") no-repeat center center fixed;\n}";
        }
    }
    // Block 2.
    for ($i = 1; $i <= 4; $i++) {
        $image = $theme->setting_file_url("sliderimageblock02img".$i, "sliderimageblock02img".$i);
        if (empty($image)) {
            if (empty($theme->settings->frontpageimglink)) {
                $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 10));
            } else {
                $image = $theme->settings->frontpageimglink."block02/d1/".$i.".jpg";
            }
        }
        $scss .= '.block02-1-bg-img-'.$i.' { ';
        $scss .= "background-image: url(" . $image .");\n";
        $scss .= '}';
    }
    // Block 4.
    for ($dsn = 1; $dsn <= 2; $dsn++) {
        for ($i = 1; $i <= 8; $i++) {
            $image = $theme->setting_file_url("sliderimageblock04img".$i, "sliderimageblock04img".$i);
            if (empty($image)) {
                if (empty($theme->settings->frontpageimglink)) {
                    $image = $OUTPUT->get_generated_image_for_id(rand(1963, 100));
                } else {
                    $image = $theme->settings->frontpageimglink."block04/d".$dsn."/".$i.".jpg";
                }
            }
            $scss .= '.block04-'.$dsn.'-bg-img-'.$i.' { ';
            $scss .= "background-image: url(" . $image .");\n";
            if (!empty($theme->settings->block04imgheight)) {
                $scss .= "height: " . $theme->settings->block04imgheight ."px !important;\n";
            }
            $scss .= '}';
        }
    }

    // Block 5.
    $image = $theme->setting_file_url("sliderimageblock05img", "sliderimageblock05img");
    if (empty($image)) {
        $image = theme_educard_imgurlcntrl("05", 1);
    }
    if (!empty($image)) {
        $scss .= '.block05-bg-img { ';
        $scss .= "background-image: url(" . $image .");\n";
        $scss .= '}';
    }

    $image = $theme->setting_file_url('sliderimageblock05img', 'sliderimageblock05img');
    if (empty($image)) {
        if (empty($theme->settings->frontpageimglink)) {
            $image = $OUTPUT->get_generated_image_for_id(rand(25021963, 10));
        } else {
            $image = $theme->settings->frontpageimglink."block05/d1/1.jpg";
        }
    }
    $scss .= '.block05-1-bg-img { ';
    $scss .= "background-image: url(" . $image .");\n";
    $scss .= '}';

    // Block 6.
    if (!empty($theme->settings->block06color)) {
        $scss .= '.block06-bg-color { ';
        $scss .= "background-color: " . $theme->settings->block06color .";\n";
        $scss .= '}';
    }
    // Block 9.
    $count = $theme->settings->block09count;
    for ($i = 1; $i <= $count; $i++) {
        $scss .= '.block09-bg-color-'.$i.' { ';
        $scss .= "background-color: " . theme_educard_random_color() ." !important;\n";
        $scss .= '}';
    }
    // Main slider.
    // Sliderdesing == 2 || sliderdesing == 3.
    for ($dsn = 2; $dsn <= 4; $dsn++) {
        for ($i = 1; $i <= $theme->settings->slidercount; $i++) {
            $image = $theme->setting_file_url("sliderimage".$i, "sliderimage".$i);
            if (empty($image)) {
                if ($dsn !== 4) {
                    if (empty($theme->settings->frontpageimglink)) {
                        $image = $OUTPUT->get_generated_image_for_id(rand(1, 100));
                    } else {
                        $image = $theme->settings->frontpageimglink."slider/d".$dsn."/".$i.".jpg";
                    }
                }
            }
            if (!empty($image)) {
                $scss .= '.main-slider-'.$dsn.'-bg-img-'.$i.' { ';
                $scss .= "background-image: url(" . $image .");\n";
                $scss .= '}';
            }
        }
    }
    return $scss;
}
/**
 * Front page bg img.
 *
 */
function theme_educard_frontpage_bg_img() {
    $theme = theme_config::load('educard');
    $scss = "";
    // Block 1.
    if (!empty($theme->settings->block01bgimg)) {
        $scss = '.block01-bg { ';
        $scss .= "background-image: ";
        if (!empty($theme->settings->frontpagegradient1) && $theme->settings->block01gradienton == true ) {
            $scss .= $theme->settings->frontpagegradient1.',';
        }
        $scss .= "url('".$theme->settings->block01bgimg."'); background-repeat: no-repeat;";
        $scss .= ' }'. ";\n";
    } else if (!empty($theme->settings->frontpagegradient1) && $theme->settings->block01gradienton == true ) {
        $scss = '.block01-bg { ';
        $scss .= "background-image: ";
        $scss .= $theme->settings->frontpagegradient1.';';
        $scss .= ' }'. ";\n";
    }
    // Blocks (2 3 4 5 6 7 8 9 10 11 12 19 21).
    $blocks = ['02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12', '13', '14', '19', '21'];
    foreach ($blocks as $block) {
        $scss .= theme_educard_frontpage_bg_img_next($block);
    }
    // Pages.
    $pages = ['02'];
    foreach ($pages as $page) {
        $scss .= theme_educard_page_bg_img_next($page);
    }
    return $scss;
}
/**
 * Front page bg img next.
 *
 * @param int $block block.
 *
 */
function theme_educard_frontpage_bg_img_next($block) {
    $theme = theme_config::load('educard');
    $scss = "";
    $blockxxbgimg = "block".$block."bgimg";
    $blockxxgradienton = "block".$block."gradienton";
    if (!empty($theme->settings->$blockxxbgimg)) {
        $scss = '.block'.$block.'-bg { ';
        $scss .= "background-image: ";
        if (!empty($theme->settings->frontpagegradient1) && $theme->settings->$blockxxgradienton == true ) {
            $scss .= $theme->settings->frontpagegradient1.',';
        }
        $scss .= "url('".$theme->settings->$blockxxbgimg."')";
        if (!empty($theme->settings->frontpagegradient2) && $theme->settings->$blockxxgradienton == true ) {
            $scss .= ",".$theme->settings->frontpagegradient2.';';
        } else {
            $scss .= ";";
        }
        $scss .= ' background-repeat: no-repeat; }'. ";\n";
    } else if ($theme->settings->$blockxxgradienton == true
            && ( (!empty($theme->settings->frontpagegradient1)) || (!empty($theme->settings->frontpagegradient2)) )) {
        $scss = '.block'.$block.'-bg { ';
        $scss .= "background-image: ";
        if (!empty($theme->settings->frontpagegradient1)) {
            $scss .= $theme->settings->frontpagegradient1;
        }
        if (!empty($theme->settings->frontpagegradient2)) {
            if (!empty($theme->settings->frontpagegradient1)) {
                $scss .= ",";
            }
            $scss .= $theme->settings->frontpagegradient2.";";
        } else {
            $scss .= ";";
        }
        $scss .= ' }'. ";\n";
    }
    return $scss;
}
/**
 * Page bg img next.
 *
 * @param int $page page.
 *
 */
function theme_educard_page_bg_img_next($page) {
    $theme = theme_config::load('educard');
    $scss = "";
    $pagexxbgimg = "page".$page."bgimg";
    $pagexxgradienton = "page".$page."gradienton";
    if (!empty($theme->settings->$pagexxbgimg)) {
        $scss = '.page'.$page.'-bg { ';
        $scss .= "background-image: ";
        if (!empty($theme->settings->frontpagegradient1) && $theme->settings->$pagexxgradienton == true ) {
            $scss .= $theme->settings->frontpagegradient1.',';
        }
        $scss .= "url('".$theme->settings->$pagexxbgimg."')";
        if (!empty($theme->settings->frontpagegradient2) && $theme->settings->$pagexxgradienton == true ) {
            $scss .= ",".$theme->settings->frontpagegradient2.';';
        } else {
            $scss .= ";";
        }
        $scss .= ' background-repeat: no-repeat; }'. ";\n";
    } else if ($theme->settings->$pagexxgradienton == true
            && ( (!empty($theme->settings->frontpagegradient1)) || (!empty($theme->settings->frontpagegradient2)) )) {
        $scss = '.page'.$page.'-bg { ';
        $scss .= "background-image: ";
        if (!empty($theme->settings->frontpagegradient1)) {
            $scss .= $theme->settings->frontpagegradient1;
        }
        if (!empty($theme->settings->frontpagegradient2)) {
            if (!empty($theme->settings->frontpagegradient1)) {
                $scss .= ",";
            }
            $scss .= $theme->settings->frontpagegradient2.";";
        } else {
            $scss .= ";";
        }
        $scss .= ' }'. ";\n";
    }
    return $scss;
}
/**
 * Front page img control.
 *
 * @param int $block block.
 * @param int $i and count.
 *
 */
function theme_educard_imgurlcntrl($block, $i) {
    global $OUTPUT;
    $theme = theme_config::load('educard');
    if (empty($theme->settings->frontpageimglink)) {
        $image = $OUTPUT->get_generated_image_for_id($block + 20);
    } else {
        $img = theme_educard_imgurlfind($theme->settings->frontpageimglink."block".$block."/".$i);
        if (!empty($img)) {
            $image = $img;
        } else {
            $image = $OUTPUT->get_generated_image_for_id($block + 4);
        }
    }
    return $image;
}
/**
 * Front page img find.
 *
 * @param string $name url name.
 *
 */
function theme_educard_imgurlfind($name) {
    // Initialize cURL changed too slow.
    $extensions = ['svg', 'png', 'jpg', 'jpeg' ];
    foreach ($extensions as $ext) {
        $ch = curl_init($name.".".$ext);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $imgcontrol = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($imgcontrol == 200) {
            return $name.".".$ext;
            break;
        }
    }
    return null;
}
/**
 * Front page img find.
 *
 * @param string $name url name.
 *
 */
function theme_educard_imgurlfind_single($name) {
    // Initialize cURL changed too slow.
    $ch = curl_init($name);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_exec($ch);
    $imgcontrol = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    curl_close($ch);
    if ($imgcontrol == 200) {
        return $name;
    }

    return null;
}
/**
 * Find category name.
 *
 * @param int $id category id.
 */
function theme_educard_ctgname($id) {
    GLOBAL  $DB;
    $category = $DB->get_record('course_categories', ['id' => $id]);
    if (!empty($category)) {
        $categoryname = $category->name;
    } else {
        $categoryname = "not found";
    }
    return $categoryname;
}
/**
 * Block course details.
 *
 * @param int $courseid course id.
 */
function theme_educard_course_single($courseid) {
    GLOBAL  $PAGE, $OUTPUT, $DB;
    $theme = theme_config::load('educard');
    $coursecontext['sc_counter'] = 0;
    if (!empty($courseid)) {
        $courses = $DB->get_records_sql("SELECT * FROM {course} WHERE  id =".$courseid);
    }
    $j = 0;
    $sql = "SELECT  en.courseid, en.cost, en.currency";
    $sql = $sql." FROM {enrol} en";
    $sql = $sql." WHERE en.courseid = :courseid and en.status = 0 and en.cost != 'NULL'";
    if (!empty($courses)) {
        foreach ($courses as $id => $course) {
            $context = context_course::instance($id);
            $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php',
            $context->id, 'course', 'summary', false);
            $coursecontext['sc_categoryname'] = theme_educard_ctgname($course->category);
            $coursecontext['sc_categorylink'] = "course/index.php?categoryid=".$course->category;
            $coursecontext['sc_fullname'] = $course->fullname;
            $coursecontext['sc_summary'] = $summary;
            $coursecontext['sc_imgurl'] = educard_get_course_image($id, null);
            $coursecontext['sc_shortname'] = $course->shortname;
            $coursecontext['sc_update'] = gmdate("M d,Y", $course->timemodified);
            $coursecontext['sc_start_date'] = gmdate("d M Y", $course->startdate);
            $coursecontext['sc_courselink'] = "course/view.php?id=".$id;
            $coursecontext['sc_counter'] = $j + 1;
            $sectiontotal = $DB->count_records('course_sections', ['course' => $id]);
            $coursecontext['sc_format'] = $sectiontotal." of ". $course->format;
            $enrol = $DB->get_records_sql($sql, ['courseid' => $id]);
            if (empty($enrol)) {
                $coursecontext['sc_currency'] = "Free";
            } else {
                foreach ($enrol as $enrols) {
                    $coursecontext['sc_cost'] = $enrols->cost;
                    $coursecontext['sc_currency'] = $enrols->currency;
                };
            }
            // Editingteacher role id 9.
            $role = $theme->settings->block07teacherrole;
            $teachers = get_role_users($role, $context);
            if (!empty($teachers)) {
                foreach ($teachers as $id => $teacher) {
                    $coursecontext['sc_teachername'] = fullname($teacher);
                    $teacher->imagealt = get_string('defaultcourseteacher', 'moodle');
                    $userpicture = new user_picture($teacher);
                    $coursecontext['sc_userpicture'] =
                        $OUTPUT->user_picture($teacher, ['class' => '', 'size' => '512']);
                }
            }
            // Student role id 5.
            $role = $DB->get_field('role', 'id', ['id' => $theme->settings->block07studentrole]);
            $students = get_role_users($role, $context);
            $coursecontext['sc_studentscount'] = count($students);
            // Comments.
            $comments = $DB->get_records('comments',  ['contextid' => $context->id]);
            if (!empty($comments)) {
                $i = 0;
                foreach ($comments as $id => $comment) {
                    $coursecontext['sc_comment'][$i]['content'] = $comment->content;
                    $coursecontext['sc_comment'][$i]['timecreated'] = gmdate("d M Y", $comment->timecreated);
                    $person = $DB->get_record('user', ['id' => $comment->userid]);
                    $coursecontext['sc_comment'][$i]['username'] = fullname($person);
                    $userpicture = new user_picture($person);
                    $userpicture->size = 100;
                    $coursecontext['sc_comment'][$i]['userimg'] = $userpicture->get_url($PAGE)->out(false);
                    $i++;
                }
            }
            $j++;
        };
    }
    return $coursecontext;
}
