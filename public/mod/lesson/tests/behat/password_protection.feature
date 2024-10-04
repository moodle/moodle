@mod @mod_lesson
Feature: A teacher can password protect a lesson
  In order to avoid undesired accesses to lesson activities
  As a teacher
  I need to set a password to access the lesson

  Scenario: Accessing as student to a protected lesson
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity    | lesson                  |
      | course      | C1                      |
      | idnumber    | 0001                    |
      | name        | Test lesson             |
      | usepassword | 1                       |
      | password    | moodle_rules            |
    Given the following "mod_lesson > page" exist:
      | lesson      | qtype   | title           | content             |
      | Test lesson | content | First page name | First page contents |
    And the following "mod_lesson > answer" exist:
      | page            | answer        | jumpto    |
      | First page name | The first one | Next page |
    When I am on the "Test lesson" "lesson activity" page logged in as student1
    Then I should see "Test lesson is a password protected lesson"
    And I should not see "First page contents"
    And I set the field "userpassword" to "moodle"
    And I press "Continue"
    And I should see "Login failed, please try again..."
    And I should see "Test lesson is a password protected lesson"
    And I set the field "userpassword" to "moodle_rules"
    And I press "Continue"
    And I should see "First page contents"
