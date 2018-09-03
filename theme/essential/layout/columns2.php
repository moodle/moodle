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

$pagebottomregion = \theme_essential\toolbox::has_page_bottom_region();
?>

<div id="page" class="container-fluid">
    <?php require_once(\theme_essential\toolbox::get_tile_file('pagetopheader')); ?>
    <!-- Start Main Regions -->
    <div id="page-content" class="row-fluid">
        <div id="<?php echo $regionbsid ?>" class="span12">
            <div class="row-fluid">
                <?php require_once(\theme_essential\toolbox::get_tile_file('twocolumncontent')); ?>
            </div>
            <?php
            if ($pagebottomregion) {
                echo $OUTPUT->essential_blocks('side-pre', 'row-fluid', 'aside', 'pagebottomblocksperrow');
            }
?>
        </div>
    </div>
    <!-- End Main Regions -->
</div>

<?php require_once(\theme_essential\toolbox::get_tile_file('footer')); ?>
</body>
</html>
