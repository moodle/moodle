@core @core_reportbuilder @javascript
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
    And I click on "Schedule an email" "link" in the ".dropdown" "css_element"
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
    And the following should exist in the "Report schedules" table:
      | Name        | Time last sent | Time next send                          | Modified by |
      | My schedule | Never          | ##tomorrow 11:00##%A, %d %B %Y, %H:%M## | Admin User  |

  Scenario: Create report schedule for audience renamed using filters
    Given the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Audience" dynamic tab
    And I set the field "Rename audience 'All users'" to "<span class=\"multilang\" lang=\"en\">English</span><span class=\"multilang\" lang=\"es\">Spanish</span>"
    When I click on the "Schedules" dynamic tab
    And I press "New schedule"
    And I click on "Schedule an email" "link" in the ".dropdown" "css_element"
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
    Then I should see "My renamed schedule" in the "Report schedules" "table"

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
    Then I should see "English" in the "Report schedules" "table"
    And I should not see "Spanish" in the "Report schedules" "table"
    # Confirm schedule name is correctly shown in actions.
    And I press "Send schedule" action in the "English" report row
    And I should see "Are you sure you want to queue the schedule 'English' for sending immediately?" in the "Send schedule" "dialogue"
    And I click on "Cancel" "button" in the "Send schedule" "dialogue"
    And I press "Delete schedule" action in the "English" report row
    And I should see "Are you sure you want to delete the schedule 'English'?" in the "Delete schedule" "dialogue"
    And I click on "Cancel" "button" in the "Delete schedule" "dialogue"

  Scenario Outline: Filter report schedules by date
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name          | timescheduled | timelastsent  |
      | My report | My schedule 1 | ##yesterday## | ##yesterday## |
      | My report | My schedule 2 | ##tomorrow##  | 0             |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    When I click on the "Schedules" dynamic tab
    And I click on "Filters" "button"
    And I set the following fields in the "<filter>" "core_reportbuilder > Filter" to these values:
      | <filter> operator | Range          |
      | <filter> from     | ##2 days ago## |
      | <filter> to       | ##today##      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "My schedule 1" in the "Report schedules" "table"
    And I should not see "My schedule 2" in the "Report schedules" "table"
    Examples:
      | filter         |
      | Time last sent |
      | Time next send |

  Scenario: Filter report schedules by enabled state
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name          | enabled |
      | My report | My schedule 1 | 1       |
      | My report | My schedule 2 | 0       |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I click on "Filters" "button"
    And I set the field "Enabled operator" in the "Enabled" "core_reportbuilder > Filter" to "Yes"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then I should see "Filters applied"
    And I should see "My schedule 1" in the "Report schedules" "table"
    And I should not see "My schedule 2" in the "Report schedules" "table"
    And I set the field "Enabled operator" in the "Enabled" "core_reportbuilder > Filter" to "No"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should not see "My schedule 1" in the "Report schedules" "table"
    And I should see "My schedule 2" in the "Report schedules" "table"

  Scenario: Toggle report schedule enabled state
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name        |
      | My report | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I click on "Disable schedule" "field" in the "My schedule" "table_row"
    Then the "class" attribute of "My schedule" "table_row" should contain "text-muted"
    And I should see "Schedule disabled" in the ".toast-wrapper" "css_element"
    And I click on "Enable schedule" "field" in the "My schedule" "table_row"
    And the "class" attribute of "My schedule" "table_row" should be set
    And I should see "Schedule enabled" in the ".toast-wrapper" "css_element"

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
      | Subject                | Tell me how to win your heart |
      | Body                   | For I haven't got a clue      |
      | If the report is empty | Don't send message            |
    And I click on "Save" "button" in the "Edit schedule details" "dialogue"
    Then I should see "Schedule updated"
    And the following should exist in the "Report schedules" table:
      | Name                | Time last sent | Time next send                          | Modified by |
      | My updated schedule | Never          | ##tomorrow 11:00##%A, %d %B %Y, %H:%M## | Admin User  |
    And I press "Edit schedule details" action in the "My updated schedule" report row
    And the following fields in the "Edit schedule details" "dialogue" match these values:
      | Name          | My updated schedule |
      | Starting from | ##tomorrow 11:00##  |
      | All users: All site users | 1       |
      | Subject                | Tell me how to win your heart |
      | Body                   | For I haven't got a clue      |
      | If the report is empty | Don't send message            |

  Scenario: Send report schedule
    Given the following "core_reportbuilder > Schedules" exist:
      | report    | name        |
      | My report | My schedule |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I click on the "Schedules" dynamic tab
    When I press "Send schedule" action in the "My schedule" report row
    And I click on "Confirm" "button" in the "Send schedule" "dialogue"
    Then I should see "Schedule sent"
    And I run all adhoc tasks
    And I reload the page
    And the following should exist in the "Report schedules" table:
      | Name        | Time last sent |
      | My schedule | ##today##%A##  |

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
