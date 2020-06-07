@report @report_configlog
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
    Then the following should exist in the "report-configlog-report-table" table:
      | User       | Plugin | Setting             | New value | Original value |
      | Admin User | quiz   | initialnumfeedbacks | 5         | 2              |
      | Admin User | folder | maxsizetodownload   | 2048      | 0              |
      | Admin User | core   | defaultcity         | Perth     |                |

  @javascript
  Scenario Outline: Search configuration changes report
    When I navigate to "Reports > Config changes" in site administration
    And I click on "Show more..." "link"
    And I set the field "<field>" to "<search>"
    And I click on "Search" "button" in the "#fitem_id_submitbutton" "css_element"
    Then the following should exist in the "report-configlog-report-table" table:
      | Plugin   | Setting   | New value |
      | <plugin> | <setting> | <value>   |
    And I should not see "<excluded>" in the "report-configlog-report-table" "table"
    Examples:
      | field   | search              | plugin | setting             | value  | excluded            |
      | Setting | initialnumfeedbacks | quiz   | initialnumfeedbacks | 5      | maxsizetodownload   |
      | Setting | maxsizetodownload   | folder | maxsizetodownload   | 2048   | initialnumfeedbacks |
      | Value   | Perth               | core   | defaultcity         | Perth  | maxsizetodownload   |
      | User    | Admin               | core   | defaultcity         | Perth  | zzzzzzzzz           |
