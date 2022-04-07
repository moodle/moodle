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
    And I am on the "My report" "reportbuilder > Editor" page logged in as "manager1"
    And I click on the "Access" dynamic tab
    And I should see "Nothing to display"
    And I click on the "Audience" dynamic tab
    And I should see "There are no audiences for this report"
    Then I click on "Add audience 'Manually added users'" "link"
    And I should see "Added audience 'Manually added users'"
    And I set the field "Add users manually" to "User 1,User 3"
    And I press "Save changes"
    And I should see "Audience saved"
    And I should see "User 1"
    And I should not see "User 2"
    And I should see "User 3"
    And I should not see "There are no audiences for this report"
    And I click on the "Access" dynamic tab
    And I should see "User 1" in the "reportbuilder-table" "table"
    And I should not see "User 2" in the "reportbuilder-table" "table"
    And I should see "User 3" in the "reportbuilder-table" "table"

  Scenario: Configure report audience with has system role audience type
    Given the following "roles" exist:
      | shortname | name      | archetype |
      | testrole  | Test role |            |
    And the following "role assigns" exist:
      | user    | role     | contextlevel | reference |
      | user2   | testrole | System       |          |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    When I click on "Add audience 'Assigned system role'" "link"
    And I should see "Added audience 'Assigned system role'"
    And I set the field "Select a role" to "Test role"
    And I press "Save changes"
    Then I should see "Audience saved"
    And I should see "Test role"
    And I should not see "There are no audiences for this report"
    And I click on the "Access" dynamic tab
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should see "User 2" in the "reportbuilder-table" "table"
    And I should not see "User 3" in the "reportbuilder-table" "table"

  Scenario: Configure report audience with Member of cohort audience type
    Given the following "cohorts" exist:
      | name    | idnumber |
      | Cohort1 | cohort1  |
    And the following "cohort members" exist:
      | cohort  | user  |
      | cohort1 | user3 |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    When I click on "Add audience 'Member of cohort'" "link"
    And I should see "Added audience 'Member of cohort'"
    And I set the field "Select members from cohort" to "Cohort1"
    And I press "Save changes"
    Then I should see "Audience saved"
    And I should see "Cohort1"
    And I should not see "There are no audiences for this report"
    And I click on the "Access" dynamic tab
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should not see "User 2" in the "reportbuilder-table" "table"
    And I should see "User 3" in the "reportbuilder-table" "table"

  Scenario: Configure report audience with Member of cohort audience type with no cohorts available
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on the "Audience" dynamic tab
    Then "Add audience 'All users'" "link" should exist
    # This audience type should be disabled because there are no cohorts available.
    And "Add audience 'Member of cohort'" "link" should not exist

  Scenario: Search for and add audience to report
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    When I set the field "Search" in the "[data-region=sidebar-menu]" "css_element" to "All users"
    Then I should see "All users" in the "[data-region=sidebar-menu]" "css_element"
    And I should not see "Member of cohort" in the "[data-region=sidebar-menu]" "css_element"
    And I click on "Add audience 'All users'" "link"
    And I should see "Added audience 'All users'"
    And I press "Save changes"
    And I should see "Audience saved"

  Scenario: Rename report audience
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    And I click on "Add audience 'All users'" "link"
    And I press "Save changes"
    When I set the field "Rename audience 'All users'" to "All my lovely users"
    And I reload the page
    Then I should see "All my lovely users" in the "[data-region='audience-card']" "css_element"

  Scenario: Rename report audience using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    And I click on "Add audience 'All users'" "link"
    And I press "Save changes"
    When I set the field "Rename audience 'All users'" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    And I reload the page
    Then I should see "English" in the "[data-region='audience-card']" "css_element"
    And I should not see "Spanish" in the "[data-region='audience-card']" "css_element"

  Scenario: Delete report audience
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    And I click on "Add audience 'All users'" "link"
    And I press "Save changes"
    When I click on "Delete audience 'All users'" "button"
    And I click on "Delete" "button" in the "Delete audience 'All users'" "dialogue"
    Then I should see "Deleted audience 'All users'"
    And I should see "There are no audiences for this report"

  Scenario: Edit report audience with manually added users audience type
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Access" dynamic tab
    And I should see "Nothing to display"
    And I click on the "Audience" dynamic tab
    And I should see "There are no audiences for this report"
    And I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User 1,User 3"
    And I press "Save changes"
    When I press "Edit audience 'Manually added users'"
    And I set the field "Add users manually" to "User 2"
    And I press "Save changes"
    Then I should see "Audience saved"
    And I should not see "User 1"
    And I should see "User 2"
    And I should not see "User 3"
    And I click on the "Access" dynamic tab
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should see "User 2" in the "reportbuilder-table" "table"
    And I should not see "User 3" in the "reportbuilder-table" "table"

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
    When I log in as "user1"
    And I follow "Reports" in the user menu
    And I should see "Custom reports"
    And I should not see "My report"
    And I should not see "My second report"
    And I log out
    And I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "My report" "link" in the "My report" "table_row"
    And I click on the "Audience" dynamic tab
    And I should see "There are no audiences for this report"
    Then I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User 1"
    And I press "Save changes"
    And I log out
    And I log in as "user1"
    And I follow "Reports" in the user menu
    And I should not see "My second report"
    And I click on "My report" "link" in the "My report" "table_row"

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
    When I log in as "user1"
    And I follow "Reports" in the user menu
    And I should see "Custom reports"
    And I should not see "My report"
    And I should not see "My second report"
    And I click on "New report" "button"
    And I set the following fields in the "New report" "dialogue" to these values:
      | Name                  | My user1 report |
      | Report source         | Users           |
      | Include default setup | 1               |
    And I click on "Save" "button" in the "New report" "dialogue"
    And I click on "Close 'My user1 report' editor" "button"
    And I should see "My user1 report"
    And I log out
    And I log in as "admin"
    And I navigate to "Reports > Report builder > Custom reports" in site administration
    And I click on "My report" "link" in the "My report" "table_row"
    And I click on the "Audience" dynamic tab
    And I should see "There are no audiences for this report"
    Then I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User 1"
    And I press "Save changes"
    And I log out
    And I log in as "user1"
    And I follow "Reports" in the user menu
    And I should not see "My second report"
    And I should see "My user1 report"
    And I click on "My report" "link" in the "My report" "table_row"

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
    When I log in as "user1"
    And I follow "Reports" in the user menu
    And I should see "Custom reports"
    And I should see "My report"
    Then I click on "My second report" "link" in the "My second report" "table_row"
    And I should see "Email address"
