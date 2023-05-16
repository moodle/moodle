@core @core_group
Feature: Bulk update group messaging status
  In order to update group messaging settings in bulk
  As a teacher
  I need to be able to select the groups and update their messaging settings using the buttons provided.

  Background:
    Given the following "courses" exist:
        | fullname | shortname | format |
        | Course 1 | C1        | topics |
    And the following "users" exist:
        | username | firstname | lastname | email                |
        | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
        | user     | course | role           |
        | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    And I set the following fields to these values:
        | Group name      | Group-A-Test |
        | Group ID number | Group-A-Test |
    And I press "Save changes"
    And I press "Create group"
    And I set the following fields to these values:
        | Group name      | Group-B-Test |
        | Group ID number | Group-B-Test |
    And I press "Save changes"
    And I select "Groups" from the "jump" singleselect

  @javascript
  Scenario: Bulk enable messaging in groups
    Given I set the field "groups" to "Group-A-Test (0)"
    And I press "Edit group settings"
    And I set the field "id_enablemessaging" to "0"
    And I press "Save changes"
    And I wait until the page is ready
    And the field "groups" matches value "Group-A-Test (0)"
    And I press "Enable messaging"
    And I wait until the page is ready
    And I should see "Successfully enabled messaging in 1 group(s)"
    And I set the field "groups" to "Group-A-Test (0)"
    And I press "Edit group settings"
    Then the field "id_enablemessaging" matches value "1"

  @javascript
  Scenario: Bulk disable messaging in groups
    Given I set the field "groups" to "Group-A-Test (0)"
    And I press "Edit group settings"
    And I set the field "id_enablemessaging" to "1"
    And I press "Save changes"
    And I wait until the page is ready
    And the field "groups" matches value "Group-A-Test (0)"
    And I press "Disable messaging"
    And I wait until the page is ready
    And I should see "Successfully disabled messaging in 1 group(s)"
    And I set the field "groups" to "Group-A-Test (0)"
    And I press "Edit group settings"
    Then the field "id_enablemessaging" matches value "0"

  @javascript
  Scenario: Messaging buttons are enabled when a group is selected
    Given I set the field "groups" to "Group-A-Test (0)"
    Then the field "groups" matches value "Group-A-Test (0)"
    And the "Enable messaging" "button" should be enabled
    And the "Disable messaging" "button" should be enabled
