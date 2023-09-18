@report @report_outline
Feature: Filter an outline report
  In order to ensure the outline report works as expected
  As a teacher
  I need to log in as a teacher and view the outline report with various filters in place

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity | name       | course | idnumber | section |
      | forum    | Forum name | C1     | FORUM01  | 1       |
      | book     | Book name  | C1     | BOOK01   | 1       |
    When I am on the "Course 1" course page logged in as admin

  Scenario: Filter the outline report by start date
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I am on the "Forum name" "forum activity" page logged in as student1
    And the log timestamp for "student1" and "FORUM01" is set to "12 June 2017 12:49:00"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And the log timestamp for "student1" and "BOOK01" is set to "10 June 2017 14:01:00"
    And I am on the "Book name" "book activity" page logged in as student2
    And the log timestamp for "student2" and "BOOK01" is set to "14 June 2017 11:02:00"
    And I am on the "Course 1" course page logged in as admin
    And I navigate to "Reports" in current page administration
    And I click on "Activity report" "link"
    And I should see "2 views by 2 users" in the "Book name" "table_row"
    And I should see "1 views by 1 users" in the "Forum name" "table_row"
    When I set the following fields to these values:
      | filterstartdate[enabled] | 1    |
      | filterstartdate[day]     | 12   |
      | filterstartdate[month]   | June |
      | filterstartdate[year]    | 2017 |
    And I click on "Filter" "button" in the "#fgroup_id_buttonar" "css_element"
    Then I should see "1 views by 1 users" in the "Book name" "table_row"
    And I should see "1 views by 1 users" in the "Forum name" "table_row"

  Scenario: Filter the outline report by end date
    Given I navigate to "Plugins > Logging > Manage log stores" in site administration
    And "Disable" "link" should exist in the "Standard log" "table_row"
    And I am on the "Forum name" "forum activity" page logged in as student1
    And the log timestamp for "student1" and "FORUM01" is set to "12 June 2017 12:49:00"
    And I am on "Course 1" course homepage
    And I follow "Book name"
    And the log timestamp for "student1" and "BOOK01" is set to "10 June 2017 14:01:00"
    And I am on the "Book name" "book activity" page logged in as student2
    And the log timestamp for "student2" and "BOOK01" is set to "14 June 2017 11:02:00"
    And I am on the "Course 1" course page logged in as admin
    And I navigate to "Reports" in current page administration
    And I click on "Activity report" "link"
    And I should see "2 views by 2 users" in the "Book name" "table_row"
    And I should see "1 views by 1 users" in the "Forum name" "table_row"
    When I set the following fields to these values:
      | filterenddate[enabled] | 1    |
      | filterenddate[day]     | 11   |
      | filterenddate[month]   | June |
      | filterenddate[year]    | 2017 |
    And I click on "Filter" "button" in the "#fgroup_id_buttonar" "css_element"
    Then I should see "1 views by 1 users" in the "Book name" "table_row"
    And I should not see "views by" in the "Forum name" "table_row"
