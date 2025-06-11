@mod @mod_assign
Feature: Bulk remove submissions
  In order to reset the assignment submission of multiple students
  As a teacher with the capability to edit submissions
  I need to be able to remove student submissions by bulk

  Background:
    Given the following "courses" exist:
      | fullname  | shortname  | category  | groupmode  |
      | Course 1  | C1         | 0         | 0          |
    And the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | student1  | Student    | 1         | student1@example.com  |
      | student2  | Student    | 2         | student2@example.com  |
      | student3  | Student    | 3         | student3@example.com  |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | student1  | C1      | student         |
      | student2  | C1      | student         |
      | student3  | C1      | student         |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |

  @javascript @skip_chrome_zerosize
  Scenario: Bulk remove submissions should remove the data that was submitted
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | submissiondrafts                    | 0                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |
      | Test assignment name  | student2  | I'm the student2 submission  |
    And the following "role capability" exists:
      | role                           | editingteacher |
      | mod/assign:editothersubmission | allow          |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I set the field "selectall" to "1"
    And I click on "Delete" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Remove submission" "dialogue"
    Then I should not see "I'm the student1 submission"
    And I should not see "I'm the student2 submission"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student1
    And I should not see "I'm the student1 submission"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as student2
    And I should not see "I'm the student2 submission1"

  @javascript
  Scenario: Bulk remove submissions should be unavailable if the user is missing the editing submission capability
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | intro                               | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | submissiondrafts                    | 0                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |
      | Test assignment name  | student2  | I'm the student2 submission  |

    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I set the field "selectall" to "1"
    Then I should not see "Delete" in the "sticky-footer" "region"

  @javascript @skip_chrome_zerosize
  Scenario: Bulk remove submission when shared group users are added to the bulk
    removing submissions process in separate group mode without access all groups capability
    Given the following "group members" exist:
      | user      | group  |
      | teacher1  | G1     |
      | student1  | G1     |
      | student2  | G1     |
    Given the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | assignsubmission_onlinetext_enabled | 1                       |
      | assignsubmission_file_enabled       | 0                       |
      | groupmode                           | 1                       |
      | submissiondrafts                    | 0                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |
      | Test assignment name  | student2  | I'm the student2 submission  |
      | Test assignment name  | student3  | I'm the student3 submission  |
    And the following "role capability" exists:
      | role                           | editingteacher |
      | mod/assign:editothersubmission | allow          |
      | moodle/site:accessallgroups    | prevent        |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I should not see "I'm the student3 submission"
    And I set the field "selectall" to "1"
    When I click on "Delete" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Remove submission" "dialogue"

    Then I should not see "I'm the student1 submission"
    Then I should not see "I'm the student2 submission"

  @javascript @skip_chrome_zerosize
  Scenario: Bulk remove submission when group users and non-group users are added to the bulk
    removing submissions process in separate group mode with access all groups capability
    Given the following "group members" exist:
      | user      | group  |
      | student1  | G1     |
      | student2  | G1     |
    And the following "activity" exists:
      | activity                             | assign                |
      | course                               | C1                    |
      | name                                 | Test assignment name  |
      | assignsubmission_onlinetext_enabled  | 1                     |
      | assignsubmission_file_enabled        | 0                     |
      | groupmode                            | 1                     |
      | submissiondrafts                     | 0                     |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |
      | Test assignment name  | student2  | I'm the student2 submission  |
      | Test assignment name  | student3  | I'm the student3 submission  |
    And the following "role capability" exists:
      | role                           | editingteacher |
      | mod/assign:editothersubmission | allow          |
      | moodle/site:accessallgroups    | allow          |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I should see "I'm the student1 submission"
    And I should see "I'm the student2 submission"
    And I should see "I'm the student3 submission"
    And I set the field "selectall" to "1"
    When I click on "Delete" "button" in the "sticky-footer" "region"
    And I click on "Delete" "button" in the "Remove submission" "dialogue"
    Then I should not see "I'm the student1 submission"
    And I should not see "I'm the student2 submission"
    And I should not see "I'm the student3 submission"
