@ou @ou_vle @report @report_editdates
Feature: Timeline view
  When a user view edit dates report
  They can see a timeline view at the bottom

  Background: Setup course and sample plugins
    Given the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@asd.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Quiz" to section "1" and I fill the form with:
      | Name | Test quiz              |
      | Description | Test forum description |
      | timeopen[enabled] | 1 |
      | timeopen[day]       | 1 |
      | timeopen[month]     | January |
      | timeopen[year]      | 2020 |
      | timeopen[hour]      | 08 |
      | timeopen[minute]    | 00 |
    Given I log out

  @javascript @_switch_iframe
  Scenario: Test edit dates report to see if timeline view shows
    Given the following config values are set as admin:
    | timelinemax | 1 | report_editdates |
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Dates" in current page administration
    And I follow "Dates"
    And I should see "12/31/2019"
    And I should see "1/1/2020"
    And I should see "1/2/2020"

  @javascript @_switch_iframe
  Scenario: Test edit dates report to see if timeline view is hidden
    Given the following config values are set as admin:
    | timelinemax | 0 | report_editdates |
    When I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Reports > Dates" in current page administration
    And I follow "Dates"
    And I should not see "12/31/2019"
    And I should not see "1/1/2020"
    And I should not see "1/2/2020"
