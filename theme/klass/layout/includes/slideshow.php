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
 * @package     theme_klass
 * @copyright   2015 LMSACE Dev Team,lmsace.com
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$numberofslides = theme_klass_get_setting('numberofslides');
$slideimage = array();
for ($s1 = 1; $s1 <= $numberofslides; $s1++) {
    $slideimage [] = theme_klass_render_slideimg($s1, 'slide' . $s1 . 'image');
}
$slideimage = (array_filter($slideimage, function($value) {

        return !is_null($value) && $value !== '';
})
);

$countslideimage = count($slideimage);
if ($countslideimage > 0) {
    if ($numberofslides) {
        ?>

        <div class="theme-slider">
          <div id="home-page-carousel" class="carousel slide" data-ride="carousel" data-interval= "2000">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <?php for($s = 0; $s < $numberofslides; $s++):
                         $clstxt = ($s == "0") ? ' class="active"' : '';
                    ?>
             <li data-target="#home-page-carousel" data-slide-to="<?php echo $s; ?>" <?php echo $clstxt; ?>></li>
                    <?php
                endfor; ?>
            </ol>

            <!-- Wrapper for slides -->
            <div class="carousel-inner" role="listbox">

                <?php
                $allcontent = "";

                $visableslide = 0;
                for($s1 = 1; $s1 <= $numberofslides; $s1++):
                    $slidecaption = theme_klass_get_setting('slide' . $s1 . 'caption', true);
                    $slideimg = theme_klass_render_slideimg($s1, 'slide' . $s1 . 'image');
                    $icon = "fa-angle-right";
                    if (right_to_left()) {
                        $icon = "fa-angle-left";
                    }
                    $content = '';
                    $slidebtn = theme_klass_get_setting('slide'.$s1.'urltext');
                    $slidebtn = theme_klass_lang($slidebtn);
                    $slideurl = theme_klass_get_setting('slide' . $s1 . 'url');

                    if ($slideimg) {

                        $visableslide += 1;
                        $clstxt1 = ($visableslide == "1") ? ' active' : '';
                        $content .= html_writer::start_tag('div', array('class' => "carousel-item ".
                            $clstxt1, 'style' => "background-image:url(".$slideimg.")"));
                        $content .= html_writer::start_tag('div', array('class' => "carousel-overlay-content container-fluid"));
                        $content .= html_writer::start_tag('div', array('class' => "content-wrap"));

                        if ($slidecaption != '' || $slidebtn != '') {
                            $content .= html_writer::start_tag('div', array('class' => 'carousel-content'));
                            if ($slidecaption) {
                                $content .= html_writer::start_tag('h2');
                                $content .= $slidecaption;
                                $content .= html_writer::end_tag('h2');
                            }

                            if ($slidebtn != '') {
                                $content .= html_writer::start_tag('div', array('class' => 'carousel-btn'));

                                $content .= html_writer::start_tag('a', array('href' => $slideurl, 'class' => 'read-more'));
                                $content .= $slidebtn;
                                $content .= html_writer::end_tag('a');

                                $content .= html_writer::end_tag('div');
                            }
                        }
                        $content .= html_writer::end_tag('div');
                        $content .= html_writer::empty_tag('br');
                        $content .= html_writer::end_tag('div');
                        $content .= html_writer::end_tag('div');
                        $content .= html_writer::end_tag('div');
                    }
                    $allcontent .= $content;

                endfor;
                    echo $allcontent;
            ?>

            </div>
                <?php
                if ($countslideimage > 1) {
                ?>
                <div>
                  <a class="left carousel-control carousel-control-prev" href="#home-page-carousel" data-slide="prev"></a>
                  <a class="right carousel-control carousel-control-next" href="#home-page-carousel" data-slide="next"></a>
                </div>
                <?php
                }
                ?>

          </div>

        </div>
        <style>

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
        <!--E.O.Slider-->
    <?php }
}
