@mod @mod_lesson
Feature: Set time spent as a completion condition for a lesson
  In order to ensure students spend the needed time to study lessons
  As a teacher
  I need to set time spent to mark the lesson activity as completed

  @javascript
  Scenario: Set time spent as a condition
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
    And the following config values are set as admin:
      | enablecompletion | 1 |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Enable completion tracking | Yes |
    And I press "Save and display"
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson |
      | Description | Test lesson description |
      | Completion tracking | Show activity as complete when conditions are met |
      | completiontimespentenabled | 1 |
      | completiontimespent[timeunit] | 60 |
      | completiontimespent[number] | 1 |
    And I follow "Test lesson"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | id_answer_editor_0 | Next page |
      | id_jumpto_0 | Next page |
    And I press "Save page"
    And I set the field "qtype" to "Add a content page"
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
    Then I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_lesson ')]/descendant::img[@alt='Not completed: Test lesson']" "xpath_element"
    And I follow "Test lesson"
    And I press "Next page"
    And I press "Next page"
    And I should see "You completed this lesson in"
    And I should see ", which is less than the required time of 1 min. You might need to attempt the lesson again."
    And I follow "Course 1"
    And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_lesson ')]/descendant::img[@alt='Not completed: Test lesson']" "xpath_element"
    And I follow "Course 1"
    And I follow "Test lesson"
    And I press "Next page"
    And I wait "61" seconds
    And I press "Next page"
    And I should not see "You might need to attempt the lesson again."
    And I follow "Course 1"
    And I hover "//li[contains(concat(' ', normalize-space(@class), ' '), ' modtype_lesson ')]/descendant::img[@alt='Completed: Test lesson']" "xpath_element"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And "Student 1" user has completed "Test lesson" activity
