@ou @ou_vle @qtype @qtype_ddmarker
Feature: Test all the basic functionality of this question type
  In order to evaluate students responses, As a teacher I need to
  create and preview ddmarkers (Drag and drop markers) questions.

    # Due to complexity and since the javascript code needs to be converted at some stage,
    # we are not going to test attempting this qtype. However, we will do all other
    # possible testings, such as creating the question preview it and seeing the
    # correct information on the preview string as well as backing-up and restoring
    # the course containing this qtype.

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

  @javascript
  Scenario: Create, edit then preview a ddmarker question.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"
    Then I should see "Question bank"

    # Create a new question
    # We have to set the fields individually, because of the step for uploading the background image.
    Given I press "Create a new question ..."
    And I set the field "Drag and drop markers" to "1"
    Then I press "Add"
    And I should see "Adding drag and drop markers"
    Then I set the field "Question name" to "Drag and drop markers 001"
    And I set the field "Question text" to "Please place the markers on the map of Milton Keynes and be aware that there is more than one railway station."
    And I set the field "General feedback" to "The Open University is at the junction of Brickhill Street and Groveway. There are three railway stations, Wolverton, Milton Keynes Central and Bletchley."
    Given I upload "question/type/ddmarker/tests/fixtures/mkmap.png" file to "Background image" filemanager

    # Markers
    Given I follow "Markers"

    And I set the field "id_drags_0_label" to "OU"
    And I set the field "id_drags_0_noofdrags" to "1"

    And I set the field "id_drags_1_label" to "Railway station"
    And I set the field "id_drags_1_noofdrags" to "3"

    # Drop zones
    Given I follow "Drop zones"
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

    # forget setting the last merker and press Save changes
    When I press "id_submitbutton"
    Then I should see "You have specified a drop zone but not chosen a marker that must be dragged to the zone"

    # Set the last marker and press Save changes again
    And I set the field "id_drops_3_choice" to "2"

    And I press "id_submitbutton"
    Then I should see "Drag and drop markers 001"

    # Preview it.
    When I click on "Preview" "link" in the "Drag and drop markers 001" "table_row"
    And I switch to "questionpreview" window
    Then I should see "Preview question: Drag and drop markers 001"
    And I switch to the main window

    # Backup the course and restore it.
    When I log out
    And I log in as "admin"
    When I backup "Course 1" course using this options:
      | Confirmation | Filename | test_backup.mbz |
    When I restore "test_backup.mbz" backup into a new course using this options:
      | Schema | Course name | Course 2 |
    Then I should see "Course 2"
    When I navigate to "Question bank" node in "Course administration"
    Then I should see "Drag and drop markers 001"

    # Edit the copy and verify the form field contents.
    When I click on "Edit" "link" in the "Drag and drop markers 001" "table_row"
    Then the following fields match these values:
      | Question name             | Drag and drop markers 001   |
      | Question text             | Please place the markers on the map of Milton Keynes and be aware that there is more than one railway station. |
      | General feedback          | The Open University is at the junction of Brickhill Street and Groveway. There are three railway stations, Wolverton, Milton Keynes Central and Bletchley. |

      # Drop zones
      | id_drops_0_shape   | Circle     |
      | id_drops_0_coords  | 322,213;10 |
      | id_drops_0_choice  | 1          |

      | id_drops_1_shape   | Circle     |
      | id_drops_1_coords  | 144,84;10  |
      | id_drops_1_choice  | 2          |

      | id_drops_2_shape   | Circle     |
      | id_drops_2_coords  | 195,180;10 |
      | id_drops_2_choice  | 2          |

      | id_drops_3_shape   | Circle     |
      | id_drops_3_coords  | 267,302;10 |
      | id_drops_3_choice  | 2          |

      # Markers
      | id_drags_0_label     | OU              |
      | id_drags_0_noofdrags | 1               |

      | id_drags_1_label     | Railway station |
      | id_drags_1_noofdrags | 3               |

    And I set the following fields to these values:
      | Question name | Edited question name |
    And I press "id_submitbutton"
    Then I should see "Edited question name"
