@mod @mod_lesson
Feature: Set end of lesson reached as a completion condition for a lesson
  In order to ensure students really see all lesson pages
  As a teacher
  I need to set end of lesson reached to mark the lesson activity as completed

  Scenario: Set end reached as a condition
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson |
      | Description | Test lesson description |
      | Completion tracking | Show activity as complete when conditions are met |
      | completionendreached | 1 |
    And I follow "Test lesson"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I select "Add a content page" from the "qtype" singleselect
    And I set the following fields to these values:
      | Page title | Second page name |
      | Page contents | Second page contents |
      | id_answer_editor_0 | Previous page |
      | id_jumpto_0 | Previous page |
      | id_answer_editor_1 | Next page |
      | id_jumpto_1 | Next page |
    And I press "Save page"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    Then the "Test lesson" "lesson" activity with "auto" completion should be marked as not complete
    And I follow "Test lesson"
    And I press "Next page"
    And I follow "Course 1"
    Then the "Test lesson" "lesson" activity with "auto" completion should be marked as not complete
    And I follow "Course 1"
    And I follow "Test lesson"
    And I should see "You have seen more than one page of this lesson already."
    And I should see "Do you want to start at the last page you saw?"
    And I click on "No" "link" in the "#page-content" "css_element"
    And I press "Next page"
    And I press "Next page"
    And I follow "Course 1"
    Then the "Test lesson" "lesson" activity with "auto" completion should be marked as complete
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And "Student 1" user has completed "Test lesson" activity
