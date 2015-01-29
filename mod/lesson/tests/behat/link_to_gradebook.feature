@mod @mod_lesson
Feature: link to gradebook on the end of lesson page
  In order to allow students to see their lesson grades
  As a teacher
  I need to provide a link to gradebook on the end of lesson page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
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
    And I add a "Lesson" to section "1" and I fill the form with:
      | Name | Test lesson |
      | Description | Test lesson description |
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
  @javascript
  Scenario: Link to gradebook for non practice lesson
    Given I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson"
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should see "View grades"
    And I follow "View grades"
    And I should see "User report - Student 1"
    And I should see "Test lesson"

  @javascript
  Scenario: No link to gradebook for non graded lesson
    Given I follow "Test lesson"
    And I navigate to "Edit settings" node in "Lesson administration"
    And I set the following fields to these values:
        | Type | None |
    And I press "Save and display"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson"
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

  @javascript
  Scenario: No link to gradebook for practice lesson
    Given I follow "Test lesson"
    And I navigate to "Edit settings" node in "Lesson administration"
    And I set the following fields to these values:
        | Practice lesson | Yes |
    And I press "Save and display"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson"
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

  @javascript
  Scenario: No link if Show gradebook to student disabled
    Given I follow "Course 1"
    And I click on "Edit settings" "link" in the "Administration" "block"
    And I set the following fields to these values:
      | Show gradebook to students | No |
    And I press "Save changes"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson"
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"

  @javascript
  Scenario: No link to gradebook if no gradereport/user:view capability
    Given I log out
    And I log in as "admin"
    And I set the following system permissions of "Student" role:
      | capability | permission |
      | gradereport/user:view | Prevent |
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Test lesson"
    And I press "Next page"
    And I press "Next page"
    Then I should see "Congratulations - end of lesson reached"
    And I should not see "View grades"
