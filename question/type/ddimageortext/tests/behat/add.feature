@qtype @qtype_ddimageortext
Feature: Test creating a drag and drop onto image question
  As a teacher
  In order to test my students
  I need to be able to create drag and drop onto image questions

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher  | C1     | editingteacher |

  @javascript @_file_upload
  Scenario: Create a drag and drop onto image question
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Drag and drop onto image" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "Drag and drop onto image 001"
    And I set the field "Question text" to "Identify the features in this cross-section."
    And I set the field "General feedback" to "The locations are now labelled on the diagram below."
    And I upload "question/type/ddimageortext/tests/fixtures/oceanfloorbase.jpg" file to "Background image" filemanager

    # Draggable items
    And I follow "Draggable items"
    And I press "Blanks for 3 more draggable items"

    And I set the field "id_shuffleanswers" to "1"
    And I set the field "id_drags_0_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_0" to "island<br/>arc"

    And I set the field "id_drags_1_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_1" to "mid-ocean<br/>ridge"

    And I set the field "id_drags_2_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_2" to "abyssal<br/>plain"

    And I set the field "id_drags_3_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_3" to "continental<br/>rise"

    And I set the field "id_drags_4_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_4" to "ocean<br/>trench"

    And I set the field "id_drags_5_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_5" to "continental<br/>slope"

    And I set the field "id_drags_6_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_6" to "mountain<br/>belt"

    And I set the field "id_drags_7_dragitemtype" to "Draggable text"
    And I set the field "id_draglabel_7" to "continental<br/>shelf"

    # Drop zones
    And I follow "Drop zones"
    And I set the field "id_dropzonevisibility" to "1"
    And I press "Blanks for 3 more drop zones"

    And I set the field "id_drops_0_xleft" to "53"
    And I set the field "id_drops_0_ytop" to "17"
    And I set the field "id_drops_0_choice" to "7"

    And I set the field "id_drops_1_xleft" to "172"
    And I set the field "id_drops_1_ytop" to "2"
    And I set the field "id_drops_1_choice" to "8"

    And I set the field "id_drops_2_xleft" to "363"
    And I set the field "id_drops_2_ytop" to "31"
    And I set the field "id_drops_2_choice" to "5"

    And I set the field "id_drops_3_xleft" to "440"
    And I set the field "id_drops_3_ytop" to "13"
    And I set the field "id_drops_3_choice" to "3"

    And I set the field "id_drops_4_xleft" to "115"
    And I set the field "id_drops_4_ytop" to "74"
    And I set the field "id_drops_4_choice" to "6"

    And I set the field "id_drops_5_xleft" to "210"
    And I set the field "id_drops_5_ytop" to "94"
    And I set the field "id_drops_5_choice" to "4"

    And I set the field "id_drops_6_xleft" to "310"
    And I set the field "id_drops_6_ytop" to "87"
    And I set the field "id_drops_6_choice" to "1"

    And I set the field "id_drops_7_xleft" to "479"
    And I set the field "id_drops_7_ytop" to "84"
    And I set the field "id_drops_7_choice" to "2"

    And I press "id_submitbutton"
    Then I should see "Drag and drop onto image 001"
    # Checking that the next new question form displays user preferences settings.
    And I press "Create a new question ..."
    And I set the field "item_qtype_ddimageortext" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And the following fields match these values:
      | id_shuffleanswers     | 1 |
      | id_dropzonevisibility | 1 |

  @javascript @_file_upload
  Scenario: Question must have at least one drag item and one drop zone
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    And I press "Create a new question ..."
    And I set the field "Drag and drop onto image" to "1"
    And I click on "Add" "button" in the "Choose a question type to add" "dialogue"
    And I set the field "Question name" to "Test question"
    And I set the field "Question text" to "Identify the features in this cross-section."
    And I upload "question/type/ddimageortext/tests/fixtures/oceanfloorbase.jpg" file to "Background image" filemanager
    And I press "Save changes"
    Then I should see "You must add at least one draggable item to this question."
    And I should see "You must define at least one drop zone for this question."
