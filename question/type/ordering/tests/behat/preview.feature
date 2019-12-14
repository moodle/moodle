@qtype @qtype_ordering
Feature: Preview an Ordering question
  As a teacher
  In order to check my Ordering questions will work for students
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
      | questioncategory | qtype    | name         | template | layouttype |
      | Test questions   | ordering | ordering-001 | moodle   | 0          |
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Question bank" in current page administration

  @javascript @_switch_window
  Scenario: Preview an Ordering question and submit a correct response.
    When I choose "Preview" action for "ordering-001" in the question bank
    And I switch to "questionpreview" window
    And I set the field "How questions behave" to "Immediate feedback"
    And I press "Start again with these options"
    # The test was unreliable unless if an item randomly started in the right place.
    # So we first moved each item to the last place, before putting it into the right place.
    And I drag "Modular" to space "6" in the ordering question
    And I drag "Modular" to space "1" in the ordering question
    And I drag "Object" to space "6" in the ordering question
    And I drag "Object" to space "2" in the ordering question
    And I drag "Oriented" to space "6" in the ordering question
    And I drag "Oriented" to space "3" in the ordering question
    And I drag "Dynamic" to space "6" in the ordering question
    And I drag "Dynamic" to space "4" in the ordering question
    And I drag "Learning" to space "6" in the ordering question
    And I drag "Learning" to space "5" in the ordering question
    And I press "Submit and finish"
    Then the state of "Put these words in order." question is shown as "Correct"
    And I should see "Mark 1.00 out of 1.00"
    And I switch to the main window
