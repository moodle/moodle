@core @core_user
Feature: Viewing the list of cohorts to enrol in a course
  In order to ensure we only display the cohorts when applicable
  As a teacher
  I should only see the list of cohorts under some circumstances

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname | email                 |
      | teacher1  | Teacher   | 1        | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |

  @javascript @skip_chrome_zerosize
  Scenario: Check the teacher does not see the cohorts field without the proper capabilities
    Given the following "cohort" exists:
      | name        | Test cohort name        |
      | idnumber    | 1337                    |
      | description | Test cohort description |
    And I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability           | permission |
      | moodle/cohort:manage | Prohibit |
      | moodle/cohort:view   | Prohibit |
    And I log out
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    When I press "Enrol users"
    Then I should not see "Select cohorts"
    And I should not see "Enrol selected users and cohorts"

  @javascript
  Scenario: Check we show the cohorts field if there are some present
    Given the following "cohort" exists:
      | name        | Test cohort name        |
      | idnumber    | 1337                    |
      | description | Test cohort description |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    When I press "Enrol users"
    Then I should see "Select cohorts"
    And I should see "Enrol selected users and cohorts"

  @javascript
  Scenario: Check we do not show the cohorts field if there are none present
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to course participants
    When I press "Enrol users"
    Then I should not see "Select cohorts"
    And I should not see "Enrol selected users and cohorts"
