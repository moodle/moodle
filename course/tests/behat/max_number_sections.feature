@core @core_course
Feature: The maximum number of weeks/topics in a course can be configured
  In order to set boundaries to courses size
  As a manager
  I need to limit the number of weeks/topics a course can have

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
    And the following "system role assigns" exist:
      | user | course | role |
      | manager1 | Acceptance test site | manager |
    And I log in as "admin"
    And I navigate to "Courses > Default settings > Course default settings" in site administration

  @javascript
  Scenario: The number of sections can be increased and the limits are applied to courses
    Given I set the field "Maximum number of sections" to "100"
    When I press "Save changes"
    And the field "Maximum number of sections" matches value "100"
    And the "Number of sections" select box should contain "100"
    And I log out
    And I log in as "manager1"
    And the following "course" exists:
      | fullname     | New course fullname  |
      | shortname    | New course shortname |
      | format       | topics               |
      | numsections  | 90                   |
      | initsections | 1                    |
    And I am on the "New course fullname" course page
    Then I should see "Section 90"

  @javascript
  Scenario: The number of sections can be reduced to 0 and the limits are applied to courses
    Given I set the field "Maximum number of sections" to "0"
    When I press "Save changes"
    And the field "Maximum number of sections" matches value "0"
    And the "Number of sections" select box should contain "0"
    And the "Number of sections" select box should not contain "52"
    And I log out
    And I log in as "manager1"
    And the following "course" exists:
      | fullname     | New course fullname  |
      | shortname    | New course shortname |
      | format       | topics               |
      | numsections  | 0                    |
      | initsections | 1                    |
    And I am on the "New course fullname" course page
    Then I should not see "Section 1"
