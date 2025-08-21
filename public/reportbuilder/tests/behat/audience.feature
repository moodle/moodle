@core_reportbuilder @javascript
Feature: Configure access to reports based on intended audience
  As an admin
  I want to restrict which users have access to a report

  Background:
    Given the following "custom profile fields" exist:
      | datatype | shortname | name  |
      | text     | fruit     | Fruit |
    And the following "users" exist:
      | username  | firstname | middlename | lastname | email             | profile_field_fruit |
      | user1     | User      | One        | 1        | user1@example.com | Apple               |
      | user2     | User      | Two        | 2        | user2@example.com | Banana              |
      | user3     | User      | Three      | 3        | user3@example.com | Banana              |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 1       |

  Scenario: Configure report audience with manually added users audience type
    When I log in as "user1"
    And I follow "Reports" in the user menu
    And I should see "Nothing to display"
    And I log out
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Access" dynamic tab
    And I should see "Nothing to display"
    And I click on the "Audience" dynamic tab
    And I should see "There are no audiences for this report"
    And I click on "Add audience 'Manually added users'" "link"
    And I should see "Added audience 'Manually added users'"
    And I should see "Audience not saved" in the "Manually added users" "core_reportbuilder > Audience"
    And I set the following fields in the "Manually added users" "core_reportbuilder > Audience" to these values:
      | Add users manually | User 1,User 3 |
    And I press "Save changes"
    And I should see "Audience saved"
    And I should not see "Audience not saved" in the "Manually added users" "core_reportbuilder > Audience"
    And I should see "User 1, User 3" in the "Manually added users" "core_reportbuilder > Audience"
    And I should not see "There are no audiences for this report"
    And I click on the "Access" dynamic tab
    And I should see "User 1" in the "reportbuilder-table" "table"
    And I should not see "User 2" in the "reportbuilder-table" "table"
    And I should see "User 3" in the "reportbuilder-table" "table"
    And I log out
    And I log in as "user1"
    And I follow "Reports" in the user menu
    Then I should see "My report" in the "reportbuilder-table" "table"
    And I click on "My report" "link" in the "My report" "table_row"
    And I should see "User 1" in the "reportbuilder-table" "table"

  Scenario: Configure report audience with administrator audience type
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    When I click on "Add audience 'Site administrators'" "link"
    And I press "Save changes"
    Then I should see "Audience saved"
    And I click on the "Access" dynamic tab
    And I should see "Admin User" in the "reportbuilder-table" "table"
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should not see "User 2" in the "reportbuilder-table" "table"
    And I should not see "User 3" in the "reportbuilder-table" "table"

  Scenario: Configure report audience with has system role audience type
    Given the following "role assigns" exist:
      | user    | role     | contextlevel | reference |
      | user2   | manager  | System       |          |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    When I click on "Add audience 'Assigned system role'" "link"
    And I should see "Added audience 'Assigned system role'"
    And I set the following fields in the "Assigned system role" "core_reportbuilder > Audience" to these values:
      | Select a role | Manager |
    And I press "Save changes"
    Then I should see "Audience saved"
    And I should see "Manager" in the "Assigned system role" "core_reportbuilder > Audience"
    And I click on the "Access" dynamic tab
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should see "User 2" in the "reportbuilder-table" "table"
    And I should not see "User 3" in the "reportbuilder-table" "table"
    And I log out
    And I am on the "My report" "reportbuilder > View" page logged in as "user2"
    And I should see "User 1" in the "reportbuilder-table" "table"

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
    And I set the following fields in the "Member of cohort" "core_reportbuilder > Audience" to these values:
      | Select members from cohort | Cohort1 |
    And I press "Save changes"
    Then I should see "Audience saved"
    And I should see "Cohort1" in the "Member of cohort" "core_reportbuilder > Audience"
    And I click on the "Access" dynamic tab
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should not see "User 2" in the "reportbuilder-table" "table"
    And I should see "User 3" in the "reportbuilder-table" "table"
    And I log out
    And I am on the "My report" "reportbuilder > View" page logged in as "user3"
    And I should see "User 1" in the "reportbuilder-table" "table"

  Scenario: Configure report audience with Member of cohort audience type with no cohorts available
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on the "Audience" dynamic tab
    Then "Add audience 'All users'" "link" should exist
    # This audience type should be disabled because there are no cohorts available.
    And "Add audience 'Member of cohort'" "link" should not exist
    And the "title" attribute of "//div[@data-region='sidebar-menu']/descendant::div[normalize-space(.)='Member of cohort']" "xpath_element" should contain "Not available"

  Scenario: Configure and filter report audience with multiple types
    Given the following "core_reportbuilder > Audiences" exist:
      | report    | classname                                           | configdata |
      | My report | \core_reportbuilder\reportbuilder\audience\admins   |            |
      | My report | \core_reportbuilder\reportbuilder\audience\allusers |            |
    When I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Access" dynamic tab
    Then the following should exist in the "Report access list" table:
      | -1-        |
      | Admin User |
      | User 1     |
      | User 2     |
      | User 3     |
    # Now let's filter them.
    And I click on "Filters" "button"
    And I set the field "Audience value" in the "Audience" "core_reportbuilder > Filter" to "Site administrators"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And the following should exist in the "Report access list" table:
      | -1-        |
      | Admin User |
    And the following should not exist in the "Report access list" table:
      | -1-        |
      | User 1     |
      | User 2     |
      | User 3     |

  Scenario: Configure report audience as user who cannot use specific audience
    Given the following "users" exist:
      | username  | firstname | lastname |
      | manager1  | Manager   | 1        |
    And the following "role assigns" exist:
      | user     | role    | contextlevel   | reference |
      | manager1 | manager | System         |           |
    And the following "permission overrides" exist:
      | capability                   | permission | role    | contextlevel | reference |
      | moodle/reportbuilder:editall | Allow      | manager | System       |           |
      | moodle/cohort:view           | Prohibit   | manager | System       |           |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "manager1"
    When I click on the "Audience" dynamic tab
    Then I should not see "Member of cohort" in the "[data-region='sidebar-menu']" "css_element"

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
    And I should see "Are you sure you want to delete the audience 'All users'?" in the "Delete audience 'All users'" "dialogue"
    And I click on "Delete" "button" in the "Delete audience 'All users'" "dialogue"
    Then I should see "Deleted audience 'All users'"
    And "All users" "core_reportbuilder > Audience" should not exist
    And I should see "There are no audiences for this report"

  Scenario: Delete report audience used in schedule
    Given the following "core_reportbuilder > Audiences" exist:
      | report    | configdata |
      | My report |            |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    And I press "New schedule"
    And I click on "Schedule an email" "link" in the ".dropdown" "css_element"
    And I set the following fields in the "New schedule" "dialogue" to these values:
      | Name          | My schedule                     |
      | Starting from | ##tomorrow 11:00##              |
      | All users     | 1                               |
      | Subject       | Cause you know just what to say |
      | Body          | And you know just what to do    |
    And I click on "Save" "button" in the "New schedule" "dialogue"
    When I click on the "Audience" dynamic tab
    Then I should see "This audience is used in a schedule for this report" in the "All users" "core_reportbuilder > Audience"
    And I click on "Delete audience 'All users'" "button"
    And I should see "This audience is used in a schedule for this report" in the "Delete audience 'All users'" "dialogue"
    And I click on "Delete" "button" in the "Delete audience 'All users'" "dialogue"
    And I should see "Deleted audience 'All users'"
    And "All users" "core_reportbuilder > Audience" should not exist
    And I should see "There are no audiences for this report"

  Scenario: Edit report audience with manually added users audience type
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Access" dynamic tab
    And I should see "Nothing to display"
    And I click on the "Audience" dynamic tab
    And I click on "Add audience 'Manually added users'" "link"
    And I set the following fields in the "Manually added users" "core_reportbuilder > Audience" to these values:
      | Add users manually | User 1,User 3 |
    And I press "Save changes"
    When I press "Edit audience 'Manually added users'"
    And "User 1" "autocomplete_selection" in the "Manually added users" "core_reportbuilder > Audience" should be visible
    And "User 3" "autocomplete_selection" in the "Manually added users" "core_reportbuilder > Audience" should be visible
    And I set the following fields in the "Manually added users" "core_reportbuilder > Audience" to these values:
      | Add users manually | User 2 |
    And I press "Save changes"
    Then I should see "Audience saved"
    And I should see "User 2" in the "Manually added users" "core_reportbuilder > Audience"
    And I click on the "Access" dynamic tab
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should see "User 2" in the "reportbuilder-table" "table"
    And I should not see "User 3" in the "reportbuilder-table" "table"

  Scenario: View configured user additional names on the access tab
    Given the following config values are set as admin:
      | alternativefullnameformat | firstname middlename lastname |
    And the following "core_reportbuilder > Audiences" exist:
      | report    | configdata |
      | My report |            |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on the "Access" dynamic tab
    Then I should see "User One 1" in the "reportbuilder-table" "table"
    And I should see "User Two 2" in the "reportbuilder-table" "table"
    And I should see "User Three 3" in the "reportbuilder-table" "table"
    # Now sort each of them.
    And I click on "First name" "link" in the "reportbuilder-table" "table"
    And "Admin User" "table_row" should appear before "User One 1" "table_row"
    And I click on "First name" "link" in the "reportbuilder-table" "table"
    And "User One 1" "table_row" should appear before "Admin User" "table_row"
    And I click on "Middle name" "link" in the "reportbuilder-table" "table"
    And "User Two 2" "table_row" should appear before "User One 1" "table_row"
    And I click on "Middle name" "link" in the "reportbuilder-table" "table"
    And "User One 1" "table_row" should appear before "User Two 2" "table_row"
    And I click on "Last name" "link" in the "reportbuilder-table" "table"
    And "User One 1" "table_row" should appear before "User Two 2" "table_row"
    And I click on "Last name" "link" in the "reportbuilder-table" "table"
    And "User Two 2" "table_row" should appear before "User One 1" "table_row"

  Scenario: View configured user identity fields on the access tab
    Given the following config values are set as admin:
      | showuseridentity | email,profile_field_fruit |
    And the following "core_reportbuilder > Audiences" exist:
      | report    | configdata |
      | My report |            |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on the "Access" dynamic tab
    Then the following should exist in the "reportbuilder-table" table:
      | -1-    | Email address     | Fruit  |
      | User 1 | user1@example.com | Apple  |
      | User 2 | user2@example.com | Banana |
      | User 3 | user3@example.com | Banana |
    # Now let's filter them.
    And I click on "Filters" "button"
    And I set the following fields in the "Fruit" "core_reportbuilder > Filter" to these values:
      | Fruit operator | Is equal to |
      | Fruit value    | Banana      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should see "User 2" in the "reportbuilder-table" "table"
    And I should see "User 3" in the "reportbuilder-table" "table"
    And I set the following fields in the "Email address" "core_reportbuilder > Filter" to these values:
      | Email address operator | Contains |
      | Email address value    | user2 |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "User 1" in the "reportbuilder-table" "table"
    And I should see "User 2" in the "reportbuilder-table" "table"
    And I should not see "User 3" in the "reportbuilder-table" "table"

  Scenario: View report as a user with edit capability
    Given the following "roles" exist:
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
    And I should see "Nothing to display"
    And I click on "New report" "button"
    And I set the following fields in the "New report" "dialogue" to these values:
      | Name                  | My user1 report |
      | Report source         | Users           |
      | Include default setup | 1               |
    And I click on "Save" "button" in the "New report" "dialogue"
    And I click on "Close 'My user1 report' editor" "button"
    And I should see "My user1 report" in the "reportbuilder-table" "table"
    And I press "View report" action in the "My user1 report" report row
    And I should see "User 1" in the "reportbuilder-table" "table"

  Scenario: View report as a user with editall capability
    Given the following "roles" exist:
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
    Then I should see "My report" in the "reportbuilder-table" "table"
    And I press "View report" action in the "My report" report row
    And I should see "User 1" in the "reportbuilder-table" "table"
