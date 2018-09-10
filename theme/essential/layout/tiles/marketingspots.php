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
 * @copyright   2015 Gareth J Barnard
 * @copyright   2014 Gareth J Barnard, David Bezemer
 * @copyright   2013 Julian Ridden
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$additionalmarketingclass = false;
$spotimage = array();
$additionalmarketingcontentclass = false;
$spotbutton = array();

for ($mspot = 1; $mspot <= 3; $mspot++) {
    $spotimage[$mspot] = \theme_essential\toolbox::get_setting('marketing'.$mspot.'image');
    if ($spotimage[$mspot]) {
        $additionalmarketingclass = true;
    }
    $spotbutton[$mspot] = $OUTPUT->essential_marketing_button($mspot);
    if ($spotbutton[$mspot]) {
        $additionalmarketingcontentclass = true;
    }
}
?>
<div class="row-fluid<?php
    echo ($additionalmarketingclass) ? ' withimage' : ' noimages';
    echo ($additionalmarketingcontentclass) ? ' withbutton' : ''; ?>" id="marketing-spots">
    <div class="row-fluid">
    <?php for ($mspot = 1; $mspot <= 3; $mspot++) {
            echo '<!-- Spot #'.$mspot.' -->'; ?>
        <div class="marketing-spot span4">
            <div class="title"><h5><span>
                <span aria-hidden="true" class="fa fa-<?php
                    echo \theme_essential\toolbox::get_setting('marketing'.$mspot.'icon'); ?>"></span>
                <?php echo \theme_essential\toolbox::get_setting('marketing'.$mspot, true); ?>
            </span></h5></div>
            <?php if ($spotimage[$mspot]) { ?>
                <div class="marketing-image-container">
                    <div class="marketing-image" id="marketing-image<?php echo $mspot; ?>"></div>
                </div>
            <?php
}
?>
            <div class="content<?php echo ($additionalmarketingcontentclass) ? ' withbutton' : ''; ?>">
                <?php
                    echo \theme_essential\toolbox::get_setting('marketing'.$mspot.'content', 'format_html');
                    echo $spotbutton[$mspot];
                ?>
            </div>
        </div>
        <?php
}
?>
    </div>
</div>
