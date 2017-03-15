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
      | Course 3 | C3        | 0        | 1         |
    And the following "activities" exist:
      | activity | course | idnumber | name                     | intro                       | assignsubmission_onlinetext_enabled | preventsubmissionnotingroup | teamsubmission |
      | assign   | C1     | assign1  | Allow default group      | Test assignment description | 1                                   | 0                           | 1              |
      | assign   | C1     | assign2  | Require group membership | Test assignment description | 1                                   | 1                           | 1              |
      | assign   | C2     | assign2  | Require group membership | Test assignment description | 1                                   | 1                           | 1              |
      | assign   | C3     | assign2  | Require group membership | Test assignment description | 1                                   | 1                           | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C2     | GC21     |
      | Group 1 | C3     | GC31     |
      | Group 2 | C3     | GC32     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | teacher1 | C2     | editingteacher |
      | student1 | C2     | student        |
      | student2 | C2     | student        |
      | teacher1 | C3     | editingteacher |
      | student3 | C3     | student        |
    And the following "group members" exist:
      | user     | group |
      | student1 | GC21  |
      | student2 | GC21  |
      | student3 | GC31  |
      | student3 | GC32  |
    # Student 1 can only submit assignment in course 2.
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Allow default group"
    Then I should not see "Not a member of any group"
    And I should not see "This assignment requires submission in groups. You are not a member of any group"
    And I should see "Nothing has been submitted for this assignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I should see "Submitted for grading"
    And I am on "Course 1" course homepage
    And I follow "Require group membership"
    And I should see "Not a member of any group"
    And I should see "This assignment requires submission in groups. You are not a member of any group"
    And I should see "Nothing has been submitted for this assignment"
    And I should not see "Add submission"
    And I am on "Course 2" course homepage
    And I follow "Require group membership"
    And I should not see "Not a member of any group"
    And I should see "Nothing has been submitted for this assignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I should see "Submitted for grading"
    And I log out
    # Student 2 should see submitted for grading.
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Allow default group"
    And I should see "Submitted for grading"
    And I am on "Course 2" course homepage
    And I follow "Require group membership"
    And I should see "Submitted for grading"
    And I log out
    # Teacher should see student 1 and student 2 has submitted assignment.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Allow default group"
    And I should see "1" in the "Groups" "table_row"
    And I should not see "The setting 'Require group to make submission\' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I navigate to "View all submissions" in current page administration
    And I should see "Default group" in the "Student 1" "table_row"
    And I should see "Default group" in the "Student 2" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 2" "table_row"
    And I am on "Course 1" course homepage
    And I follow "Require group membership"
    And I should see "0" in the "Groups" "table_row"
    And I should see "The setting 'Require group to make submission' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I navigate to "View all submissions" in current page administration
    And I should see "Not a member of any group, so unable to make submissions." in the "Student 1" "table_row"
    And I should see "Not a member of any group, so unable to make submissions." in the "Student 2" "table_row"
    And I should not see "Submitted for grading" in the "Student 1" "table_row"
    And I should not see "Submitted for grading" in the "Student 2" "table_row"
    And I am on "Course 2" course homepage
    And I follow "Require group membership"
    And I should see "1" in the "Groups" "table_row"
    And I should not see "The setting 'Require group to make submission' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I navigate to "View all submissions" in current page administration
    And I should see "Group 1" in the "Student 1" "table_row"
    And I should see "Group 1" in the "Student 2" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 2" "table_row"
    And I log out
    # Test student 3 (in multiple groups) should not be able to submit.
    And I log in as "student3"
    And I am on "Course 3" course homepage
    And I follow "Require group membership"
    And I should see "Member of more than one group"
    And I should see "The assignment requires submission in groups. You are a member of more than one group."
    And I should see "Nothing has been submitted for this assignment"
    And I should not see "Add submission"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 3" course homepage
    And I follow "Require group membership"
    And I should see "The setting 'Require group to make submission' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I navigate to "View all submissions" in current page administration
    And I should see "Member of more than one group, so unable to make submissions." in the "Student 3" "table_row"
