@enrol @enrol_guest
Feature: Guest users can auto-enrol themself in courses where guest access is allowed
  In order to access courses contents
  As a guest
  I need to access courses as a guest

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name | Test forum name |
      | Description | Test forum description |
    And I follow "Edit settings"

  @javascript
  Scenario: Allow guest access without password
    Given I fill the moodle form with:
      | Allow guest access | Yes |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    When I follow "Test forum name"
    Then I should not see "Subscribe to this forum"

  @javascript
  Scenario: Allow guest access with password
    Given I fill the moodle form with:
      | Allow guest access | Yes |
      | Password | moodle_rules |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    When I follow "Course 1"
    Then I should see "Guest access"
    And I fill the moodle form with:
      | Password | moodle_rules |
    And I press "Submit"
    And I should see "Test forum name"
