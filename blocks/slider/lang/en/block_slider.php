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
 * Simple slider block for Moodle
 *
 * @package   block_slider
 * @copyright 2015-2020 Kamil Łuczak    www.limsko.pl     kamil@limsko.pl
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
$string['pluginname'] = 'Slider';
$string['slider:addinstance'] = 'Add a new Slider block';
$string['slider:myaddinstance'] = 'Add a new Slider block to the My Moodle page';
$string['slider:manage'] = 'Manage slider';
$string['header'] = 'Slider header';
$string['privacy:metadata:block'] = 'Slider stores some of its data within the block subsystem.';
$string['privacy:metadata:slider_slides'] = 'Block slider stores most of its data inside slider_slides database table.';
$string['privacy:metadata:slide_title'] = 'User created slide title';
$string['privacy:metadata:slide_desc'] = 'User created slide description';
$string['privacy:metadata:slide_link'] = 'User created slide link';
$string['privacy:metadata:slide_image'] = 'User uploaded slide image';
$string['config_width'] = 'Player base width in px';
$string['config_height'] = 'Player base height in px';
$string['config_width_help'] = 'Best is to use images original width';
$string['config_height_help'] = 'Best is to use images original height';
$string['nav'] = 'Navigation';
$string['nav_desc'] = 'Enables/disables prev/next navigation below slider';
$string['pag'] = 'Pagination';
$string['pag_desc'] = 'Enables/disables pagination below slider';
$string['auto_play'] = 'Auto play slides';
$string['auto_play_desc'] = 'Enables/disables auto playing of slides';
$string['effect'] = 'Slide Effect';
$string['int'] = 'Slide interval in ms';
$string['noimages'] = 'Please enter block config and add some images';
$string['manage_slides'] = 'Manage slides';
$string['add_slide'] = 'Add slide';
$string['modify_slide'] = 'Modify slide';
$string['slide_url'] = 'Slide URL';
$string['slide_title'] = 'Slide Title';
$string['slide_desc'] = 'Slide Description';
$string['slide_order'] = 'Slide Order';
$string['slide_image'] = 'Slide Image';
$string['slider_id_for_filter'] = '<i class="fa fa-icon fa-info-circle"></i> Use <code>[SLIDER-{$a}]</code> if You want to display this Slider with Filter Slider.';
$string['saved'] = 'Slide saved';
$string['slider_js'] = 'Slider JS library';
$string['confirm_deletion'] = 'Do You really want to delete this slide?';
$string['deleted'] = 'Slide has been deleted';
$string['new_slide_image'] = 'Upload new Slide Image';
$string['donation'] = 'If You like my plugin, I will be thankful if You send me a small donation.';
$string['slidesjs_h1'] = 'SlideJS Settings';
$string['bxslider_h1'] = 'config_bxSlider Settings';
$string['config_bx_captions'] = 'Display image titles';
$string['config_bx_captions_help'] = 'Include image captions. Captions can be set when managing slider images.';
$string['config_bx_hideonhover'] = 'Show slide caption/description on hover';
$string['config_bx_hideonhover_help'] = 'Show slide caption and description only on hover. When enabled caption will be hidden until mouse is over slideshow.';
$string['config_bx_displaydesc'] = 'Display image descriptions';
$string['config_bx_displaydesc_help'] = 'Include image descriptions. Descriptions can be set when managing slider images.';
$string['config_bx_responsive'] = 'Responsive slider';
$string['config_bx_responsive_help'] = 'Enable or disable auto resize of the slider. Useful if you need to use fixed width sliders.';
$string['config_bx_pager'] = 'Pager';
$string['config_bx_pager_help'] = 'If true, a pager will be added.';
$string['config_bx_controls'] = 'Controls';
$string['config_bx_controls_help'] = 'If true, "Next" / "Prev" controls will be added';
$string['config_bx_auto'] = 'Auto';
$string['config_bx_auto_help'] = 'Slides will automatically transition';
$string['config_bx_stopAutoOnClick'] = 'Stop auto on click';
$string['config_bx_stopAutoOnClick_help'] = 'Auto will stop on interaction with controls';
$string['config_bx_speed'] = 'Transition duration';
$string['config_bx_speed_help'] = 'Slide transition duration (in ms)';
$string['config_bx_useCSS'] = 'Use CSS';
$string['config_bx_useCSS_help'] = 'If true, CSS transitions will be used for horizontal and vertical slide animations (this uses native hardware acceleration). If false, jQuery animate() will be used.';