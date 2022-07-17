phpcs:set Generic.WhiteSpace.ScopeIndent ignoreIndentationTokens[] T_CLOSE_TAG
<?php
defined('MOODLE_INTERNAL') || die(); // Make this always the 1st line in all CS fixtures.

if ($condition) {
    $somevar = true;
  $anothervar = true; // Bad aligned PHP.
      $anothervar = true; // Bad aligned PHP.
    // Next lines (PHP close, inline HTML and PHP start should be skipped.
?>
<div>
    <p>
        <span>some page content</span>
    </p>
</div>
    <?php
    // Back to work, incorrect indenting should be detected again.
    $somevar = true;
  $anothervar = true; // Bad aligned PHP.
      $anothervar = true; // Bad aligned PHP.
}
// This must not throw any indentation error, and running the Sniff
// under "exact mode" causes that, so we need to run it in unexact mode.
// Note 8 spaces indentation is the correct one for any line wrap, both
// for normal and control structure lines. (Thanks Tim for remembering it here).
if ($condition) {
    execute_one_function_having_a_very_long_description('and a lot of params', $like, $these,
            $causing, $us, $to, $split);
}
    ?>

<span>Let's try with a number of one-liners, any indentation accepted, reset on open/close allows it</span>

    <?php require('somefile.php'); ?>
        <?php require('somefile.php'); ?>
            <?php require('somefile.php'); ?>
<?php require('somefile.php'); ?>
    <?php
    if ($condition) {
        $somevar = true;
    ?>
    <p>and, with some html in the middle, in a new php block</p>
        <?php
      echo "this should fail";
        echo "this is OK";
        if ($condition) {
            echo "this is OK too";
        }
    }
    // There is freedom for closing tags, it's not clear the policy to follow when mixing multiple sections.
      ?>
// Adding some known content that caused problems. See CONTRIB-6146.
// Source: https://github.com/roelmann/moodle-theme_flexibase/blob/d99ca95f0b77c4cd2f877cf8b1d0715acb392cf4/layout/secure.php
<?php
if (!empty($PAGE->theme->settings->frontpagelayout)) {
    $layoutoption = $PAGE->theme->settings->frontpagelayout;
} else {
    $layoutoption = 'preandpost';
}

$hasmarketing = (empty($PAGE->theme->settings->togglemarketing)) ? false : $PAGE->theme->settings->togglemarketing;

require('includes/preheaderlogic.php');
require('includes/header.php');
?>

<div id="page" class="container-fluid">
    <?php require('includes/alerts.php'); ?>
    <?php require('includes/breadcrumb.php'); ?>

    <div id="page-header" class="clearfix">
        <!-- Start Carousel -->
        <?php require('includes/carousel2.php');?>
        <!-- End Carousel -->

        <!-- Start Marketing Spots -->
        <?php
        if ($hasmarketing == 1) {
            require_once(dirname(__FILE__).'/includes/marketing.php');
        } else if ($hasmarketing == 2 && !isloggedin()) {
            require_once(dirname(__FILE__).'/includes/marketing.php');
        } else if ($hasmarketing == 3 && isloggedin()) {
            require_once(dirname(__FILE__).'/includes/marketing.php');
        }
        ?>
        <!-- End Marketing Spots -->

        <div id="course-header">
            <?php echo $OUTPUT->course_header(); ?>
        </div>
        <div id="region-top">
            <?php
            if ($knownregiontop) {
                echo $OUTPUT->blocks('side-top', "sidetop flexcontainer");
            }
            ?>
        </div>
    </div>
</div>
// Adding some known content that caused problems. See CONTRIB-6146.
// Source: https://github.com/gjb2048/moodle-theme_essential/blob/a85a228057a3e8e62b2966e4bfe09177a40a361e/layout/report.php
<?php
require_once(\theme_essential\toolbox::get_tile_file('additionaljs'));
require_once(\theme_essential\toolbox::get_tile_file('header'));
?>

<div id="page" class="container-fluid">
    <?php require_once(\theme_essential\toolbox::get_tile_file('pagenavbar')); ?>
    <section role="main-content">
        <!-- Start Main Regions -->
        <div id="page-content" class="row-fluid">
            <div id="<?php echo $regionbsid ?>" class="span12">
                <div class="row-fluid">
                    <section id="region-main" class="span12">
<?php
if (($COURSE->id > 1) && (essential_report_page_has_title() == true)) {
    echo $OUTPUT->heading(format_string($COURSE->fullname), 1, 'coursetitle');
    echo '<div class="bor"></div>';
}
echo $OUTPUT->course_content_header();
echo $OUTPUT->main_content();
if (empty($PAGE->layout_options['nocoursefooter'])) {
    echo $OUTPUT->course_content_footer();
}
?>
                    </section>
                </div>
<?php
echo $OUTPUT->essential_blocks('side-pre', 'row-fluid', 'aside', 4);
?>
            </div>
        </div>
        <!-- End Main Regions -->
    </section>
</div>

<?php require_once(\theme_essential\toolbox::get_tile_file('footer')); ?>
</body>
</html>

<?php

// Now we are going to try a number of crazily nested indentations, mixing
// both functions and arrays, to verify that CONTRIB-6206 does not break again.

$somevariable = some_function(another_function($param1, $param2),
        more_function($param3, another_one(
                $key1, $value1,
                $key2, $value2)));
$continue = this_line_is_correct_and_works_ok();

$somevariable = some_function(another_function($param1, $param2),
        more_function($param3, array(
                $key1, $value1,
                $key2, $value2)));
$continue = this_line_is_correct_and_works_ok();

function read_competency_framework($id) {
    global $PAGE;

    $params = self::validate_parameters(self::read_competency_framework_parameters(),
                                        array(
                                            'id' => $id,
                                        ));

    $framework = api::read_framework($params['id']);
}

if ($showcompleted) {
    $feedbackcompleted = $DB->get_record('feedback_completed',
            array('feedback' => $feedback->id, 'id' => $showcompleted,
                'anonymous_response' => FEEDBACK_ANONYMOUS_YES), '*', MUST_EXIST);
    $responsetitle = get_string('response_nr', 'feedback') . ': ' .
        $feedbackcompleted->random_response . ' (' . get_string('anonymous', 'feedback') . ')';
}

// End of function/array fixtures (CONTRIB-6206).
