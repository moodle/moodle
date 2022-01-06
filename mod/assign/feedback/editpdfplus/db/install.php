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
 * Install code for the feedback_editpdfplus module.
 *
 * @package   assignfeedback_editpdfplus
 * @copyright 2017 Université de Lausanne
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

/**
 * EditPDFplus install code
 */
function xmldb_assignfeedback_editpdfplus_install() {
    global $CFG, $DB;

    //init DB with data example
    //axis
    $axis1 = new assignfeedback_editpdfplus\bdd\axis();
    $axis1->contextid = 1;
    $axis1->label = "Axis 1 : grammar / syntax";
    $axis1->order_axis = 1;
    $axis2 = new assignfeedback_editpdfplus\bdd\axis();
    $axis2->contextid = 1;
    $axis2->label = "Axis 2 : contents";
    $axis2->order_axis = 2;
    $axis3 = new assignfeedback_editpdfplus\bdd\axis();
    $axis3->contextid = 1;
    $axis3->label = "Axis 3 : others";
    $axis3->order_axis = 3;
    $axis = array($axis1, $axis2, $axis3);
    $DB->insert_records('assignfeedback_editpp_axis', $axis);
    //tool type
    $tytool1 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool1->cartridge_color = "#FF6F40";
    $tytool1->cartridge_x = 0;
    $tytool1->cartridge_y = -24;
    $tytool1->color = "#FFFF40";
    $tytool1->contextid = 1;
    $tytool1->label = "highlightplus";
    $tytool1b = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool1b->cartridge_color = null;
    $tytool1b->cartridge_x = null;
    $tytool1b->cartridge_y = null;
    $tytool1b->color = "red";
    $tytool1b->contextid = 1;
    $tytool1b->label = "lineplus";
    $tytool1b->configurable = 0;
    $tytool2 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool2->cartridge_color = null;
    $tytool2->cartridge_x = null;
    $tytool2->cartridge_y = null;
    $tytool2->color = "red";
    $tytool2->contextid = 1;
    $tytool2->label = "stampplus";
    $tytool2->configurable_cartridge = 0;
    $tytool2->configurable_cartridge_color = 0;
    $tytool2->configurable_texts = 0;
    $tytool2->configurable_question = 0;
    $tytool3 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool3->cartridge_color = null;
    $tytool3->cartridge_x = 5;
    $tytool3->cartridge_y = -8;
    $tytool3->color = "#FF0000";
    $tytool3->contextid = 1;
    $tytool3->label = "frame";
    $tytool3->configurable_cartridge_color = 0;
    $tytool3->configurable_color = 0;
    $tytool4 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool4->cartridge_color = "#0000FF";
    $tytool4->cartridge_x = 5;
    $tytool4->cartridge_y = 0;
    $tytool4->color = "#0000FF";
    $tytool4->contextid = 1;
    $tytool4->label = "verticalline";
    $tytool5 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool5->cartridge_color = "#000099";
    $tytool5->cartridge_x = 35;
    $tytool5->cartridge_y = -4;
    $tytool5->color = "#000099";
    $tytool5->contextid = 1;
    $tytool5->label = "stampcomment";
    $tytool5->configurable_color = 0;
    $tytool6 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool6->cartridge_color = "#000000";
    $tytool6->cartridge_x = null;
    $tytool6->cartridge_y = null;
    $tytool6->color = null;
    $tytool6->contextid = 1;
    $tytool6->label = "commentplus";
    $tytool6->configurable_texts = 0;
    $tytool6->configurable_color = 0;
    $tytool7 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool7->cartridge_color = null;
    $tytool7->cartridge_x = null;
    $tytool7->cartridge_y = null;
    $tytool7->color = null;
    $tytool7->contextid = 1;
    $tytool7->label = "pen";
    $tytool7->configurable = 0;
    $tytool8 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool8->cartridge_color = null;
    $tytool8->cartridge_x = null;
    $tytool8->cartridge_y = null;
    $tytool8->color = null;
    $tytool8->contextid = 1;
    $tytool8->label = "line";
    $tytool8->configurable = 0;
    $tytool9 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool9->cartridge_color = null;
    $tytool9->cartridge_x = null;
    $tytool9->cartridge_y = null;
    $tytool9->color = null;
    $tytool9->contextid = 1;
    $tytool9->label = "rectangle";
    $tytool9->configurable = 0;
    $tytool10 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool10->cartridge_color = null;
    $tytool10->cartridge_x = null;
    $tytool10->cartridge_y = null;
    $tytool10->color = null;
    $tytool10->contextid = 1;
    $tytool10->label = "oval";
    $tytool10->configurable = 0;
    $tytool11 = new assignfeedback_editpdfplus\bdd\type_tool();
    $tytool11->cartridge_color = null;
    $tytool11->cartridge_x = null;
    $tytool11->cartridge_y = null;
    $tytool11->color = null;
    $tytool11->contextid = 1;
    $tytool11->label = "highlight";
    $tytool11->configurable = 0;
    $tytools = array($tytool1, $tytool1b, $tytool2, $tytool3, $tytool4, $tytool5, $tytool6, $tytool7, $tytool8, $tytool9, $tytool10, $tytool11);
    $DB->insert_records('assignfeedback_editpp_typet', $tytools);
    //tools
    $tool1 = new assignfeedback_editpdfplus\bdd\tool();
    $tool1->axis = 0;
    $tool1->cartridge = null;
    $tool1->cartridge_color = null;
    $tool1->colors = null;
    $tool1->contextid = 1;
    $tool1->enabled = 1;
    $tool1->label = "pen";
    $tool1->order_tool = null;
    $tool1->reply = 0;
    $tool1->texts = null;
    $tool1->type = 8;
    $tool2 = new assignfeedback_editpdfplus\bdd\tool();
    $tool2->axis = 0;
    $tool2->cartridge = null;
    $tool2->cartridge_color = null;
    $tool2->colors = null;
    $tool2->contextid = 1;
    $tool2->enabled = 1;
    $tool2->label = "line";
    $tool2->order_tool = null;
    $tool2->reply = 0;
    $tool2->texts = null;
    $tool2->type = 9;
    $tool3 = new assignfeedback_editpdfplus\bdd\tool();
    $tool3->axis = 0;
    $tool3->cartridge = null;
    $tool3->cartridge_color = null;
    $tool3->colors = null;
    $tool3->contextid = 1;
    $tool3->enabled = 1;
    $tool3->label = "rectangle";
    $tool3->order_tool = null;
    $tool3->reply = 0;
    $tool3->texts = null;
    $tool3->type = 10;
    $tool4 = new assignfeedback_editpdfplus\bdd\tool();
    $tool4->axis = 0;
    $tool4->cartridge = null;
    $tool4->cartridge_color = null;
    $tool4->colors = null;
    $tool4->contextid = 1;
    $tool4->enabled = 1;
    $tool4->label = "oval";
    $tool4->order_tool = null;
    $tool4->reply = 0;
    $tool4->texts = null;
    $tool4->type = 11;
    $tool5 = new assignfeedback_editpdfplus\bdd\tool();
    $tool5->axis = 0;
    $tool5->cartridge = null;
    $tool5->cartridge_color = null;
    $tool5->colors = null;
    $tool5->contextid = 1;
    $tool5->enabled = 1;
    $tool5->label = "highlight";
    $tool5->order_tool = null;
    $tool5->reply = 0;
    $tool5->texts = null;
    $tool5->type = 12;
    $tool6 = new assignfeedback_editpdfplus\bdd\tool();
    $tool6->axis = 1;
    $tool6->cartridge = "Axis1";
    $tool6->cartridge_color = null;
    $tool6->colors = null;
    $tool6->contextid = 1;
    $tool6->enabled = 1;
    $tool6->label = "COMMENT";
    $tool6->order_tool = 1;
    $tool6->reply = 1;
    $tool6->texts = null;
    $tool6->type = 7;
    $tool7 = new assignfeedback_editpdfplus\bdd\tool();
    $tool7->axis = 1;
    $tool7->cartridge = "LEX";
    $tool7->cartridge_color = null;
    $tool7->colors = null;
    $tool7->contextid = 1;
    $tool7->enabled = 1;
    $tool7->label = "LEXIQUE";
    $tool7->order_tool = 2;
    $tool7->reply = 1;
    $tool7->texts = '"wrong meaning","bad word"';
    $tool7->type = 1;
    $tool8 = new assignfeedback_editpdfplus\bdd\tool();
    $tool8->axis = 1;
    $tool8->cartridge = "REP";
    $tool8->cartridge_color = null;
    $tool8->colors = null;
    $tool8->contextid = 1;
    $tool8->enabled = 1;
    $tool8->label = "REPETION";
    $tool8->order_tool = 3;
    $tool8->reply = 1;
    $tool8->texts = '"repetition","duplication"';
    $tool8->type = 4;
    $tool9 = new assignfeedback_editpdfplus\bdd\tool();
    $tool9->axis = 1;
    $tool9->cartridge = null;
    $tool9->cartridge_color = null;
    $tool9->colors = "blue";
    $tool9->contextid = 1;
    $tool9->enabled = 1;
    $tool9->label = "PONCTUATION";
    $tool9->order_tool = 4;
    $tool9->reply = 0;
    $tool9->texts = null;
    $tool9->type = 3;
    $tool10 = new assignfeedback_editpdfplus\bdd\tool();
    $tool10->axis = 2;
    $tool10->cartridge = "Axis2";
    $tool10->cartridge_color = null;
    $tool10->colors = null;
    $tool10->contextid = 1;
    $tool10->enabled = 1;
    $tool10->label = "COMMENT";
    $tool10->order_tool = 1;
    $tool10->reply = 1;
    $tool10->texts = null;
    $tool10->type = 7;
    $tool11 = new assignfeedback_editpdfplus\bdd\tool();
    $tool11->axis = 2;
    $tool11->cartridge = "LI";
    $tool11->cartridge_color = null;
    $tool11->colors = null;
    $tool11->contextid = 1;
    $tool11->enabled = 1;
    $tool11->label = "LINK";
    $tool11->order_tool = 2;
    $tool11->reply = 1;
    $tool11->texts = '"Connection","Correlation","Relation between these 2 ideas"';
    $tool11->type = 6;
    $tool12 = new assignfeedback_editpdfplus\bdd\tool();
    $tool12->axis = 3;
    $tool12->cartridge = "Useless";
    $tool12->cartridge_color = "#0000FF";
    $tool12->colors = "#0000FF";
    $tool12->contextid = 1;
    $tool12->enabled = 1;
    $tool12->label = "Useless";
    $tool12->order_tool = 1;
    $tool12->reply = 0;
    $tool12->texts = null;
    $tool12->type = 1;
    $tool13 = new assignfeedback_editpdfplus\bdd\tool();
    $tool13->axis = 3;
    $tool13->cartridge = null;
    $tool13->cartridge_color = null;
    $tool13->colors = "green";
    $tool13->contextid = 1;
    $tool13->enabled = 1;
    $tool13->label = "✔";
    $tool13->order_tool = 2;
    $tool13->reply = 0;
    $tool13->texts = null;
    $tool13->type = 3;
    $tool14 = new assignfeedback_editpdfplus\bdd\tool();
    $tool14->axis = 3;
    $tool14->cartridge = "Formatting";
    $tool14->cartridge_color = "#FF6F40";
    $tool14->colors = "#FF6F40";
    $tool14->contextid = 1;
    $tool14->enabled = 1;
    $tool14->label = "FORM";
    $tool14->order_tool = 3;
    $tool14->reply = 1;
    $tool14->texts = '"identation","order"';
    $tool14->type = 5;
    $tools = array($tool1, $tool2, $tool3, $tool4, $tool5, $tool6, $tool7, $tool8, $tool9, $tool10, $tool11, $tool12, $tool13, $tool14);
    $DB->insert_records('assignfeedback_editpp_tool', $tools);
}
