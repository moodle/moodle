@mod @mod_survey
Feature: Viewing response reports by group
  In order to view reponse reports on a large course
  As a teacher
  I need to filter the users on the response reports by group

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
      | noneditor1 | NoneditorG1   | 1        | noneditor1@example.com |
      | noneditor2 | NoneditorNone | 2        | noneditor2@example.com |
      | user1      | User1G1       | 1        | user1@example.com      |
      | user2      | User2G2       | 2        | user2@example.com      |
      | user3      | User3None     | 3        | user3@example.com      |
      | user4      | User4NPgroup  | 4        | user4@example.com      |
    And the following "course enrolments" exist:
      | user       | course | role           |
      | teacher1   | C1     | editingteacher |
      | noneditor1 | C1     | teacher        |
      | noneditor2 | C1     | teacher        |
      | user1      | C1     | student        |
      | user2      | C1     | student        |
      | user3      | C1     | student        |
      | user4      | C1     | student        |
    And the following "group members" exist:
      | user       | group |
      | teacher1   | G1    |
      | noneditor1 | G1    |
      | user1      | G1    |
      | user2      | G2    |
      | user4      | G3    |
    And the following "activities" exist:
      | activity   | name            | intro                       | course | idnumber   | groupmode | template |
      | survey     | Separate survey | survey with separate groups | C1     | survey1    | 1         | 5        |
      | survey     | Visible survey  | survey with visible groups  | C1     | survey2    | 2         | 5        |
    And I am on the "Separate survey" "survey activity" page logged in as user1
    And I press "Submit"
    And I am on the "Separate survey" "survey activity" page logged in as user2
    And I press "Submit"
    And I am on the "Separate survey" "survey activity" page logged in as user3
    And I press "Submit"
    And I am on the "Separate survey" "survey activity" page logged in as user4
    And I press "Submit"
    And I am on the "Visible survey" "survey activity" page logged in as user1
    And I press "Submit"
    And I am on the "Visible survey" "survey activity" page logged in as user2
    And I press "Submit"
    And I am on the "Visible survey" "survey activity" page logged in as user3
    And I press "Submit"
    And I am on the "Visible survey" "survey activity" page logged in as user4
    And I press "Submit"

  Scenario Outline: Editing teachers should see all groups on the Results page. Non-editing teachers should see just their own
    groups in Separate groups mode, all groups in Visible groups mode.
    Given I am on the "<survey>" "survey activity" page logged in as "<user>"
    And I follow "Response reports"
    Then I <all> see "All participants"
    And I <G1> see "Group 1"
    And I <G2> see "Group 2"
    And I should not see "Group 3"
    And I <user1> see "User1G1"
    And I <user2> see "User2G2"
    And I <user3> see "User3None"
    And I <user4> see "User4NPgroup"

    Examples:
      | survey  | user       | all        | G1         | G2         | user1  | user2      | user3      | user4      |
      | survey1 | teacher1   | should     | should     | should     | should | should     | should     | should     |
      | survey1 | noneditor1 | should not | should     | should not | should | should not | should not | should not |
      | survey2 | teacher1   | should     | should     | should     | should | should     | should     | should     |
      | survey2 | noneditor1 | should     | should     | should     | should | should not | should not | should not |
      | survey2 | noneditor2 | should     | should     | should     | should | should not | should not | should not |

  Scenario: Non-editing teacher without access to any groups should not see survey results in separate groups mode
    Given I am on the "survey1" "survey activity" page logged in as "noneditor2"
    Then I should not see "Response reports"
