@block @block_xp @javascript
Feature: A report displays students' progress
  In order to know how my students are doing
  As a teacher
  I can visit the report

  Scenario: Report with visible groups and resetting data
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | Student   | One      | s1@example.com |
      | s2       | Student   | Two      | s2@example.com |
      | s3       | Student   | Three    | s3@example.com |
      | t1       | Teacher   | One      | t1@example.com |
    And the following "courses" exist:
      | fullname  | shortname | groupmode      |
      | Course 1  | c1        | 2 |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | s1       | c1     | student |
      | s2       | c1     | student |
      | s3       | c1     | student |
      | t1       | c1     | editingteacher |
    And the following "groups" exist:
      | name     | course | idnumber |
      | Group A  | c1     | ga |
      | Group B  | c1     | gb |
    And the following "group members" exist:
      | user | group |
      | s1   | ga    |
      | s2   | gb    |
    And the following "activity" exists:
      | course    | c1                             |
      | section   | 1                              |
      | activity  | forum                          |
      | name      | Test forum name                |
      | forumtype | Standard forum for general use |
      | intro     | Test forum description         |
    And I log in as "t1"
    And I am on front page
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add the "Level Up XP" block
    And I log out
    And I log in as "s1"
    And I am on front page
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion one    |
      | Message | This is the body  |
    And I log out
    And I log in as "s2"
    And I am on front page
    And I am on "Course 1" course homepage
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Discussion two    |
      | Message | This is the body  |
    And I reply "Discussion two" post from "Test forum name" forum with:
      | Subject | Reply with text   |
      | Message | This is the body  |
    And I log out
    And I log in as "s3"
    And I am on front page
    And I am on "Course 1" course homepage
    And I log out
    And I log in as "t1"
    And I am on front page
    And I am on "Course 1" course homepage
    When I click on "Report" "link" in the "Level up!" "block"
    Then the following should exist in the "block_xp-report-table" table:
      | First name    | Level | Total |
      | Student One   | 1     | 63    |
      | Student Two   | 1     | 117   |
      | Student Three | 1     | 9     |
    And I set the field "Visible groups" to "Group A"
    And I should not see "Student Two"
    And the following should exist in the "block_xp-report-table" table:
      | First name  | Level | Total |
      | Student One | 1     | 63    |
    And I set the field "Visible groups" to "Group B"
    And I should not see "Student One"
    And the following should exist in the "block_xp-report-table" table:
      | First name  | Level | Total |
      | Student Two | 1     | 117   |
    And I follow "Reset group data" in the XP page menu
    And I press "Reset"
    And the following should exist in the "block_xp-report-table" table:
      | First name  | Level | Total |
      | Student Two | -     | -     |
    And I set the field "Visible groups" to "All participants"
    And the following should exist in the "block_xp-report-table" table:
      | First name    | Level | Total |
      | Student One   | 1     | 63    |
      | Student Two   | -     | -     |
      | Student Three | 1     | 9     |
    And I follow "Reset course data" in the XP page menu
    And I press "Reset"
    And the following should exist in the "block_xp-report-table" table:
      | First name    | Level | Total |
      | Student One   | -     | -     |
      | Student Two   | -     | -     |
      | Student Three | -     | -     |

  Scenario: Use the report to edit a student's total
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | s1       | Student   | One      | s1@example.com |
      | s2       | Student   | Two      | s2@example.com |
      | t1       | Teacher   | One      | t1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | s1       | c1     | student |
      | s2       | c1     | student |
      | t1       | c1     | editingteacher |
    And I log in as "t1"
    And I am on front page
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add the "Level Up XP" block
    And I click on "Report" "link" in the "Level up!" "block"
    And the following should exist in the "block_xp-report-table" table:
      | First name    | Level | Total |
      | Student One   | -     | -     |
      | Student Two   | -     | -     |
    # Click on the edit button for Student One.
    And I follow edit for "Student One" in XP report
    # And I click on "td[normalize-space(.)='Student One']/parent::tr/descendant::img[@title='Edit']/parent::a" "xpath"
    And I wait until "Edit Student One" "dialogue" exists
    When I set the field "Total" to "512"
    And I press "Save changes"
    Then the following should exist in the "block_xp-report-table" table:
      | First name    | Level | Total |
      | Student One   | 4     | 512   |
      | Student Two   | -     | -     |
