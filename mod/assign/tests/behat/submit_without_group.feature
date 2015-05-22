@mod @mod_assign
Feature: Submit assignment without group
  As a teacher
  I should be able to prevent students submitting team assignments as members of the default group

  @javascript
  Scenario: Switch between group modes
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
      | Course 2 | C2        | 0        | 1         |
    And the following "activities" exist:
      | activity | course | idnumber | name                     | intro                       | assignsubmission_onlinetext_enabled | preventsubmissionnotingroup | teamsubmission |
      | assign   | C1     | assign1  | Allow default group      | Test assignment description | 1                                   | 0                           | 1              |
      | assign   | C1     | assign2  | Require group membership | Test assignment description | 1                                   | 1                           | 1              |
      | assign   | C2     | assign2  | Require group membership | Test assignment description | 1                                   | 1                           | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C2     | G1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
      | student2 | C2     | student        |
    And I log in as "teacher1"
    And I follow "Course 2"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    And I follow "Allow default group"
    Then I should not see "You're not a member of any group, please contact your teacher."
    And I should see "Nothing has been submitted for this assignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    Then I should see "Submitted for grading"
    And I follow "Course 1"
    And I follow "Require group membership"
    Then I should see "You're not a member of any group, please contact your teacher."
    And I should see "Nothing has been submitted for this assignment"
    And I should not see "Add submission"
    And I am on homepage
    And I follow "Course 2"
    And I follow "Require group membership"
    Then I should not see "You're not a member of any group, please contact your teacher."
    And I should see "Nothing has been submitted for this assignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    Then I should see "Submitted for grading"
    And I log out
    When I log in as "student2"
    And I follow "Course 1"
    And I follow "Allow default group"
    Then I should see "Submitted for grading"
    And I am on homepage
    And I follow "Course 2"
    And I follow "Require group membership"
    Then I should see "Submitted for grading"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Allow default group"
    And I should see "1" in the "Groups" "table_row"
    And I should not see "The setting 'Require group to make submission' is turned on and some users are not allocated to groups, this will prevent them from submitting assignments."
    And I follow "View/grade all submissions"
    And I should see "Default group" in the "Student 1" "table_row"
    And I should see "Default group" in the "Student 2" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 2" "table_row"
    And I am on homepage
    And I follow "Course 1"
    And I follow "Require group membership"
    And I should see "0" in the "Groups" "table_row"
    And I should see "The setting 'Require group to make submission' is turned on and some users are not allocated to groups, this will prevent them from submitting assignments."
    And I follow "View/grade all submissions"
    And I should see "Default group" in the "Student 1" "table_row"
    And I should see "Default group" in the "Student 2" "table_row"
    And I should not see "Submitted for grading" in the "Student 1" "table_row"
    And I should not see "Submitted for grading" in the "Student 2" "table_row"
    And I am on homepage
    And I follow "Course 2"
    And I follow "Require group membership"
    And I should see "1" in the "Groups" "table_row"
    And I should not see "The setting 'Require group to make submission' is turned on and some users are not allocated to groups, this will prevent them from submitting assignments."
    And I follow "View/grade all submissions"
    And I should see "Group 1" in the "Student 1" "table_row"
    And I should see "Group 1" in the "Student 2" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 2" "table_row"
