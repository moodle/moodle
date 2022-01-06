@mod @mod_assign
Feature: Remove a submission
  In order to restart an assignment for a student
  As a teacher
  I need to remove a student submission at any time

  Background:
    Given I log in as "admin"
    And I set the following system permissions of "Teacher" role:
      | capability                     | permission |
      | mod/assign:editothersubmission | Allow      |
    And I log out
    And the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 0 |
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
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    And the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G1 |

  @javascript @skip_chrome_zerosize
  Scenario: Remove a submission should remove the data that was submitted
    Given the following "activity" exists:
      | activity                             | assign                |
      | course                               | C1                    |
      | name                                 | Test assignment name  |
      | submissiondrafts                     | 0                     |
      | assignsubmission_onlinetext_enabled  | 1                     |
      | assignsubmission_file_enabled        | 0                     |
      | submissiondrafts                     | 0                     |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                  |
      | Test assignment name  | student1  | I'm the student submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    And I open the action menu in "Student 1" "table_row"
    When I follow "Remove submission"
    And I click on "Continue" "button"
    Then I should not see "I'm the student submission"
    And "Student 1" row "Status" column of "generaltable" table should contain "No submission"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student1
    And I should not see "I'm the student submission"
    And I should see "No attempt" in the "Submission status" "table_row"

  @javascript @skip_chrome_zerosize
  Scenario: Remove a group submission should remove the data from all group members
    Given the following "activity" exists:
      | activity                             | assign                |
      | course                               | C1                    |
      | name                                 | Test assignment name  |
      | submissiondrafts                     | 0                     |
      | assignsubmission_onlinetext_enabled  | 1                     |
      | assignsubmission_file_enabled        | 0                     |
      | teamsubmission                       | 1                     |
      | submissiondrafts                     | 0                     |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                  |
      | Test assignment name  | student1  | I'm the student submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    And I open the action menu in "Student 1" "table_row"
    When I follow "Remove submission"
    And I click on "Continue" "button"
    Then I should not see "I'm the student submission"
    And "Student 1" row "Status" column of "generaltable" table should contain "No submission"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I should not see "I'm the student submission"
    And I should see "Nothing has been submitted for this assignment" in the "Submission status" "table_row"

  @javascript @skip_chrome_zerosize
  Scenario: A student can remove their own submission
    Given the following "activity" exists:
      | activity                             | assign                |
      | course                               | C1                    |
      | name                                 | Test assignment name  |
      | submissiondrafts                     | 0                     |
      | assignsubmission_onlinetext_enabled  | 1                     |
      | assignsubmission_file_enabled        | 0                     |
      | submissiondrafts                     | 0                     |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                  |
      | Test assignment name  | student1  | I'm the student submission  |

    And I am on the "Test assignment name" Activity page logged in as student1
    And I click on "Remove submission" "button"
    And I click on "Continue" "button"
    And I log out

    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    Then I should not see "I'm the student submission"
    And "Student 1" row "Status" column of "generaltable" table should contain "No submission"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student1
    And I should not see "I'm the student submission"
    And I should see "No attempt" in the "Submission status" "table_row"
