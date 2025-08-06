@report @report_log
Feature: In a course with group mode, I can view the group report log page or not
  depending on the group I am in.

  Background:
    Given the following "courses" exist:
      | fullname              | shortname | category | groupmode |
      | Course separate group | C1        | 0        | 1         |
      | Course visible group  | C2        | 0        | 2         |
      | Course no group       | C3        | 0        | 0         |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | C1     | teacher |
      | teacher2 | C1     | teacher |
      | student1 | C1     | student |
      | student2 | C1     | student |
      | teacher1 | C2     | teacher |
      | teacher2 | C2     | teacher |
      | student1 | C2     | student |
      | student2 | C2     | student |
      | teacher1 | C3     | teacher |
      | teacher2 | C3     | teacher |
      | student1 | C3     | student |
      | student2 | C3     | student |
    And the following "groups" exist:
      | course | name       | idnumber |
      | C1     | Group C1.1 | group11  |
      | C2     | Group C2.1 | group21  |
      | C3     | Group C3.1 | group31  |
    And the following "group members" exist:
      | group   | user     |
      | group11 | student1 |
      | group11 | teacher1 |
      | group21 | student1 |
      | group21 | teacher1 |
      | group31 | student1 |
      | group31 | teacher1 |
    And the following "activities" exist:
      | activity | name   | intro  | course | idnumber |
      | page     | Page11 | Page11 | C1     | page1    |
      | page     | Page21 | Page21 | C2     | page1    |
      | page     | Page31 | Page31 | C3     | page1    |
    # Generate logs for the pages.
    And I am on the "Page11" "page activity" page logged in as student1
    And I am on "Course separate group" course homepage
    And I log out
    And I am on the "Page11" "page activity" page logged in as student2
    And I am on "Course separate group" course homepage
    And I log out
    And I am on the "Page21" "page activity" page logged in as student1
    And I am on "Course visible group" course homepage
    And I log out
    And I am on the "Page21" "page activity" page logged in as student2
    And I am on "Course visible group" course homepage
    And I log out
    And I am on the "Page31" "page activity" page logged in as student1
    And I am on "Course no group" course homepage
    And I log out
    And I am on the "Page31" "page activity" page logged in as student2
    And I am on "Course no group" course homepage
    And I log out

  Scenario Outline: As a user in a course, I can view a link to the report logs if I am in the right group.
    Given I log in as "<user>"
    And I am on "<course>" course homepage
    When I navigate to "Reports" in current page administration
    And "Logs" "link" <shouldexist>
    And I log out
    Examples:
      | course                | user     | shouldexist      |
      | Course separate group | teacher1 | should exist     |
      | Course separate group | teacher2 | should not exist |
      | Course visible group  | teacher1 | should exist     |
      | Course visible group  | teacher2 | should exist     |
      | Course no group       | teacher1 | should exist     |
      | Course no group       | teacher2 | should exist     |

  Scenario Outline: As a non editing teacher not in a group, I can not view the report logs.
    Given I log in as "<user>"
    When I am on the "<course>" "report_log > Logs" page
    Then I <shouldsee> "you need to be part of a group to see this page."
    Examples:
      | course                | user     | shouldsee      |
      | Course separate group | teacher1 | should not see |
      | Course separate group | teacher2 | should see     |
      | Course visible group  | teacher1 | should not see |
      | Course visible group  | teacher2 | should not see |
      | Course no group       | teacher1 | should not see |
      | Course no group       | teacher2 | should not see |
