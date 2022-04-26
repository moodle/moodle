@core @core_course
Feature: Report navigation
  As a teacher
  I will be redirected to the first report page on the navigation if I can't access to other reports.

  Background:
    Given the following "users" exist:
      | username |
      | teacher  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C1     | editingteacher |

  Scenario: The teacher will be redirected to the first report page if they can't access to most recently report
    Given I am on the "C1" "Course" page logged in as "teacher"
    When I navigate to "Reports > Logs" in current page administration
    Then I should see "Choose which logs you want to see"
    When I click on "Reports" "link"
    Then I should see "Choose which logs you want to see"
    When  the following "permission overrides" exist:
      | capability      | permission | role           | contextlevel | reference |
      | report/log:view | Prohibit   | editingteacher | System       |           |
    And I click on "Reports" "link"
    Then I should see "Competency breakdown"
    And I should not see "Sorry, but you do not currently have permissions to do that"

  Scenario: If capability Log view is unset, the teacher will be redirected to the first valid report page
    Given the following "permission overrides" exist:
      | capability      | permission | role           | contextlevel | reference |
      | report/log:view | Prohibit   | editingteacher | System       |           |
    When I am on the "C1" "Course" page logged in as "teacher"
    And I navigate to "Reports" in current page administration
    Then I should see "Competency breakdown"
    And I should not see "Sorry, but you do not currently have permissions to do that"

  Scenario: A warning message will be shown if the user cannot access any report page
    Given the following "permission overrides" exist:
      | capability                             | permission | role           | contextlevel | reference |
      | report/log:view                        | Prohibit   | editingteacher | System       |           |
      | report/loglive:view                    | Prohibit   | editingteacher | System       |           |
      | report/outline:view                    | Prohibit   | editingteacher | System       |           |
      | report/participation:view              | Prohibit   | editingteacher | System       |           |
      | moodle/competency:coursecompetencyview | Prohibit   | editingteacher | System       |           |
    When I am on the "C1" "Course" page logged in as "teacher"
    And I navigate to "Reports" in current page administration
    Then I should see "No reports accessible"
