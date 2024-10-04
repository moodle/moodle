@enrol @enrol_guest
Feature: Guest users can auto-enrol themself in courses where guest access is allowed
  In order to access courses contents
  As a guest
  I need to access courses as a guest

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activity" exists:
      | activity                      | forum                  |
      | course                        | C1                     |
      | idnumber                      | 0001                   |
      | name                          | Test forum name        |
    And I am on the "Course 1" "enrolment methods" page logged in as teacher1

  Scenario: Allow guest access without password
    Given I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save changes"
    When I am on the "Test forum name" "forum activity" page logged in as student1
    Then I should not see "Subscribe to this forum"

  @javascript
  Scenario: Allow guest access with password
    Given I click on "Edit" "link" in the "Guest access" "table_row"
    And I set the following fields to these values:
      | Allow guest access | Yes |
      | Password | moodle_rules |
    And I press "Save changes"
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Guest access"
    And I press "Access as a guest"
    And I set the following fields to these values:
      | Password | moodle_rules |
    And I click on "Access as a guest" "button" in the "Guest access" "dialogue"
    And I should see "Test forum name"
