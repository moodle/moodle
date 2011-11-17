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
 *
 * @package    qtype
 * @subpackage ddmarker
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addingddmarker'] = 'Adding drag and drop markers';
$string['addmoredropzones'] = 'Blanks for {no} more drop zones';
$string['addmoreitems'] = 'Blanks for {no} more markers';
$string['alttext'] = 'Alt text';
$string['answer'] = 'Answer';
$string['bgimage'] = 'Background image';
$string['coords'] = 'Coords';
$string['correctansweris'] = 'The correct answer is: {$a}';
$string['ddmarker'] = 'Drag and drop markers';
$string['ddmarker_help'] = 'Select a background image file, enter text labels for markers and define the drop zones on the background image to which they must be dragged.';
$string['ddmarkersummary'] = 'Markers are dragged and dropped onto a background image.';
$string['draggableimage'] = 'Draggable image';
$string['draggableitem'] = 'Draggable item';
$string['draggableitemheader'] = 'Draggable item {$a}';
$string['draggableitemtype'] = 'Type';
$string['draggableword'] = 'Draggable text';
$string['dropzone'] = 'Drop zone {$a}';
$string['dropzoneheader'] = 'Drop zones';
$string['editingddmarker'] = 'Editing drag and drop markers';
$string['followingarewrong'] = 'The following markers have been dragged to the wrong position : {$a}.';
$string['followingarewrongandhighlighted'] = 'The following markers need to be dragged to the correct drop zone : {$a}.  The drop zones they should have been dragged to are shown above.<br />Click on the markers above to see the drop zones they should have been dropped in highlighted.';
$string['formerror_nobgimage'] = 'You need to select an image to use as the background for the drag and drop area.';
$string['formerror_noitemselected'] = 'You have specified a drop zone but not chosen a marker that must be dragged to the zone';
$string['formerror_nosemicolons'] = 'There are no semicolons in your coordinates string. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_notagsallowed'] = 'No html tags are allowed in the label for a marker';
$string['formerror_onlyusewholepositivenumbers'] = 'Please use only whole positive numbers to specify x,y coords and/or width and height of shapes. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_polygonmusthaveatleastthreepoints'] = 'For a polygon shape you need to specify at least 3 points. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_shapeoutsideboundsofbgimage'] = 'The shape you have defined goes out of the bounds of the background image';
$string['formerror_toomanysemicolons'] = 'There are too many semi colon separated parts to the coordinates you have specified. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_unrecognisedwidthheightpart'] = 'We do not recognise the width and height you have specified. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_unrecognisedxypart'] = 'We do not recognise the x,y coordinates you have specified. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['infinite'] = 'Infinite';
$string['marker'] = 'Marker';
$string['marker_n'] = 'Marker {no}';
$string['markers'] = 'Markers';
$string['nolabel'] = 'No label text';
$string['pleasedragatleastonemarker'] = 'Your answer is not complete, please drag at least one marker onto the image.';
$string['previewarea'] = 'Preview area -';
$string['previewareaheader'] = 'Preview';
$string['previewareamessage'] = 'Select a background image file, enter text labels for markers and define the drop zones on the background image to which they must be dragged.';
$string['refresh'] = 'Refresh Preview';
$string['clearwrongparts'] = 'Move incorrectly placed markers back to default start position below image';
$string['shape'] = 'Shape';
$string['shape_circle'] = 'Circle';
$string['shape_circle_coords'] = 'centerx, centery; radius';
$string['shape_rectangle'] = 'Rectangle';
$string['shape_rectangle_coords'] = 'leftx, topy; width, height';
$string['shape_polygon'] = 'Polygon';
$string['shape_polygon_coords'] = 'x1,y1;x2,y2;x3,y3;x4,y4....';
$string['showmisplaced'] = 'Highlight drop zones which have not had the correct drag item dropped on them';
$string['shuffleimages'] = 'Shuffle Drag Items Each Time Question Is Attempted';
$string['stateincorrectlyplaced'] = 'State which markers are incorrectly placed';
$string['summariseplace'] = '{$a->no}. {$a->text}';
$string['summariseplaceno'] = 'Drop zone {$a}';
$string['ytop'] = 'Top';