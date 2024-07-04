@report @report_configlog @core_reportbuilder
Feature: In a report, admin can see configuration changes
  In order see configuration changes
  As an admin
  I need to view the configuration changes report and use search to filter the report

  # Set some config values so the report contains known data.
  Background:
    Given I log in as "admin"
    And I change the window size to "large"
    And I set the following administration settings values:
      | Initial number of overall feedback fields | 5     |
      | Maximum folder download size              | 2048  |
      | Default city                              | Perth |

  @javascript
  Scenario: Display configuration changes report
    When I navigate to "Reports > Config changes" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | First name | Plugin | Setting             | New value | Original value |
      | Admin User | quiz   | initialnumfeedbacks | 5         | 2              |
      | Admin User | folder | maxsizetodownload   | 2048      | 0              |
      | Admin User | core   | defaultcity         | Perth     |                |

  @javascript
  Scenario Outline: Search configuration changes report
    When I navigate to "Reports > Config changes" in site administration
    And I click on "Filters" "button"
    And I set the following fields in the "<field>" "core_reportbuilder > Filter" to these values:
      | <field> operator | Contains   |
      | <field> value    | <search>   |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I should see "Filters applied"
    Then the following should exist in the "reportbuilder-table" table:
      | Plugin   | Setting   | New value |
      | <plugin> | <setting> | <value>   |
    And I should not see "<excluded>" in the "reportbuilder-table" "table"
    Examples:
      | field     | search              | plugin | setting             | value  | excluded            |
      | Plugin    | folder              | folder | maxsizetodownload   | 2048   | quiz                |
      | Setting   | initialnumfeedbacks | quiz   | initialnumfeedbacks | 5      | maxsizetodownload   |
      | Setting   | maxsizetodownload   | folder | maxsizetodownload   | 2048   | initialnumfeedbacks |
      | New value | Perth               | core   | defaultcity         | Perth  | maxsizetodownload   |
      | Full name | Admin User          | core   | defaultcity         | Perth  | zzzzzzzzz           |
