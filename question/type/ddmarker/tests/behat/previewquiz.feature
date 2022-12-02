@qtype @qtype_ddmarker
Feature: Preview a quiz with multiple maker question.
  As a teacher
  In order to check my drag-drop marker questions will work for students
  I need to preview them in quiz with multiple questions.

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype    | name           | template |
      | Test questions   | ddmarker | Drag markers   | mkmap    |
      | Test questions   | ddmarker | Drag markers 2 | mkmap    |
    And the following "activities" exist:
      | activity | name      | course | idnumber |
      | quiz     | Test quiz | C1     | quiz1    |
    And quiz "Test quiz" contains the following questions:
      | Drag markers   | 1 |
      | Drag markers 2 | 2 |

  @javascript
  Scenario: Preview a quiz with multiple markers question
    Given I am on the "Test quiz" "mod_quiz > View" page logged in as "admin"
    And I press "Preview quiz"
    # Add change window size so we can drag-drop OU marker on 322,213 coordinates on firefox.
    And I change viewport size to "large"
    # Drag items and go back and forth between the question.
    And I drag "OU" to "322,213" in the drag and drop markers question
    And I drag "Railway station" to "144,84" in the drag and drop markers question
    And I drag "Railway station" to "195,180" in the drag and drop markers question
    And I press "Next page"
    And I drag "OU" to "322,213" in the drag and drop markers question
    And I drag "Railway station" to "144,84" in the drag and drop markers question
    And I drag "Railway station" to "195,180" in the drag and drop markers question
    And I press "Previous page"
    And I drag "Railway station" to "267,302" in the drag and drop markers question
    And I press "Next page"
    And I drag "Railway station" to "267,302" in the drag and drop markers question
    And I press "Previous page"
    And I press "Next page"
    And I press "Finish attempt ..."
    And I press "Submit all and finish"
    When I click on "Submit all and finish" "button" in the "Confirmation" "dialogue"
    Then I should see "2.00/2.00"
    And the state of "Please place the markers on the map of Milton Keynes and be aware that" question is shown as "Correct"
    And I should see "Well done!"
