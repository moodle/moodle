@qtype @qtype_ddimageortext @_switch_window
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
      | questioncategory | qtype         | name            | template |
      | Test questions   | ddimageortext | Drag onto image | xsection |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript @_bug_phantomjs
  Scenario: Preview a question using the mouse.
    When I choose "Preview" action for "Drag onto image" in the question bank
    And I switch to "questionpreview" window
    # Increase window size and wait 2 seconds to ensure elements are placed properly by js.
    # Keep window large else drag will scroll the window to find element.
    And I change window size to "medium"
    And I wait "2" seconds
    # Odd, but the <br>s go to nothing, not a space.
    And I drag "mountainbelt" to place "1" in the drag and drop onto image question
    And I drag "continentalshelf" to place "2" in the drag and drop onto image question
    And I drag "oceantrench" to place "3" in the drag and drop onto image question
    And I drag "abyssalplain" to place "4" in the drag and drop onto image question
    And I drag "continentalslope" to place "5" in the drag and drop onto image question
    And I drag "continentalrise" to place "6" in the drag and drop onto image question
    And I drag "islandarc" to place "7" in the drag and drop onto image question
    And I drag "mid-oceanridge" to place "8" in the drag and drop onto image question
    And I press "Submit and finish"
    Then the state of "Identify the features" question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"
    And I switch to the main window

  @javascript
  Scenario: Preview a question using the keyboard.
    When I choose "Preview" action for "Drag onto image" in the question bank
    And I switch to "questionpreview" window
    # Increase window size and wait 2 seconds to ensure elements are placed properly by js.
    And I change window size to "medium"
    And I wait "2" seconds
    And I type "       " on place "1" in the drag and drop onto image question
    And I type "       " on place "2" in the drag and drop onto image question
    And I type "     " on place "3" in the drag and drop onto image question
    And I type "   " on place "4" in the drag and drop onto image question
    And I type "    " on place "5" in the drag and drop onto image question
    And I type "   " on place "6" in the drag and drop onto image question
    And I type " " on place "7" in the drag and drop onto image question
    And I type " " on place "8" in the drag and drop onto image question
    And I press "Submit and finish"
    Then the state of "Identify the features" question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"
    And I switch to the main window
