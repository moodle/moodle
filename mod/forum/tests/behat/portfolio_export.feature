@mod @mod_forum
Feature: Ensure only users with appropriate permissions can export forum discussions.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                 |
      | teacher1 | Teacher   | 1        | teacher1@example.com  |
      | student1 | Student   | 1        | student1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  |
      | Course 1 | C1         | 0         |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
  @javascript
  Scenario: A teacher can export discussions to a portfolio.
    Given the following "activities" exist:
      | activity   | name                   | intro             | course | idnumber     | groupmode |
      | forum      | Test forum 1           | Test forum 2      | C1     | forum        | 0         |
    And I log in as "admin"
    And the following config values are set as admin:
      | enableportfolios | 1 |
    And I navigate to "Plugins > Portfolios > Manage portfolios" in site administration
    And I set portfolio instance "File download" to "Enabled and visible"
    And I click on "Save" "button"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum 1"
    And I add a new discussion to "Test forum 1" forum with:
      | Subject | Discussion 1 |
      | Message | Test post message |
    And I reload the page
    And I follow "Discussion 1"
    And the button "Export whole discussion to portfolio" does not exist
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Test forum 1"
    And I follow "Discussion 1"
    And the button "Export whole discussion to portfolio" exists
    And I press "Export whole discussion to portfolio"
    Then I should see "Exporting to portfolio"
