@report @report_participation
Feature: In a participation report, admin can filter student actions
  In order to filter participation data
  As a student
  I need to log action and then log in as admin to view participation report

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | manager1 | Manager | 1 | manager1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | manager1 | C1 | manager |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | course      | C1             |
      | activity    | forum           |
      | name        | Test forum name |
      | idnumber    | forum1          |

  Scenario: Filter participation report when standard log reader is enabled later
    Given I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Disable" "link" in the "Standard log" "table_row"
    And I log out

    And I am on the "Test forum name" "forum activity" page logged in as student1
    And I log out

    And I log in as "admin"
    And I navigate to "Plugins > Logging > Manage log stores" in site administration
    And I click on "Enable" "link" in the "Standard log" "table_row"

    And I am on the "Test forum name" "forum activity" page logged in as student1

    And I am on the "Course 1" course page logged in as admin
    When I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the following fields to these values:
      | Activity module | Test forum name |
      | Show only       | Student         |
    And I press "Go"
    Then the following should exist in the "reporttable" table:
      | -1-       | All actions |
      | Student 1 | Yes (1)     |

  Scenario: Filter participation report when only standard log reader is enabled by default
    Given I am on the "Test forum name" "forum activity" page logged in as student1
    When I am on the "Course 1" course page logged in as admin
    And I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    And I set the following fields to these values:
      | Activity module | Test forum name |
      | Show only       | Student         |
    And I press "Go"
    Then the following should exist in the "reporttable" table:
      | -1-       | All actions |
      | Student 1 | Yes (1)     |

  Scenario Outline: Filter participation report by viewable roles
    Given I am on the "Course 1" course page logged in as "teacher1"
    When I navigate to "Reports" in current page administration
    And I click on "Course participation" "link"
    # Teacher role cannot see Manager by default.
    Then "Manager" "option" should not exist in the "Show only" "select"
    And I set the following fields to these values:
      | Activity module | Test forum name |
      | Show only       | <role>          |
    And I press "Go"
    And I should see "<uservisible>" in the "reporttable" "table"
    And I should not see "<usernonvisible>" in the "reporttable" "table"
    Examples:
      | role    | uservisible | usernonvisible |
      | Student | Student 1   | Teacher 1      |
      | Teacher | Teacher 1   | Student 1      |
