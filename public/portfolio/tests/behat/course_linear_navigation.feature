@core @core_portfolio
Feature: Hide the course linear navigation in the portfolio pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want the course linear navigation to be hidden in portfolio pages

  Background:
    Given the following config values are set as admin:
      | enableportfolios | 1 |
    And the following "users" exist:
      | username | firstname | lastname |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
    And the following "activities" exist:
      | activity | course | name        | assignsubmission_onlinetext_enabled | submissiondrafts | duedate       |
      | assign   | C1     | Assignment1 | 1                                   | 0                | ##tomorrow##  |
    And I log in as "admin"
    And I navigate to "Plugins > Portfolios > Manage portfolios" in site administration
    And I set the portfolio instance "File download" to "Enabled and visible"
    And I press "Save"

  @javascript
  Scenario: As a user I should not see the course linear navigation in portfolio pages
    When I am on the "Assignment1" Activity page logged in as student
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I don't want to miss a thing |
    And I press "Save changes"
    # Portfolio export wizard.
    And I follow "Export to portfolio"
    And the course linear navigation should not be visible
    And I press "Next"
    And I press "Continue"
    # Portfolio download page.
    And the course linear navigation should not be visible
