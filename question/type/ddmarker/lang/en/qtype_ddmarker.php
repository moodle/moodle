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
 * Language strings for qtype_ddmarker.
 * @package   qtype_ddmarker
 * @copyright 2012 The Open University
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['addmoreitems'] = 'Blanks for {no} more markers';
$string['alttext'] = 'Alt text';
$string['answer'] = 'Answer';
$string['bgimage'] = 'Background image';
$string['coords'] = 'Coordinates';
$string['correctansweris'] = 'The correct answer is: {$a}';
$string['draggableimage'] = 'Draggable image';
$string['draggableitem'] = 'Draggable item';
$string['draggableitemheader'] = 'Draggable item {$a}';
$string['draggableitemtype'] = 'Type';
$string['draggableword'] = 'Draggable text';
$string['dropbackground'] = 'Background image for dragging markers onto';
$string['dropzone'] = 'Drop zone {$a}';
$string['dropzoneheader'] = 'Drop zones';
$string['dropzones'] = 'Drop zones';
$string['dropzones_help'] = 'Drop zones may be defined by coordinates, or dragged into position in the preview above.

First selecting a shape (circle, rectangle or polygon) will add a new drop zone shape to the top left of the preview. It may be useful to minimise the Markers section so you can see the preview while editing the Drop zones.

Editing a shape starts with a click on the shape in the preview to show the editing handles. You can move the shape using the center handle, or adjust the shape\'s dimensions with the vertex handles.

For polygons only, holding the control button (command button on a Mac) while clicking on a vertex handle will add a new vertex to the polygon. Please keep a polygon shape as simple as possible, without crossing lines.

For information the three shapes use coordinates in this way:

* Circle: centre_x, centre_y; radius<br />for example: <code>80,100;50</code>
* Rectangle: top_left_x, top_left_y; width, height<br />for example: <code>20,60;80,40</code>
* Polygon: x1, y1; x2, y2; ...; xn, yn<br />for example: <code>20,60;100,60;20,100</code>

Selecting a Marker text will add that text to the shape in the preview.';
$string['followingarewrong'] = 'The following markers have been placed in the wrong area : {$a}.';
$string['followingarewrongandhighlighted'] = 'The following markers were incorrectly placed :  {$a}. Highlighted marker(s) are now shown with the correct placement(s).<br /> Click on the marker to highlight the allowed area.';
$string['formerror_nobgimage'] = 'You need to select an image to use as the background for the drag and drop area.';
$string['formerror_noitemselected'] = 'You have specified a drop zone but not chosen a marker that must be dragged to the zone.';
$string['formerror_nosemicolons'] = 'There are no semicolons in your coordinates string. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_onlysometagsallowed'] = 'Only "{$a}" tags are allowed in the label for a marker.';
$string['formerror_onlyusewholepositivenumbers'] = 'Please use only whole positive numbers to specify x,y coordinates and/or width and height of shapes. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_polygonmusthaveatleastthreepoints'] = 'For a polygon shape you need to specify at least 3 points. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_repeatedpoint'] = 'You have entered the same coordinates twice. Each point must be unique. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_shapeoutsideboundsofbgimage'] = 'The shape you have defined goes out of the bounds of the background image.';
$string['formerror_toomanysemicolons'] = 'There are too many semicolon-separated parts to the coordinates you have specified. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_unrecognisedwidthheightpart'] = 'The width and height that you have specified are unrecognisable. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['formerror_unrecognisedxypart'] = 'The x,y coordinates that you have specified are unrecognisable. Your coordinates for a {$a->shape} should be expressed as - {$a->coordsstring}.';
$string['infinite'] = 'Infinite';
$string['marker'] = 'Marker';
$string['marker_n'] = 'Marker {no}';
$string['markers'] = 'Markers';
$string['nolabel'] = 'No label text';
$string['noofdrags'] = 'Number';
$string['pleasedragatleastonemarker'] = 'Your answer is not complete; you must place at least one marker on the image.';
$string['pluginname'] = 'Drag and drop markers';
$string['pluginname_help'] = 'Drag and drop markers require the respondent to drag text labels and drop them into defined drop zones on a background image.';
$string['pluginname_link'] = 'question/type/ddmarker';
$string['pluginnameadding'] = 'Adding drag and drop markers';
$string['pluginnameediting'] = 'Editing drag and drop markers';
$string['pluginnamesummary'] = 'Markers are dragged and dropped onto a background image.';
$string['previewareaheader'] = 'Preview';
$string['previewareamessage'] = 'Select a background image file, enter text labels for markers and define the drop zones on the background image to which they must be dragged.';
$string['privacy:metadata'] = 'The Drag and drop markers question type plugin does not store any personal data.';
$string['refresh'] = 'Refresh preview';
$string['clearwrongparts'] = 'Move incorrectly placed markers back to default start position below image';
$string['shape'] = 'Shape';
$string['shape_circle'] = 'Circle';
$string['shape_circle_lowercase'] = 'circle';
$string['shape_circle_coords'] = 'x,y;r (where x,y are the coordinates of the centre of the circle and r is the radius)';
$string['shape_rectangle'] = 'Rectangle';
$string['shape_rectangle_lowercase'] = 'rectangle';
$string['shape_rectangle_coords'] = 'x,y;w,h (where x,y are the coordinates of the top left corner of the rectangle and w and h are the width and height of the rectangle)';
$string['shape_polygon'] = 'Polygon';
$string['shape_polygon_lowercase'] = 'polygon';
$string['shape_polygon_coords'] = 'x1,y1;x2,y2;x3,y3;x4,y4... (where x1, y1 are the coordinates of the first vertex, x2, y2 are the coordinates of the second etc. There is no need to repeat the coordinates for the first vertex to close the polygon.)';
$string['showmisplaced'] = 'Highlight drop zones which have not had the correct marker dropped on them';
$string['shuffleimages'] = 'Shuffle drag items each time question is attempted';
$string['stateincorrectlyplaced'] = 'State which markers are incorrectly placed';
$string['summariseplace'] = '{$a->no}. {$a->text}';
$string['summariseplaceno'] = 'Drop zone {$a}';
$string['ytop'] = 'Top';
