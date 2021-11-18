@mod @mod_lti
Feature: Navigate existing LTI tool types using pagination
  In order to manage reusable activities for teachers
  As an admin
  I need to view existing tools

  Background:
    Given 100 "mod_lti > tool types" exist with the following data:
      |name        |Test tool [count]                  |
      |description |Example description [count]        |
      |baseurl     |https://www.example.com/tool[count]|

  @javascript
  Scenario: View first page of tool types.
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    Then I should see "Test tool 30"
    And "Test tool 70" "text" should not be visible

  @javascript
  Scenario: View second page of tool types using page 2 button.
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I click on "2" "link"
    Then I should see "Test tool 70"
    And "Test tool 30" "text" should not be visible

  @javascript
  Scenario: View last page of tool types using page 2 button.
    Given I log in as "admin"
    When I navigate to "Plugins > Activity modules > External tool > Manage tools" in site administration
    And I click on "Last" "link"
    Then I should see "Test tool 70"
    And "Test tool 30" "text" should not be visible
