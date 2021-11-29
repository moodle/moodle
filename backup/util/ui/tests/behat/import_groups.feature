@core @core_backup
Feature: Option to include groups and groupings when importing a course to another course
  In order to import a course to another course with groups and groupings
  As a teacher
  I need an option to include groups and groupings when importing a course to another course

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1 |
      | Course 2 | C2 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And the following "groups" exist:
      | name | description | course | idnumber |
      | Group 1 | Group description | C1 | GROUP1 |
      | Group 2 | Group description | C1 | GROUP2 |
    And the following "groupings" exist:
      | name | course | idnumber |
      | Grouping 1 | C1 | GROUPING1 |
      | Grouping 2 | C1 | GROUPING2 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage

  Scenario: Include groups and groupings when importing a course to another course
    Given I import "Course 1" course into "Course 2" course using this options:
      | Initial | Include groups and groupings | 1 |
    When I am on the "Course 2" "groups" page
    Then I should see "Group 1"
    And I should see "Group 2"
    And I select "Groupings" from the "jump" singleselect
    And I should see "Grouping 1"
    And I should see "Grouping 2"

  Scenario: Do not include groups and groupings when importing a course to another course
    Given I import "Course 1" course into "Course 2" course using this options:
      | Initial | Include groups and groupings | 0 |
    When I am on the "Course 2" "groups" page
    Then I should not see "Group 1"
    And I should not see "Group 2"
    And I select "Groupings" from the "jump" singleselect
    And I should not see "Grouping 1"
    And I should not see "Grouping 2"
