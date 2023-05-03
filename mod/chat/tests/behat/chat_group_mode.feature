@mod @mod_chat
Feature: Chat with my group
  In order to chat with my group
  As a student
  I want to be able to see or select by group in the Chat activity

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
      | username | firstname    | lastname | email                |
      | teacher1 | TeacherG1    | 1        | teacher1@example.com |
      | user1    | User1G1      | 1        | user1@example.com    |
      | user2    | User2G2      | 2        | user2@example.com    |
      | user3    | User3None    | 3        | user3@example.com    |
      | user4    | User4NPgroup | 4        | user4@example.com    |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | user1    | C1     | student        |
      | user2    | C1     | student        |
      | user3    | C1     | student        |
      | user4    | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
      | user1    | G1    |
      | user2    | G2    |
      | user4    | G3    |
    And the following "activities" exist:
      | activity | name          | intro                      | course | idnumber | groupmode |
      | chat     | Separate Chat | Chat with separate groups  | C1     | chat1    | 1         |
      | chat     | Visible Chat  | Chat with visible groups   | C1     | chat2    | 2         |

  Scenario Outline: Users should see their own participation groups in "separate groups" mode, and all
  participation groups in "visible groups" mode.
    Given I am on the "<chat>" "chat activity" page logged in as "<user>"
    Then I <all> "All participants"
    And I <G1> "Group 1"
    And I <G2> "Group 2"
    And I should not see "Group 3"

    Examples:
      | chat  | user     | all            | G1             | G2             |
      | chat1 | teacher1 | should see     | should see     | should see     |
      | chat1 | user1    | should not see | should see     | should not see |
      | chat1 | user2    | should not see | should not see | should see     |
      | chat1 | user3    | should see     | should not see | should not see |
      | chat1 | user4    | should see     | should not see | should not see |
      | chat2 | teacher1 | should see     | should see     | should see     |
      | chat2 | user1    | should see     | should see     | should see     |
      | chat2 | user2    | should see     | should see     | should see     |
      | chat2 | user3    | should see     | should see     | should see     |
      | chat2 | user4    | should see     | should see     | should see     |
