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
    And I am on the "Test lesson" "lesson activity" page logged in as teacher1
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | Description | The first one |
    And I press "Save page"
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
