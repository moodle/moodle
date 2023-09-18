@mod @mod_quiz
Feature: Viewing results by group
  In order to view quiz results on a large course
  As a teacher
  I need to filter results by group

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
      | activity | name          | intro                     | course | idnumber | groupmode |
      | quiz     | Separate quiz | quiz with separate groups | C1     | quiz1    | 1         |
      | quiz     | Visible quiz  | quiz with visible groups  | C1     | quiz2    | 2         |
    And the following "question categories" exist:
      | contextlevel | reference | name           |
      | Course       | C1        | Test questions |
    And the following "questions" exist:
      | questioncategory | qtype       | name  | questiontext    |
      | Test questions   | truefalse   | TF1   | First question  |
      | Test questions   | truefalse   | TF2   | Second question |
    And quiz "Separate quiz" contains the following questions:
      | question | page |
      | TF1      | 1    |
    And quiz "Visible quiz" contains the following questions:
      | question | page |
      | TF1      | 1    |

  Scenario Outline: Editing teachers should see all groups on the Results page. Non-editing teachers should see just their own
    groups in Separate groups mode, all groups in Visible groups mode.
    Given I am on the "<quiz>" "quiz activity" page logged in as "<user>"
    And I follow "Results"
    Then I <all> "All participants"
    And I <G1> "Group 1"
    And I <G2> "Group 2"
    And I <error> "Sorry, but you need to be part of a group to see this page."
    And I should not see "Group 3"

    Examples:
      | quiz  | user       | all            | G1             | G2             | error          |
      | quiz1 | teacher1   | should see     | should see     | should see     | should not see |
      | quiz1 | noneditor1 | should not see | should see     | should not see | should not see |
      | quiz1 | noneditor2 | should see     | should not see | should not see | should see     |
      | quiz2 | teacher1   | should see     | should see     | should see     | should not see |
      | quiz2 | noneditor1 | should see     | should see     | should see     | should not see |
      | quiz2 | noneditor2 | should see     | should see     | should see     | should not see |
