@core_reportbuilder @javascript
Feature: Configure access to reports based on intended audience
  As a manager
  I want to restrict which users have access to a report

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname |
      | user1     | User      | 1        |
      | user2     | User      | 2        |
      | user3     | User      | 3        |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |

  Scenario: Configure report audience with manually added users audience type
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager1  | Manager   | 1        |
    And the following "role assigns" exist:
      | user     | role    | contextlevel   | reference |
      | manager1 | manager | System         |           |
    And the following "permission overrides" exist:
      | capability                    | permission | role    | contextlevel | reference |
      | moodle/reportbuilder:editall  | Allow      | manager | System       |           |
    And I log in as "manager1"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Access" dynamic tab
    And I should see "Nothing to display"
    And I click on the "Audience" dynamic tab
    And I should see "Add an audience to this report"
    Then I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User 1,User 3"
    And I press "Save changes"
    And I should see "User 1"
    And I should not see "User 2"
    And I should see "User 3"
    And I should not see "Add an audience to this report"
    And I click on the "Access" dynamic tab
    And I should see "User 1"
    And I should not see "User 2"
    And I should see "User 3"
    And I log out

  Scenario: Configure report audience with has system role audience type
    Given the following "roles" exist:
      | shortname | name      | archetype |
      | testrole  | Test role |            |
    And the following "role assigns" exist:
      | user    | role     | contextlevel | reference |
      | user2   | testrole | System       |          |
    And I log in as "admin"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    Then I click on "Add audience 'Assigned system role'" "link"
    And I set the field "Select a role" to "Test role"
    And I press "Save changes"
    And I should see "Test role"
    And I log out

  Scenario: Configure report audience with Member of cohort audience type
    And the following "cohorts" exist:
      | name    | idnumber |
      | Cohort1 | cohort1  |
    And I log in as "admin"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    Then I click on "Add audience 'Member of cohort'" "link"
    And I set the field "Select members from cohort" to "Cohort1"
    And I press "Save changes"
    And I should see "Cohort1"
    And I log out

  Scenario: Configure report audience with Member of cohort audience type with no cohorts available
    Given I log in as "admin"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    Then "Add audience 'All users'" "link" should exist
    # This audience type should be disabled because there are no cohorts available.
    And "Add audience 'Member of cohort'" "link" should not exist

  Scenario: View report as a user with no edit capability and set in the report audience
    Given the following "core_reportbuilder > Reports" exist:
      | name              | source                                   |
      | My second report  | core_user\reportbuilder\datasource\users |
    And the following "roles" exist:
      | shortname       | name      | archetype |
      | viewreportsrole | Test role |           |
    And the following "role assigns" exist:
      | user     | role             | contextlevel | reference |
      | user1    | viewreportsrole  | System       |           |
    And the following "permission overrides" exist:
      | capability                    | permission | role            | contextlevel | reference |
      | moodle/reportbuilder:editall  | Prohibit   | viewreportsrole | System       |           |
      | moodle/reportbuilder:edit     | Prohibit   | viewreportsrole | System       |           |
      | moodle/reportbuilder:view     | Allow      | viewreportsrole | System       |           |
      | moodle/site:configview        | Allow      | viewreportsrole | System       |           |
    When I log in as "user1"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I should see "Custom reports"
    And I should not see "My report"
    And I should not see "My second report"
    And I log out
    And I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "My report" "link" in the "My report" "table_row"
    And I click on the "Audience" dynamic tab
    And I should see "Add an audience to this report"
    Then I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User 1"
    And I press "Save changes"
    And I log out
    And I log in as "user1"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I should not see "My second report"
    And I click on "My report" "link" in the "My report" "table_row"
    And I log out

  Scenario: View report as a user with edit capability
    Given the following "core_reportbuilder > Reports" exist:
      | name              | source                                   |
      | My second report  | core_user\reportbuilder\datasource\users |
    And the following "roles" exist:
      | shortname       | name      | archetype |
      | viewreportsrole | Test role |           |
    And the following "role assigns" exist:
      | user     | role             | contextlevel | reference |
      | user1    | viewreportsrole  | System       |           |
    And the following "permission overrides" exist:
      | capability                    | permission  | role            | contextlevel | reference |
      | moodle/reportbuilder:editall  | Prohibit    | viewreportsrole | System       |           |
      | moodle/reportbuilder:edit     | Allow       | viewreportsrole | System       |           |
      | moodle/reportbuilder:view     | Prohibit    | viewreportsrole | System       |           |
      | moodle/site:configview        | Allow       | viewreportsrole | System       |           |
    When I log in as "user1"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I should see "Custom reports"
    And I should not see "My report"
    And I should not see "My second report"
    And I click on "New report" "button"
    And I set the following fields in the "New report" "dialogue" to these values:
      | Name                  | My user1 report |
      | Report source         | Users           |
      | Include default setup | 1               |
    And I click on "Save" "button" in the "New report" "dialogue"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I should see "My user1 report"
    And I log out
    And I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "My report" "link" in the "My report" "table_row"
    And I click on the "Audience" dynamic tab
    And I should see "Add an audience to this report"
    Then I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User 1"
    And I press "Save changes"
    And I log out
    And I log in as "user1"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I should not see "My second report"
    And I should see "My user1 report"
    And I click on "My report" "link" in the "My report" "table_row"
    And I log out

  Scenario: View report as a user with editall capability
    Given the following "core_reportbuilder > Reports" exist:
      | name              | source                                   |
      | My second report  | core_user\reportbuilder\datasource\users |
    And the following "roles" exist:
      | shortname       | name      | archetype |
      | viewreportsrole | Test role |           |
    And the following "role assigns" exist:
      | user     | role             | contextlevel | reference |
      | user1    | viewreportsrole  | System       |           |
    And the following "permission overrides" exist:
      | capability                    | permission  | role            | contextlevel | reference |
      | moodle/reportbuilder:editall  | Allow       | viewreportsrole | System       |           |
      | moodle/reportbuilder:edit     | Prohibit    | viewreportsrole | System       |           |
      | moodle/reportbuilder:view     | Prohibit    | viewreportsrole | System       |           |
      | moodle/site:configview        | Allow       | viewreportsrole | System       |           |
    When I log in as "user1"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I should see "Custom reports"
    And I should see "My report"
    Then I click on "My second report" "link" in the "My second report" "table_row"
    And I should see "Email address"
    And I log out
