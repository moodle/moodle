@core_reportbuilder @javascript
Feature: Manage custom report schedules
  In order ot manage custom report schedules
  As an admin
  I need to create new and edit existing report schedules

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname |
      | user1     | User      | One      |
      | user2     | User      | Two      |
      | user3     | User      | Three    |
    And the following "core_reportbuilder > Report" exists:
      | name    | My report                                |
      | source  | core_user\reportbuilder\datasource\users |
      | default | 1                                        |
    And the following "core_reportbuilder > Audience" exists:
      | report     | My report                                          |
      | classname  | core_reportbuilder\reportbuilder\audience\allusers |
      | configdata |                                                    |

  Scenario: Create report schedule
    Given I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    # Rename the existing audience.
    And I set the field "Rename audience 'All users'" to "All my lovely users"
    # Add a second audience.
    And I click on "Add audience 'Manually added users'" "link"
    And I set the field "Add users manually" to "User One, User Two"
    And I press "Save changes"
    When I click on the "Schedules" dynamic tab
    And I press "New schedule"
    And I set the following fields in the "New schedule" "dialogue" to these values:
      | Name          | My schedule                 |
      | Starting from | ##tomorrow 11:00##          |
      | Subject       | You're all I've ever wanted |
      | Body          | And my arms are open wide   |
    # Confirm each audience is present in the form, select only the manually added users.
    And I should see "All my lovely users" in the "New schedule" "dialogue"
    And I set the field "Manually added users: User One, User Two" to "1"
    And I click on "Save" "button" in the "New schedule" "dialogue"
    Then I should see "Schedule created"
    And the following should exist in the "reportbuilder-table" table:
      | Name        | Starting from                           | Time last sent | Modified by |
      | My schedule | ##tomorrow 11:00##%A, %d %B %Y, %H:%M## | Never          | Admin User  |

  Scenario: Create report schedule for audience renamed using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    And I set the field "Rename audience 'All users'" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    When I click on the "Schedules" dynamic tab
    And I press "New schedule"
    Then I should see "English" in the "New schedule" "dialogue"
    And I should not see "Spanish" in the "New schedule" "dialogue"
    And I click on "Cancel" "button" in the "New schedule" "dialogue"

  Scenario: Rename report schedule
    Given the following "core_reportbuilder > Schedule" exists:
      | report | My report   |
      | name   | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I set the field "Edit schedule name" in the "My schedule" "table_row" to "My renamed schedule"
    And I reload the page
    Then I should see "My renamed schedule" in the "reportbuilder-table" "table"

  Scenario: Rename report schedule using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And the following "core_reportbuilder > Schedule" exists:
      | report | My report   |
      | name   | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I set the field "Edit schedule name" in the "My schedule" "table_row" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    And I reload the page
    Then I should see "English" in the "reportbuilder-table" "table"
    And I should not see "Spanish" in the "reportbuilder-table" "table"
    # Confirm schedule name is correctly shown in actions.
    And I press "Send schedule" action in the "English" report row
    And I should see "Are you sure you want to queue the schedule 'English' for sending immediately?" in the "Send schedule" "dialogue"
    And I click on "Cancel" "button" in the "Send schedule" "dialogue"
    And I press "Delete schedule" action in the "English" report row
    And I should see "Are you sure you want to delete the schedule 'English'?" in the "Delete schedule" "dialogue"
    And I click on "Cancel" "button" in the "Delete schedule" "dialogue"

  Scenario: Toggle report schedule
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name        |
      | My report | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I click on "Disable schedule" "field" in the "My schedule" "table_row"
    Then the "class" attribute of "My schedule" "table_row" should contain "text-muted"
    And I click on "Enable schedule" "field" in the "My schedule" "table_row"

  Scenario: Edit report schedule
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name        |
      | My report | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I press "Edit schedule details" action in the "My schedule" report row
    And I set the following fields in the "Edit schedule details" "dialogue" to these values:
      | Name          | My updated schedule |
      | Starting from | ##tomorrow 11:00##  |
      | All users: All site users | 1       |
    And I click on "Save" "button" in the "Edit schedule details" "dialogue"
    Then I should see "Schedule updated"
    And the following should exist in the "reportbuilder-table" table:
      | Name                | Starting from                           |
      | My updated schedule | ##tomorrow 11:00##%A, %d %B %Y, %H:%M## |

  Scenario: Send report schedule
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name        |
      | My report | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I press "Send schedule" action in the "My schedule" report row
    And I click on "Confirm" "button" in the "Send schedule" "dialogue"
    Then I should see "Schedule sent"

  Scenario: Delete report schedule
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name        |
      | My report | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I press "Delete schedule" action in the "My schedule" report row
    And I click on "Delete" "button" in the "Delete schedule" "dialogue"
    Then I should see "Schedule deleted"
    And I should see "Nothing to display"
