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
      | questioncategory | qtype    | name                     | template |
      | Test questions   | ddmarker | Drag markers             | mkmap    |
      | Test questions   | ddmarker | Drag to mathjax equation | mathjax  |

  @javascript @_bug_phantomjs
  Scenario: Preview a question using the mouse
    When I am on the "Drag markers" "core_question > preview" page logged in as teacher
    And I drag "OU" to "322,213" in the drag and drop markers question
    And I drag "Railway station" to "144,84" in the drag and drop markers question
    And I drag "Railway station" to "195,180" in the drag and drop markers question
    And I drag "Railway station" to "267,302" in the drag and drop markers question
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

  @javascript
  Scenario: Preview a question in multiple viewports
    When I am on the "Drag markers" "core_question > preview" page logged in as teacher
    And I change viewport size to "large"
    And I drag "OU" to "322,213" in the drag and drop markers question
    And I drag "Railway station" to "144,84" in the drag and drop markers question
    And I drag "Railway station" to "195,180" in the drag and drop markers question
    And I press "Save"
    And I change viewport size to "640x768"
    And I press "Save"
    And I drag "Railway station" to "267,302" in the drag and drop markers question
    And I press "Save"
    And I press "Submit and finish"
    Then the state of "Please place the markers on the map of Milton Keynes" question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"

  @javascript
  Scenario: Preview a drag-drop marker question with mathjax question.
    Given the "mathjaxloader" filter is "on"
    And the "mathjaxloader" filter applies to "content and headings"
    And I am on the "Drag to mathjax equation" "core_question > preview" page logged in as teacher
    When I press "Submit and finish"
    Then ".markertexts .markertext .MathJax_Display" "css_element" should exist in the ".droparea" "css_element"
    And I press "Start again"
    And I press "Fill in correct responses"
    And I press "Submit and finish"
    And ".filter_mathjaxloader_equation" "css_element" should exist in the ".droparea" "css_element"
    And ".markertexts .markertext .MathJax_Display" "css_element" should not exist in the ".droparea" "css_element"
