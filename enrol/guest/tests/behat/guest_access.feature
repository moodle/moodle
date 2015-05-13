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
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I click on "Edit settings" "link" in the "Administration" "block"

  Scenario: Allow guest access without password
    Given I set the following fields to these values:
      | Allow guest access | Yes |
    And I press "Save and display"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"
    When I follow "Test forum name"
    Then I should not see "Subscribe to this forum"

  Scenario: Allow guest access with password
    Given I set the following fields to these values:
      | Allow guest access | Yes |
      | Password | moodle_rules |
    And I press "Save and display"
    And I log out
    And I log in as "student1"
    And I am on site homepage
    When I follow "Course 1"
    Then I should see "Guest access"
    And I set the following fields to these values:
      | Password | moodle_rules |
    And I press "Submit"
    And I should see "Test forum name"
