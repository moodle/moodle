@qtype @qtype_ddmarker @_switch_window
Feature: Preview a drag-drop marker question
  As a teacher
  In order to check my drag-drop marker questions will work for students
  I need to preview them

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype    | name         | template |
      | Test questions   | ddmarker | Drag markers | mkmap    |

  @javascript @_bug_phantomjs
  Scenario: Preview a question using the mouse.
    When I am on the "Drag markers" "core_question > preview" page logged in as teacher
    # Increase window size and wait 2 seconds to ensure elements are placed properly by js.
    # Keep window large else drag will scroll the window to find element.
    And I change window size to "large"
    And I wait "2" seconds
    # Odd, but the <br>s go to nothing, not a space.
    And I drag "OU" to "345,230" in the drag and drop markers question
    And I drag "Railway station" to "262,197" in the drag and drop markers question
    And I drag "Railway station" to "334,319" in the drag and drop markers question
    And I drag "Railway station" to "211,101" in the drag and drop markers question
    And I press "Submit and finish"
    Then the state of "Please place the markers on the map of Milton Keynes" question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"

  @javascript
  Scenario: Preview a question using the keyboard
    When I am on the "Drag markers" "core_question > preview" page logged in as teacher
    And I type "up" "88" times on marker "Railway station" in the drag and drop markers question
    And I type "right" "26" times on marker "Railway station" in the drag and drop markers question
    And I press "Submit and finish"
    Then the state of "Please place the markers on the map of Milton Keynes" question is shown as "Partially correct"
    And I should see "Mark 0.25 out of 1.00"
