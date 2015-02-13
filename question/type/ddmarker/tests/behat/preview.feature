@ou @ou_vle @qtype @qtype_ddmarker
Feature: Preview a drag-drop onto image question
  As a teacher
  In order to check my drag-drop onto image questions will work for students
  I need to preview them

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
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype    | name         | template |
      | Test questions   | ddmarker | Drag markers | mkmap    |
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Question bank" node in "Course administration"

  @javascript
  Scenario: Preview a question using the mouse.
    When I click on "Preview" "link" in the "Drag markers" "table_row"
    And I switch to "questionpreview" window
    # Odd, but the <br>s go to nothing, not a space.
    And I drag "OU" to "340,228" in the drag and drop markers question
    And I drag "Railway station" to "252,195" in the drag and drop markers question
    And I drag "Railway station,1" to "324,317" in the drag and drop markers question
    And I drag "Railway station,2" to "201,99" in the drag and drop markers question
    And I press "Submit and finish"
    Then the state of "Please place the markers on the map of Milton Keynes" question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"

  @javascript
  Scenario: Preview a question using the keyboard.
    When I click on "Preview" "link" in the "Drag markers" "table_row"
    And I switch to "questionpreview" window
    And I type "up" "89" times on marker "Railway station" in the drag and drop markers question
    And I type "right" "21" times on marker "Railway station" in the drag and drop markers question
    And I press "Submit and finish"
    Then the state of "Please place the markers on the map of Milton Keynes" question is shown as "Partially correct"
    And I should see "Mark 0.25 out of 1.00"
