@mod @mod_workshop
Feature: Viewing workshop reports by group
  In order to manage workshops for separate groups
  As a teacher
  I need to select groups for grade reports and submission allocation

  Background:
    And the following "courses" exist:
      | fullname      | shortname |
      | Test Course 1 | C1        |
    And the following "groups" exist:
      | name    | course | idnumber | participation |
      | Group 1 | C1     | G1       | 1             |
      | Group 2 | C1     | G2       | 1             |
      | Group 3 | C1     | G3       | 0             |
    And the following "users" exist:
      | username   | firstname     | lastname | email                  |
      | teacher1   | TeacherG1     | 1        | teacher1@example.com   |
      | user1      | User1G1       | 1        | user1@example.com      |
      | user2      | User2G2       | 2        | user2@example.com      |
      | user3      | User3None     | 3        | user3@example.com      |
      | user4      | User4NPgroup  | 4        | user4@example.com      |
    And the following "course enrolments" exist:
      | user       | course | role           |
      | teacher1   | C1     | editingteacher |
      | user1      | C1     | student        |
      | user2      | C1     | student        |
      | user3      | C1     | student        |
      | user4      | C1     | student        |
    And the following "group members" exist:
      | user       | group |
      | teacher1   | G1    |
      | user1      | G1    |
      | user2      | G2    |
      | user4      | G3    |
    And the following "activities" exist:
      | activity | course | name              | submissiontypetext | idnumber    | groupmode |
      | workshop | C1     | Separate workshop | 1                  | workshop1   | 1         |
      | workshop | C1     | Visible workshop  | 1                  | workshop2   | 2         |

  Scenario Outline: Grade report should only show participation groups
    Given I am on the "<workshop>" "workshop activity" page logged in as "teacher1"
    And I follow "Switch to the next phase"
    And I press "Continue"
    And I should see "All participants" in the "<mode> groups" "select"
    And I should see "Group 1" in the "<mode> groups" "select"
    And I should see "Group 2" in the "<mode> groups" "select"
    And I should not see "Group 3" in the "<mode> groups" "select"
    And the field "<mode> groups" matches value "All participants"
    And I should see "User1G1"
    And I should see "User2G2"
    And I should see "User3None"
    And I should see "User4NPgroup"
    When I select "Group 1" from the "<mode> groups" singleselect
    Then I should see "User1G1"
    And I should not see "User2G2"
    And I should not see "User3None"
    And I should not see "User4NPgroup"

    Examples:
      | workshop  | mode     |
      | workshop1 | Separate |
      | workshop2 | Visible  |

  Scenario Outline: Submissions allocation page should only show participation groups
    Given I am on the "<workshop>" "workshop activity" page logged in as "teacher1"
    And I follow "Submissions allocation"
    And I should see "All participants" in the "<mode> groups" "select"
    And I should see "Group 1" in the "<mode> groups" "select"
    And I should see "Group 2" in the "<mode> groups" "select"
    And I should not see "Group 3" in the "<mode> groups" "select"
    And the field "<mode> groups" matches value "All participants"
    And I should see "User1G1"
    And I should see "User2G2"
    And I should see "User3None"
    And I should see "User4NPgroup"
    When I select "Group 1" from the "<mode> groups" singleselect
    Then I should see "User1G1"
    And I should not see "User2G2"
    And I should not see "User3None"
    And I should not see "User4NPgroup"

    Examples:
      | workshop  | mode     |
      | workshop1 | Separate |
      | workshop2 | Visible  |
