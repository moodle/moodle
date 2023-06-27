@tool @tool_licensemanager
Feature: Custom licences
  In order to use custom licences
  As an admin
  I need to be able to add custom licences

  Scenario: I am able to create custom licences
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Create licence" "link"
    And I set the following fields to these values:
      | shortname       | MIT                                 |
      | fullname        | MIT Licence                         |
      | source          | https://opensource.org/licenses/MIT |
      | Licence version | ##first day of January 2020##       |
    When I press "Save changes"
    Then I should see "Licence manager"
    And I should see "MIT Licence" in the "MIT" "table_row"
    And I should see "https://opensource.org/licenses/MIT" in the "MIT" "table_row"

  Scenario: I am only be able to make custom license with a valid url source (including scheme).
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Create licence" "link"
    And I set the following fields to these values:
      | shortname       | MIT                         |
      | fullname        | MIT Licence                 |
      | source          | opensource.org/licenses/MIT |
      | Licence version | ##2020-01-01##              |
    When I press "Save changes"
    Then I should see "Invalid source URL"
    And I set the following fields to these values:
      | source         | mailto:tomdickman@catalyst-au.net   |
    And I press "Save changes"
    And I should see "Invalid source URL"
    And I set the following fields to these values:
      | source         | https://opensource.org/licenses/MIT |
    And I press "Save changes"
    And I should see "Licence manager"
    And I should see "MIT Licence" in the "MIT" "table_row"
    And I should see "https://opensource.org/licenses/MIT" in the "MIT" "table_row"

  Scenario: Custom license version format must be YYYYMMDD00
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Create licence" "link"
    And I set the following fields to these values:
      | shortname       | MIT                                 |
      | fullname        | MIT Licence                         |
      | source          | https://opensource.org/licenses/MIT |
      | Licence version | ##1 March 2019##                    |
    When I press "Save changes"
    Then I should see "Licence manager"
    And I should see "2019030100" in the "MIT" "table_row"

  @javascript
  Scenario: Custom license short name should not be editable after first creation
    Given I log in as "admin"
    And I navigate to "Licence > Licence manager" in site administration
    And I click on "Create licence" "link"
    And I set the following fields to these values:
      | shortname       | MIT                                 |
      | fullname        | MIT Licence                         |
      | source          | https://opensource.org/licenses/MIT |
      | Licence version | ##1 Mar 2019##                      |
    And I press "Save changes"
    And I should see "Licence manager"
    And I should see "MIT Licence" in the "MIT" "table_row"
    When I click on "Edit" "icon" in the "MIT" "table_row"
    Then I should see "Edit licence"
    And the "shortname" "field" should be disabled
