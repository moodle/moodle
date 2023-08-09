@core @core_group @core_customfield @javascript
Feature: Add and use group custom fields
  In order to store an extra information about groups
  As an admin
  I need to create group customs fields and be able to populate them on group creation

  Background:
    Given the following "custom field categories" exist:
      | name                   | component  | area     | itemid |
      | Category for group1    | core_group | group    | 0      |
      | Category for grouping1 | core_group | grouping | 0      |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario: Create a new group custom field and use the field for a new group
    When I log in as "admin"
    And I navigate to "Courses > Groups > Group custom fields" in site administration
    Then I should see "Category for group1"
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then the following should exist in the "generaltable" table:
      | Custom field | Short name | Type       |
      | Test field   | testfield  | Short text |
    Then I log in as "teacher1"
    And I am on the "Course 1" "groups" page
    And I press "Create group"
    Then I should see "Category for group1"
    And I should see "Test field"
    And I set the following fields to these values:
      | Group name  | My new group      |
      | Test field  | Custom field text |
    And I press "Save changes"
    Then the "groups" select box should contain "My new group (0)"
    And I set the field "groups" to "My new group (0)"
    And I press "Edit group settings"
    And the field "Test field" matches value "Custom field text"

  Scenario: Create a new grouping custom field and use the field for a new grouping
    When I log in as "admin"
    And I navigate to "Courses > Groups > Grouping custom fields" in site administration
    Then I should see "Category for grouping1"
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then the following should exist in the "generaltable" table:
      | Custom field | Short name | Type       |
      | Test field   | testfield  | Short text |
    Then I log in as "teacher1"
    And I am on the "Course 1" "groupings" page
    And I press "Create grouping"
    Then I should see "Category for grouping1"
    And I should see "Test field"
    And I set the following fields to these values:
      | Grouping name | My new grouping   |
      | Test field    | Custom field text |
    And I press "Save changes"
    Then I should see "My new grouping"
    And I click on "Edit" "link" in the "My new grouping" "table_row"
    And the field "Test field" matches value "Custom field text"
