@mod @mod_board @javascript
Feature: Templates for mod_board

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | First     | Manager  | manager1@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "role assigns" exist:
      | user     | role           | contextlevel | reference |
      | manager1 | manager        | System       |           |

  Scenario: Admin may access mod_board template management via admin settings
    Given I log in as "admin"

    When I navigate to "Plugins > Activity modules > Board > Board templates" in site administration
    Then I should see "Board templates"

    When I press "Add template"
    And I set the following fields in the "Add template" "dialogue" to these values:
      | Name        | Template 01 |
    And I click on "Add template" "button" in the "Add template" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name        | Template description | Category | Columns | Settings      |
      | Template 01 |                      | System   |         |               |

  Scenario: Site manager may access mod_board template management via direct URL
    Given I log in as "manager1"

    When I am on the "mod_board > Templates" page
    Then I should see "Board templates"

    When I press "Add template"
    And I set the following fields in the "Add template" "dialogue" to these values:
      | Name        | Template 01 |
    And I click on "Add template" "button" in the "Add template" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name        | Template description | Category | Columns | Settings      |
      | Template 01 |                      | System   |         |               |

  Scenario: Site manager may create, update and delete mod_board template
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat A | 0        | cata     |
      | Cat B | 0        | catb     |
    And I log in as "manager1"
    And I am on the "mod_board > Templates" page

    When I press "Add template"
    And I set the following fields in the "Add template" "dialogue" to these values:
      | Name                 | Template 01      |
      | Template description | Some description |
      | Description          | Fancy info       |
      | Category             | Cat A            |
      | Sort by              | None             |
    And I set the field "Columns" to multiline:
"""
Col 1
Col 2
"""
    And I click on "Add template" "button" in the "Add template" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name        | Template description   | Category | Columns | Settings      |
      | Template 01 | Some description       | Cat A    | Col 1   | Sort by: None |
    And I should see "Col 2" in the "Template 01" "table_row"
    And I should see "Fancy info" in the "Template 01" "table_row"
    And I should not see "Single user mode" in the "Template 01" "table_row"

    When I click on "Actions" "link_or_button" in the "Template 01" "table_row"
    And I click on "Update template" "link" in the "Template 01" "table_row"
    And the following fields in the "Update template" "dialogue" match these values:
      | Name                 | Template 01      |
      | Template description | Some description |
      | Category             | Cat A            |
      | Description          | Fancy info       |
      | Sort by              | None             |
    And the field "Columns" matches multiline:
"""
Col 1
Col 2
"""
    And I set the following fields in the "Update template" "dialogue" to these values:
      | Name                 | Template 001      |
      | Template description | Other description |
      | Category             | Cat B             |
      | Description          | Other info        |
      | Sort by              | Choose...         |
      | Single user mode     | Disabled          |
    And I set the field "Columns" to multiline:
"""
Sloupec 1
"""
    And I click on "Update template" "button" in the "Update template" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name         | Template description | Category | Columns   | Settings                   |
      | Template 001 | Other description    | Cat B    | Sloupec 1 | Single user mode: Disabled |
    And I should not see "Col 2" in the "Template 001" "table_row"
    And I should see "Other info" in the "Template 001" "table_row"
    And I should not see "Sort by" in the "Template 001" "table_row"

    When I click on "Actions" "link_or_button" in the "Template 001" "table_row"
    And I click on "Delete template" "link" in the "Template 001" "table_row"
    And I click on "Delete template" "button" in the "Delete template" "dialogue"
    Then I should not see "Template 001"

  Scenario: Site manager may export mod_board template
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat A | 0        | cata     |
      | Cat B | 0        | catb     |
    And I log in as "manager1"
    And I am on the "mod_board > Templates" page
    And I press "Add template"
    And I set the following fields in the "Add template" "dialogue" to these values:
      | Name                 | Template 01      |
      | Template description | Some description |
      | Category             | Cat A            |
      | Sort by              | None             |
    And I set the field "Columns" to multiline:
"""
Col 1
Col 2
"""
    And I click on "Add template" "button" in the "Add template" "dialogue"
    And I click on "Actions" "link_or_button" in the "Template 01" "table_row"
    When I click on "Export template" "link" in the "Template 01" "table_row"
    Then I should see "\"name\": \"Template 01\""
    And I should see "\"columns\": \"Col 1\nCol 2\""
    And I should see "\"sortby\": \"3\""

  @_file_upload
  Scenario: Site manager may import mod_board template
    Given I log in as "manager1"
    And I am on the "mod_board > Templates" page

    When I press "Import template"
    And I upload "mod/board/tests/fixtures/board.json" file to "File" filemanager
    And I click on "Continue" "button" in the "Import template" "dialogue"
    And I click on "Add template" "button" in the "Import template" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name        | Template description | Category | Columns | Settings      |
      | Template 01 | Some description     | System   | Col 1   | Sort by: None |
    And I should see "Col 2" in the "Template 01" "table_row"

    When I press "Import template"
    And I upload "mod/board/tests/fixtures/board_intro.json" file to "File" filemanager
    And I click on "Continue" "button" in the "Import template" "dialogue"
    And I click on "Add template" "button" in the "Import template" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name        | Template description | Category | Columns | Settings                |
      | Template 02 | Other description    | System   |         | Description: Fancy info |
    And I should see "Col 2" in the "Template 01" "table_row"

  Scenario: Teacher may apply template when creating mod_board activity
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat A | 0        | cata     |
      | Cat B | 0        | catb     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2        | cata     |
      | Course 3 | C3        | cata     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
      | teacher1 | C3     | editingteacher |
    And the following "mod_board > templates" exist:
      | name        | description        | columns                    | contextlevel | reference | singleusermode | sortby |
      | Template 00 | First description  |                            | System       |           | -1             | 1      |
      | Template 01 | Second description | Col 1\nCol 2               | Category     | cata      |  1             | -1     |
      | Template 02 | Other description  | Col 1\nCol 2\nCol 3\nCol 4 | Category     | catb      | -1             | -1     |
    And I am on the "Course 2" course page logged in as "teacher1"
    And I turn editing mode on
    And I open dialog for adding mod_board to "General" section
    And I expand all fieldsets
    And the following fields match these values:
      | Board template   | Choose... |
      | Single user mode | Disabled  |
      | Sort by          | None      |
    And the "Board template" select box should contain "Template 01"
    And the "Board template" select box should contain "Template 00"
    And the "Board template" select box should not contain "Template 02"
    When I set the following fields to these values:
      | Board template | Template 01     |
      | Name           | My test board 1 |
    And I press "Save and display"
    Then I should see "Col 1" in the "1" "mod_board > column"
    And I should see "Col 2" in the "2" "mod_board > column"
    And "3" "mod_board > column" should not exist
    And I click on "Settings" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Single user mode | Single user mode (private) |
      | Sort by          | None                       |
    And I should not see "Board template"

    And I am on the "Course 3" course page
    And I open dialog for adding mod_board to "General" section
    When I set the following fields to these values:
      | Board template | Template 00     |
      | Name           | My test board 2 |
    And I press "Save and display"
    Then "1" "mod_board > column" should not exist
    And "2" "mod_board > column" should not exist
    And "3" "mod_board > column" should not exist
    And I click on "Settings" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Single user mode | Disabled      |
      | Sort by          | Creation date |
    And I should not see "Board template"
    And I press "Cancel"

  Scenario: Teacher may apply template to existing mod_board activity without notes
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat A | 0        | cata     |
      | Cat B | 0        | catb     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 2 | C2        | cata     |
      | Course 3 | C3        | cata     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C2     | editingteacher |
      | teacher1 | C3     | editingteacher |
    And the following "mod_board > templates" exist:
      | name        | description        | columns                    | contextlevel | reference | singleusermode | sortby |
      | Template 00 | First description  |                            | System       |           | -1             | 1      |
      | Template 01 | Second description | Col 1\nCol 2               | Category     | cata      |  1             | -1     |
      | Template 02 | Other description  | Col 1\nCol 2\nCol 3\nCol 4 | Category     | catb      | -1             | -1     |
    And the following "activity" exists:
      | activity       | board                  |
      | course         | C2                     |
      | name           | Sample board 1         |
    And I am on the "Sample board 1" "board activity" page logged in as "teacher1"

    When I click on "Apply template" "link" in the ".secondary-navigation" "css_element"
    And the "Board template" select box should contain "Template 01"
    And the "Board template" select box should contain "Template 00"
    And the "Board template" select box should not contain "Template 02"
    And I set the following fields to these values:
      | Board template | Template 01 |
    And I press "Continue"
    And I should see "Second description"
    And I should see "Single user mode: Single user mode (private)"
    And I press "Apply template"
    Then I should see "Col 1" in the "1" "mod_board > column"
    And I should see "Col 2" in the "2" "mod_board > column"
    And "3" "mod_board > column" should not exist
    And I click on "Settings" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Single user mode | Single user mode (private) |
      | Sort by          | None                       |
    And I press "Cancel"

    When I click on "Apply template" "link" in the ".secondary-navigation" "css_element"
    And I set the following fields to these values:
      | Board template | Template 00 |
    And I press "Continue"
    And I press "Apply template"
    Then I should see "Col 1" in the "1" "mod_board > column"
    And I should see "Col 2" in the "2" "mod_board > column"
    And "3" "mod_board > column" should not exist
    And I click on "Settings" "link" in the ".secondary-navigation" "css_element"
    And the following fields match these values:
      | Single user mode | Single user mode (private) |
      | Sort by          | Creation date |
    And I press "Cancel"

    When I click on "Add new post to column Col 1" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | Title Teacher 1-1 |
    And I click on "Post" "button" in the "New post for column Col 1" "dialogue"
    And I reload the page
    Then I should not see "Apply template"

  Scenario: Site manager may filter mod_board templates in management UI
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat A | 0        | cata     |
      | Cat B | 0        | catb     |
      | Cat C | 0        | catc     |
    And the following "mod_board > templates" exist:
      | name         | description        | contextlevel | reference |
      | Template 000 |                    | System       |           |
      | Template 001 |                    | Category     | cata      |
      | Template 002 |                    | Category     | catb      |
    And I log in as "manager1"
    And I am on the "mod_board > Templates" page

    When I click on "Filters" "button"
    And I set the following fields in the "Category" "core_reportbuilder > Filter" to these values:
      | Category operator | Is equal to |
      | Category value    | System      |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    Then the following should exist in the "reportbuilder-table" table:
      | Name         | Category |
      | Template 000 | System   |
    And I should not see "Template 001"
    And I should not see "Template 002"

    When I click on "Filters" "button"
    And I set the following fields in the "Category" "core_reportbuilder > Filter" to these values:
      | Category operator | Is equal to |
      | Category value    | Cat A       |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    Then the following should exist in the "reportbuilder-table" table:
      | Name         | Category |
      | Template 001 | Cat A    |
    And I should not see "Template 000"
    And I should not see "Template 002"
