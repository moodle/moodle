@core @core_admin @core_reportbuilder
Feature: An administrator can filter user accounts by role, cohort and other profile fields
  In order to find the user accounts I am looking for
  As an admin
  I need to filter the users account list using different filter

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name           |
      | text     | frog      | Favourite frog |
      | text     | undead    | Type of undead |
    And the following "users" exist:
      | username | firstname | lastname | email | auth | confirmed | lastip | institution | department | profile_field_frog | profile_field_undead |
      | user1 | User | One | one@example.com | manual | 0 | 127.0.1.1       | moodle      | red        | Kermit             |                      |
      | user2 | User | Two | two@example.com | ldap | 1 | 0.0.0.0           | moodle      | blue       | Mr Toad            | Zombie               |
      | user3 | User | Three | three@example.com | manual | 1 | 0.0.0.0 |                 |            |                    |                      |
      | user4 | User | Four | four@example.com | ldap | 0 | 127.0.1.2 |                   |            |                    |                      |
    And the following "cohorts" exist:
      | name | idnumber |
      | Cohort 1 | CH1 |
    And the following "cohort members" exist:
      | user  | cohort |
      | user2 | CH1    |
      | user3 | CH1    |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | user1 | C1 | student |
      | user2 | C1 | student |
      | user3 | C1 | student |
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration

  @javascript
  Scenario: Filter user accounts by role and cohort
    When I click on "Filters" "button"
    And I set the following fields in the "Course role" "core_reportbuilder > Filter" to these values:
      | Role name         | Student     |
      | Course short name | C1          |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I set the following fields in the "Cohort ID" "core_reportbuilder > Filter" to these values:
      | Cohort ID operator | Is equal to |
      | Cohort ID value    | CH1         |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should see "User Three" in the "reportbuilder-table" "table"
    And I should see "User Four" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Filter user accounts by confirm and authentication method
    When I click on "Filters" "button"
    And I set the following fields in the "Confirmed" "core_reportbuilder > Filter" to these values:
      | Confirmed operator | No |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should see "User Four" in the "reportbuilder-table" "table"
    And I set the following fields in the "Authentication" "core_reportbuilder > Filter" to these values:
      | Authentication operator | Is equal to     |
      | Authentication value    | Manual accounts |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Filter user accounts by enrolled in any course
    When I click on "Filters" "button"
    And I set the following fields in the "Enrolled in any course" "core_reportbuilder > Filter" to these values:
      | Enrolled in any course operator | Yes |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
    And I set the following fields in the "Enrolled in any course" "core_reportbuilder > Filter" to these values:
      | Enrolled in any course operator | No |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should see "User Four" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Filter user accounts by last IP address
    When I click on "Filters" "button"
    And I set the following fields in the "Last IP address" "core_reportbuilder > Filter" to these values:
      | Last IP address operator | Is equal to |
      | Last IP address value    | 127.0.1.1   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
    And I set the following fields in the "Last IP address" "core_reportbuilder > Filter" to these values:
      | Last IP address operator | Is equal to |
      | Last IP address value    | 127.0.1.2   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should see "User Four" in the "reportbuilder-table" "table"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should see "User Three" in the "reportbuilder-table" "table"
    And I should see "User Four" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Filter users by institution and department
    When I click on "Filters" "button"
    And I set the following fields in the "Institution" "core_reportbuilder > Filter" to these values:
      | Institution operator | Is equal to |
      | Institution value    | moodle      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "User One" in the "reportbuilder-table" "table"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
    And I set the following fields in the "Department" "core_reportbuilder > Filter" to these values:
      | Department operator | Is equal to |
      | Department value    | red         |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"

  @javascript
  Scenario: Filter users by custom profile field (specific or any)
    When I click on "Filters" "button"
    And I set the following fields in the "Favourite frog" "core_reportbuilder > Filter" to these values:
      | Favourite frog operator | Is equal to |
      | Favourite frog value    | Kermit      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I set the following fields in the "Type of undead" "core_reportbuilder > Filter" to these values:
      | Type of undead operator | Is equal to |
      | Type of undead value    | Zombie      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "User Two" in the "reportbuilder-table" "table"
    And I should not see "User One" in the "reportbuilder-table" "table"
    And I should not see "User Three" in the "reportbuilder-table" "table"
    And I should not see "User Four" in the "reportbuilder-table" "table"
