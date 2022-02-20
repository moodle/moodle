@core_reportbuilder @javascript
Feature: Manage custom report columns aggregation
  In order to manage the aggregation for columns of custom reports
  As an admin
  I need to select an aggregation for columns

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              | confirmed | lastaccess     |
      | user01   | Bill      | Richie   | user01@example.com | 1         | ##2 days ago## |
      | user02   | Ben       | Richie   | user02@example.com | 1         | ##3 days ago## |
      | user03   | Bill      | Richie   | user03@example.com | 0         | ##3 days ago## |

  Scenario Outline: Aggregate a text column
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:lastname    |
      | My report | user:firstname   |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I set the "First name" column aggregation to "<aggregation>"
    Then I should see "Aggregated column 'First name'"
    And I should see "<output>" in the "Richie" "table_row"
    Examples:
      | aggregation                     | output          |
      | Comma separated distinct values | Ben, Bill       |
      | Comma separated values          | Ben, Bill, Bill |
      | Count                           | 3               |
      | Count distinct                  | 2               |

  Scenario Outline: Aggregate a text column containing multiple fields
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:lastname    |
      | My report | user:fullname    |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I set the "Full name" column aggregation to "<aggregation>"
    Then I should see "Aggregated column 'Full name'"
    And I should see "<output>" in the "Richie" "table_row"
    Examples:
      | aggregation                     | output                               |
      | Comma separated distinct values | Ben Richie, Bill Richie              |
      | Comma separated values          | Ben Richie, Bill Richie, Bill Richie |
      | Count                           | 3                                    |
      | Count distinct                  | 2                                    |

  Scenario Outline: Aggregate a time column
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:lastname    |
      | My report | user:lastaccess  |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I set the "Last access" column aggregation to "<aggregation>"
    Then I should see "Aggregated column 'Last access'"
    And I should see "<output>" in the "Richie" "table_row"
    Examples:
      | aggregation    | output                       |
      | Count          | 3                            |
      | Count distinct | 2                            |
      | Maximum        | ##2 days ago##%A, %d %B %Y## |
      | Minimum        | ##3 days ago##%A, %d %B %Y## |

  Scenario Outline: Aggregate a boolean column
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:lastname    |
      | My report | user:confirmed   |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I set the "Confirmed" column aggregation to "<aggregation>"
    Then I should see "Aggregated column 'Confirmed'"
    And I should see "<output>" in the "Richie" "table_row"
    Examples:
      | aggregation                     | output       |
      | Comma separated distinct values | No, Yes      |
      | Comma separated values          | No, Yes, Yes |
      | Count                           | 3            |
      | Count distinct                  | 2            |
      | Maximum                         | Yes          |
      | Minimum                         | No           |
      | Average                         | 0.7          |
      | Percentage                      | 66.7%        |
      | Sum                             | 2            |

  Scenario Outline: Aggregated columns display localised floats
    Given the following "language customisations" exist:
      | component       | stringid | value |
      | core_langconfig | decsep   | ,     |
    And the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default |
      | My report | core_user\reportbuilder\datasource\users | 0       |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:lastname    |
      | My report | user:confirmed   |
    And I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    And I change window size to "large"
    When I set the "Confirmed" column aggregation to "<aggregation>"
    Then I should see "Aggregated column 'Confirmed'"
    And I should see "<output>" in the "Richie" "table_row"
    Examples:
      | aggregation | output |
      | Average     | 0,7    |
      | Percentage  | 66,7%  |

  Scenario: Show unique report rows
    Given the following "core_reportbuilder > Reports" exist:
      | name      | source                                   | default | uniquerows |
      | My report | core_user\reportbuilder\datasource\users | 0       | 1          |
    And the following "core_reportbuilder > Columns" exist:
      | report    | uniqueidentifier |
      | My report | user:firstname   |
      | My report | user:lastname    |
    When I am on the "My report" "reportbuilder > Editor" page logged in as "admin"
    Then the following should exist in the "reportbuilder-table" table:
      | -1-        | -2-     |
      | Admin      | User    |
      | Ben        | Richie  |
      | Bill       | Richie  |
    # Assert there is no 4th row (duplicate Bill Richie) because we're showing unique rows.
    And "//table[@data-region='reportbuilder-table']/tbody/tr[not(@class = 'emptyrow')][4]" "xpath_element" should not exist
    And I set the "First name" column aggregation to "Comma separated values"
    And the following should exist in the "reportbuilder-table" table:
      | -1-             | -2-     |
      | Admin           | User    |
      | Ben, Bill, Bill | Richie  |
