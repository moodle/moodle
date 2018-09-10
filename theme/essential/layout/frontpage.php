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
 * Essential is a clean and customizable theme.
 *
 * @package     theme_essential
 * @copyright   2016 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(\theme_essential\toolbox::get_tile_file('additionaljs'));
require_once(\theme_essential\toolbox::get_tile_file('header'));

$enable1alert = \theme_essential\toolbox::get_setting('enable1alert');
$enable2alert = \theme_essential\toolbox::get_setting('enable2alert');
$enable3alert = \theme_essential\toolbox::get_setting('enable3alert');

if ($enable1alert || $enable2alert || $enable3alert) {
    $alertinfo = '<span class="fa-stack"><span aria-hidden="true" class="fa fa-info fa-stack-1x fa-inverse"></span></span>';
    $alerterror = '<span class="fa-stack"><span aria-hidden="true" class="fa fa-warning fa-stack-1x fa-inverse"></span></span>';
    $alertsuccess = '<span class="fa-stack"><span aria-hidden="true" class="fa fa-bullhorn fa-stack-1x fa-inverse"></span></span>';
}
?>
<div id="page" class="container-fluid">
    <?php
    echo $OUTPUT->essential_blocks('header', 'row-fluid', 'aside', 'headerblocksperrow');
?>
    <section class="slideshow">
        <!-- Start Slideshow -->
        <?php
        $toggleslideshow = \theme_essential\toolbox::get_setting('toggleslideshow');
        if ($PAGE->user_is_editing() && ($toggleslideshow)) {
            require_once(\theme_essential\toolbox::get_tile_file('slideshow'));
        } else {
            if ($toggleslideshow == 1) {
                require_once(\theme_essential\toolbox::get_tile_file('slideshow'));
            } else if ($toggleslideshow == 2 && !isloggedin()) {
                require_once(\theme_essential\toolbox::get_tile_file('slideshow'));
            } else if ($toggleslideshow == 3 && isloggedin()) {
                require_once(\theme_essential\toolbox::get_tile_file('slideshow'));
            }
        }
?>
        <!-- End Slideshow -->
    </section>

    <section>
        <!-- Start Main Regions -->

        <!-- Start Alerts -->

        <!-- Alert #1 -->
        <?php if ($enable1alert) { ?>
            <div class="useralerts alert alert-<?php echo \theme_essential\toolbox::get_setting('alert1type'); ?>">
                <button type="button" class="close" data-dismiss="alert"><span class="fa fa-times-circle" aria-hidden="true"></span></button>
                <?php
                $alert1icon = 'alert' . \theme_essential\toolbox::get_setting('alert1type');
                echo $$alert1icon.'<span class="title">'.\theme_essential\toolbox::get_setting('alert1title', true);
                echo '</span>'.\theme_essential\toolbox::get_setting('alert1text', true); ?>
            </div>
<?php
}
?>

        <!-- Alert #2 -->
        <?php if ($enable2alert) { ?>
            <div class="useralerts alert alert-<?php echo \theme_essential\toolbox::get_setting('alert2type'); ?>">
                <button type="button" class="close" data-dismiss="alert"><span class="fa fa-times-circle" aria-hidden="true"></span></button>
                <?php
                $alert2icon = 'alert' . \theme_essential\toolbox::get_setting('alert2type');
                echo $$alert2icon.'<span class="title">'.\theme_essential\toolbox::get_setting('alert2title', true);
                echo '</span>'.\theme_essential\toolbox::get_setting('alert2text', true); ?>
            </div>
<?php
}
?>

        <!-- Alert #3 -->
        <?php if ($enable3alert) { ?>
            <div class="useralerts alert alert-<?php echo \theme_essential\toolbox::get_setting('alert3type'); ?>">
                <button type="button" class="close" data-dismiss="alert"><span class="fa fa-times-circle" aria-hidden="true"></span></button>
                <?php
                $alert3icon = 'alert' . \theme_essential\toolbox::get_setting('alert3type');
                echo $$alert3icon.'<span class="title">'.\theme_essential\toolbox::get_setting('alert3title', true);
                echo '</span>' . \theme_essential\toolbox::get_setting('alert3text', true); ?>
            </div>
<?php
}

if ($PAGE->user_is_editing()) {
    echo '<div class="alerteditbutton">';
    echo $OUTPUT->essential_edit_button('frontpage', get_string('alert_edit', 'theme_essential'));
    echo '</div>';
}
?>
        <!-- End Alerts -->

        <!-- Start Frontpage Content -->
        <?php
        $showfrontcontentsetting = \theme_essential\toolbox::get_setting('togglefrontcontent');
        if ($PAGE->user_is_editing() && ($showfrontcontentsetting)) {
            $showfrontcontent = true;
        } else {
            $showfrontcontent = false;
            switch ($showfrontcontentsetting) {
                case 1:
                    $showfrontcontent = true;
                    break;
                case 2:
                    if (!isloggedin()) {
                        $showfrontcontent = true;
                    }
                    break;
                case 3:
                    if (isloggedin()) {
                        $showfrontcontent = true;
                    }
                    break;
            }
        }
        if ($showfrontcontent) { ?>
            <div class="frontpagecontent">
                <div class="bor"></div>
                <?php
                echo \theme_essential\toolbox::get_setting('frontcontentarea', 'format_html');
                echo $OUTPUT->essential_edit_button('frontpage');
                ?>
                <div class="bor"></div>
            </div>
<?php
        }
?>
        <!-- End Frontpage Content -->

        <!-- Start Marketing Spots -->
        <?php
        $togglemarketing = \theme_essential\toolbox::get_setting('togglemarketing');
        if ($PAGE->user_is_editing() && ($togglemarketing)) {
            require_once(\theme_essential\toolbox::get_tile_file('marketingspots'));
        } else {
            if ($togglemarketing == 1) {
                require_once(\theme_essential\toolbox::get_tile_file('marketingspots'));
            } else if ($togglemarketing == 2 && !isloggedin()) {
                require_once(\theme_essential\toolbox::get_tile_file('marketingspots'));
            } else if ($togglemarketing == 3 && isloggedin()) {
                require_once(\theme_essential\toolbox::get_tile_file('marketingspots'));
            }
        }
?>
<!-- End Marketing Spots -->

<!-- Start Header blocks was Middle blocks -->
<?php
$frontpagehomeblocks = \theme_essential\toolbox::get_setting('frontpagemiddleblocks');
if ($PAGE->user_is_editing() && ($frontpagehomeblocks)) {
    require_once(\theme_essential\toolbox::get_tile_file('fphomeblocks'));
} else {
    if ($frontpagehomeblocks == 1) {
        require_once(\theme_essential\toolbox::get_tile_file('fphomeblocks'));
    } else if ($frontpagehomeblocks == 2 && !isloggedin()) {
        require_once(\theme_essential\toolbox::get_tile_file('fphomeblocks'));
    } else if ($frontpagehomeblocks == 3 && isloggedin()) {
        require_once(\theme_essential\toolbox::get_tile_file('fphomeblocks'));
    }
}
?>
<!-- End Header blocks was Middle blocks -->

        <div id="page-content" class="row-fluid">
            <section id="<?php echo $regionbsid; ?>">
<?php
$frontpageblocks = \theme_essential\toolbox::get_setting('frontpageblocks');
if ($frontpageblocks) {
    echo '<div id="content" class="span9 pull-right">';
} else {
    echo '<div id="content" class="span9 desktop-first-column">';
}
$fppagetopblocks = \theme_essential\toolbox::get_setting('fppagetopblocks');
if ($PAGE->user_is_editing() && ($fppagetopblocks)) {
    require_once(\theme_essential\toolbox::get_tile_file('fppagetopblocks'));
} else {
    if ($fppagetopblocks == 1) {
        require_once(\theme_essential\toolbox::get_tile_file('fppagetopblocks'));
    } else if ($fppagetopblocks == 2 && !isloggedin()) {
        require_once(\theme_essential\toolbox::get_tile_file('fppagetopblocks'));
    } else if ($fppagetopblocks == 3 && isloggedin()) {
        require_once(\theme_essential\toolbox::get_tile_file('fppagetopblocks'));
    }
}
echo '<section id="region-main">';
echo $OUTPUT->course_content_header();
echo $OUTPUT->main_content();
echo $OUTPUT->course_content_footer();
echo '</section>';
echo '</div>';
if ($frontpageblocks) {
    echo $OUTPUT->essential_blocks('side-pre', 'span3 desktop-first-column');
} else {
    echo $OUTPUT->essential_blocks('side-pre', 'span3 pull-right');
}
?>
            </section>
        </div>

        <!-- End Main Regions -->

        <?php if (is_siteadmin()) { ?>
            <div class="hidden-blocks">
                <div class="row-fluid">
                    <h4><?php echo get_string('visibleadminonly', 'theme_essential'); ?></h4>
                    <?php echo $OUTPUT->essential_blocks('hidden-dock'); ?>
                </div>
            </div>
<?php
}
?>

    </section>
</div>

<?php require_once(\theme_essential\toolbox::get_tile_file('footer')); ?>

</body>
</html>
