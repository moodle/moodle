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
 * slideshow.php
 *
 * @package     theme_eguru
 * @copyright   2015 LMSACE Dev Team,lmsace.com
 * @author      LMSACE Dev Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * slideshow
 * @return string
 */
function slideshow() {
    global $PAGE;

    $numberofslides = theme_eguru_get_setting('numberofslides');
    $slideimage = '';
    $visableslide = 0;

    for ($s1 = 1; $s1 <= $numberofslides; $s1++) {
        $slideimage .= theme_eguru_render_slideimg($s1, 'slide' . $s1 . 'image');
    }

    if ($slideimage) {

        if ($numberofslides) {

            $content = html_writer::start_tag('div', array('class' => 'homepage-carousel'));

            $content .= html_writer::start_tag('div', array('id' => 'home-page-carousel', 'class' => 'carousel slide', 'data-ride' => 'carousel'));

            $content .= html_writer::start_tag('ol', array('class' => 'carousel-indicators'));

            for ($s = 0; $s < $numberofslides; $s++):
                $clstxt = ($s == "0") ? ' class="active"' : '';
                $content .= html_writer::start_tag('li', array('data-target' => '#home-page-carousel', 'data-slide-to' => $s.$clstxt));
                $content .= html_writer::end_tag('li');
            endfor;

            $content .= html_writer::end_tag('ol');
            $content .= html_writer::start_tag('div', array('class' => 'carousel-inner', 'role' => 'listbox'));

            for ($s1 = 1; $s1 <= $numberofslides; $s1++):
                $slidecaption = theme_eguru_get_setting('slide'.$s1.'caption', true);
                $slidecaption = theme_eguru_lang($slidecaption);
                $slideimg = theme_eguru_render_slideimg($s1, 'slide' . $s1 . 'image');
                if (empty($slideimg)) {
                    $slideimg = '';
                }
                $slidebtn = theme_eguru_get_setting('slide'.$s1.'urltext');
                $slidebtn = theme_eguru_lang($slidebtn);
                $slidebtnurl = theme_eguru_get_setting('slide' . $s1 . 'url');
                $icon = "fa-angle-right";
                if (right_to_left()) {
                    $icon = "fa-angle-left";
                }

                if (!empty($slideimg)) {
                    $visableslide += 1;
                    $clstxt1 = ($visableslide == "1") ? ' active' : '';
                    $content .= html_writer::start_tag('div', array('
                    class' => 'carousel-item'.$clstxt1, 'style' => 'background-image: url('.$slideimg));
                    $content .= '<video autoplay muted loop id="myVideo" style="width:100% !important; objectif-fit: fill;">
                                   <source src="https://infans.fr/wp-content/uploads/2023/01/TEST5_1.mp4" type="video/mp4">
                                </video>';

                    $content .= html_writer::start_tag('div', array('class' => 'carousel-overlay-content container-fluid'));
                    if ($slidecaption != '' || $slidebtn != '') {
                         $content .= html_writer::start_tag('div', array('class' => 'carousel-content'));
                        $content .= html_writer::start_tag('h2');
                        $content .= $slidecaption;
                        $content .= html_writer::end_tag('h2');

                        if ($slidebtn != '') {
                            $content .= html_writer::start_tag('div', array('class' => 'carousel-btn'));

                            $content .= html_writer::start_tag('a', array('href' => $slidebtnurl, 'class' => 'read-more'));
                            $content .= $slidebtn;
                            $content .= html_writer::start_tag('i', array('class' => 'fa fa-arrow-right'));
                            $content .= html_writer::end_tag('i');

                            $content .= html_writer::end_tag('a');

                            $content .= html_writer::end_tag('div');
                        }
                        $content .= html_writer::end_tag('div');
                    }

                    $content .= html_writer::end_tag('div');

                    $content .= html_writer::end_tag('div');
                }
            endfor;
            if ($numberofslides > 1) {
                $content .= html_writer::start_tag('a', array('class' => 'left carousel-control carousel-control-prev', 'href' => '#home-page-carousel', '
                  data-slide' => 'prev'));

                $content .= '<span class="carousel-control-prev-icon"></span>';
                $content .= html_writer::end_tag('a');

                $content .= html_writer::start_tag('a', array('
                    class' => 'right carousel-control carousel-control-next', 'href' => '#home-page-carousel', 'data-slide' => 'next'));
                $content .= '<span class="carousel-control-next-icon"></span>';
                $content .= html_writer::end_tag('a');
              }

            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
    ?>
<style type="text/css">
    .theme-slider, #home-page-carousel .carousel-item {
      height:550px;
    }

.carousel-item-next.carousel-item-left,
.carousel-item-prev.carousel-item-right {
  -webkit-transform: translateX(0);
  transform: translateX(0);
}

@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-item-next.carousel-item-left,
  .carousel-item-prev.carousel-item-right {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
}

.carousel-item-next,
.active.carousel-item-right {
  -webkit-transform: translateX(100%);
  transform: translateX(100%);
}

@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-item-next,
  .active.carousel-item-right {
    -webkit-transform: translate3d(100%, 0, 0);
    transform: translate3d(100%, 0, 0);
  }
}

.carousel-item-prev,
.active.carousel-item-left {
  -webkit-transform: translateX(-100%);
  transform: translateX(-100%);
}

@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-item-prev,
  .active.carousel-item-left {
    -webkit-transform: translate3d(-100%, 0, 0);
    transform: translate3d(-100%, 0, 0);
  }
}

.carousel-fade .carousel-item {
  opacity: 0;
  transition-duration: .6s;
  transition-property: opacity;
}


@supports ((-webkit-transform-style: preserve-3d) or (transform-style: preserve-3d)) {
  .carousel-fade .carousel-item-next,
  .carousel-fade .carousel-item-prev,
  .carousel-fade .carousel-item.active,
  .carousel-fade .active.carousel-item-left,
  .carousel-fade .active.carousel-item-prev {
    -webkit-transform: translate3d(0, 0, 0);
    transform: translate3d(0, 0, 0);
  }
}
</style>

<?php

        }
    }
    return $content;
}
echo slideshow();