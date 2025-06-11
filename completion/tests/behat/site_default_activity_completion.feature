@core @core_completion
Feature: Allow admins to edit the default activity completion rules at site level.
  In order to set the activity completion defaults for new activities
  As an admin
  I need to be able to edit the completion rules for a group of activities at site level.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And I log in as "admin"

  @javascript
  Scenario: Default activity completion rules with no site or course default completion
    Given the following "activity" exists:
      | activity   | assign               |
      | course     | C1                   |
      | name       | Test assignment one  |
      | completion | 1                    |
    When I add a assign activity to course "Course 1" section "0"
    And I expand all fieldsets
    # Completion tracking 0 = Do not indicate activity completion.
    Then the field "None" matches value "1"
    # Default values don't affect existing activities.
    But I am on the "Test assignment one" "assign activity editing" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "Students must manually mark the activity as done" matches value "1"
    And the field "None" matches value "0"

  @javascript
  Scenario: Default activity completion rules with site default completion but with no course default completion
    Given the following "activity" exists:
      | activity   | assign               |
      | course     | C1                   |
      | name       | Test assignment one  |
      | completion | 0                    |
    And the following "core_completion > Course default" exist:
      | course               | module | completion | completionview | completionusegrade | completionsubmit |
      | Acceptance test site | assign | 2          | 0              | 1                  | 1                |
    When I add a assign activity to course "Course 1" section "0"
    And I expand all fieldsets
    Then the field "Add requirements" matches value "1"
    And the field "completionview" matches value "0"
    And the field "completionusegrade" matches value "1"
    And the field "completionsubmit" matches value "1"
    # Default values don't affect existing activities.
    But I am on the "Test assignment one" "assign activity editing" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "Add requirements" matches value "0"
    And the field "None" matches value "1"

  @javascript
  Scenario: Default activity completion rules with site default completion and course default completion
    Given the following "activity" exists:
      | activity   | assign               |
      | course     | C1                   |
      | name       | Test assignment one  |
      | completion | 0                    |
    And the following "core_completion > Course defaults" exist:
      | course               | module | completion | completionview | completionusegrade | completionsubmit |
      | Acceptance test site | assign | 2          | 0              | 1                  | 1                |
      | C1                   | assign | 2          | 1              | 0                  | 1                |
    When I add a assign activity to course "Course 1" section "0"
    And I expand all fieldsets
    Then the field "Add requirements" matches value "1"
    And the field "completionview" matches value "1"
    And the field "completionusegrade" matches value "0"
    And the field "completionsubmit" matches value "1"
    # Default values don't affect existing activities.
    But I am on the "Test assignment one" "assign activity editing" page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the field "Add requirements" matches value "0"
    And the field "None" matches value "1"

  Scenario: Navigate to site default activity completion
    Given I navigate to "Courses > Default settings > Default activity completion" in site administration
    When I should see "Default activity completion"
    Then I should see "These are the default completion conditions for activities in all courses."
    And the following config values are set as admin:
      | enablecompletion  | 0 |
    And I navigate to "Courses > Default settings" in site administration
    And I should not see "Default activity completion"
