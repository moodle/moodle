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
      | intro       | Test lesson description |
      | section     | 1                       |
      | usepassword | 1                       |
      | password    | moodle_rules            |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test lesson"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | Description | The first one |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test lesson"
    Then I should see "Test lesson is a password protected lesson"
    And I should not see "First page contents"
    And I set the field "userpassword" to "moodle"
    And I press "Continue"
    And I should see "Login failed, please try again..."
    And I should see "Test lesson is a password protected lesson"
    And I set the field "userpassword" to "moodle_rules"
    And I press "Continue"
    And I should see "First page contents"
