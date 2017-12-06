@qtype @qtype_ddmarker
Feature: Test creating a drag and drop markers question
  As a teacher
  In order to test my students
  I need to be able to create drag and drop markers questions

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email               |
      | teacher1 | T1        | Teacher1 | teacher1@moodle.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" node in "Course administration"

  @javascript @_file_upload
  Scenario: Create a drag and drop markers question
    When I press "Create a new question ..."
    And I set the field "Drag and drop markers" to "1"
    And I press "Add"
    And I set the field "Question name" to "Drag and drop markers"
    And I set the field "Question text" to "Please place the markers on the map of Milton Keynes and be aware that there is more than one railway station."
    And I set the field "General feedback" to "The Open University is at the junction of Brickhill Street and Groveway. There are three railway stations, Wolverton, Milton Keynes Central and Bletchley."
    And I upload "question/type/ddmarker/tests/fixtures/mkmap.png" file to "Background image" filemanager

    # Markers.
    And I follow "Markers"
    And I set the field "id_drags_0_label" to "OU"
    And I set the field "id_drags_0_noofdrags" to "1"
    And I set the field "id_drags_1_label" to "Railway station"
    And I set the field "id_drags_1_noofdrags" to "3"

    # Drop zones.
    And I follow "Drop zones"
    And I set the field "id_drops_0_shape" to "Circle"
    And I set the field "id_drops_0_coords" to "322,213;10"
    And I set the field "id_drops_0_choice" to "1"
    And I set the field "id_drops_1_shape" to "Circle"
    And I set the field "id_drops_1_coords" to "144,84;10"
    And I set the field "id_drops_1_choice" to "2"
    And I set the field "id_drops_2_shape" to "Circle"
    And I set the field "id_drops_2_coords" to "195,180;10"
    And I set the field "id_drops_2_choice" to "2"
    And I set the field "id_drops_3_shape" to "Circle"
    And I set the field "id_drops_3_coords" to "267,302;10"

    # Try to submit without setting the last marker.
    And I press "id_submitbutton"
    Then I should see "You have specified a drop zone but not chosen a marker that must be dragged to the zone."

    # Set the last marker and submit again.
    And I set the field "id_drops_3_choice" to "2"
    And I press "id_submitbutton"
    And I should see "Drag and drop markers"
