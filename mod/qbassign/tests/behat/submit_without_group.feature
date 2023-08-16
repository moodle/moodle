@mod @mod_qbassign
Feature: Submit qbassignment without group
  As a teacher
  I should be able to prevent students submitting team qbassignments as members of the default group

  @javascript
  Scenario: Switch between group modes
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
      | Course 2 | C2        | 0        | 1         |
      | Course 3 | C3        | 0        | 1         |
    And the following "activities" exist:
      | activity | course | idnumber  | name                     | intro                       | qbassignsubmission_onlinetex_enabled | preventsubmissionnotingroup | teamsubmission |
      | qbassign   | C1     | c1qbassign1 | Allow default group      | Test qbassignment description | 1                                   | 0                           | 1              |
      | qbassign   | C1     | c1qbassign2 | Require group membership | Test qbassignment description | 1                                   | 1                           | 1              |
      | qbassign   | C2     | c2qbassign1 | Require group membership | Test qbassignment description | 1                                   | 1                           | 1              |
      | qbassign   | C3     | c3qbassign1 | Require group membership | Test qbassignment description | 1                                   | 1                           | 1              |
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
    # Student 1 can only submit qbassignment in course 2.
    When I am on the "c1qbassign1" "qbassign activity" page logged in as student1
    Then I should not see "Not a member of any group"
    And I should not see "This qbassignment requires submission in groups. You are not a member of any group"
    And I should see "Nothing has been submitted for this qbassignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit qbassignment"
    And I press "Continue"
    And I should see "Submitted for grading"
    And I am on the "c1qbassign2" "qbassign activity" page
    And I should see "Not a member of any group"
    And I should see "This qbassignment requires submission in groups. You are not a member of any group"
    And I should see "Nothing has been submitted for this qbassignment"
    And I should not see "Add submission"
    And I am on the "c2qbassign1" "qbassign activity" page
    And I should not see "Not a member of any group"
    And I should see "Nothing has been submitted for this qbassignment"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student submission |
    And I press "Save changes"
    And I press "Submit qbassignment"
    And I press "Continue"
    And I should see "Submitted for grading"
    And I log out
    # Student 2 should see submitted for grading.
    And I am on the "c1qbassign1" "qbassign activity" page logged in as student2
    And I should see "Submitted for grading"
    And I am on the "c2qbassign1" "qbassign activity" page
    And I should see "Submitted for grading"
    And I log out
    # Teacher should see student 1 and student 2 has submitted qbassignment.
    And I am on the "c1qbassign1" "qbassign activity" page logged in as teacher1
    And I should see "1" in the "Groups" "table_row"
    And I should not see "The setting 'Require group to make submission\' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I follow "View all submissions"
    And I should see "Default group" in the "Student 1" "table_row"
    And I should see "Default group" in the "Student 2" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 2" "table_row"
    And I am on the "c1qbassign2" "qbassign activity" page
    And I should see "0" in the "Groups" "table_row"
    And I should see "The setting 'Require group to make submission' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I follow "View all submissions"
    And I should see "Not a member of any group, so unable to make submissions." in the "Student 1" "table_row"
    And I should see "Not a member of any group, so unable to make submissions." in the "Student 2" "table_row"
    And I should not see "Submitted for grading" in the "Student 1" "table_row"
    And I should not see "Submitted for grading" in the "Student 2" "table_row"
    And I am on the "c2qbassign1" "qbassign activity" page
    And I should see "1" in the "Groups" "table_row"
    And I should not see "The setting 'Require group to make submission' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I follow "View all submissions"
    And I should see "Group 1" in the "Student 1" "table_row"
    And I should see "Group 1" in the "Student 2" "table_row"
    And I should see "Submitted for grading" in the "Student 1" "table_row"
    And I should see "Submitted for grading" in the "Student 2" "table_row"
    And I log out
    # Test student 3 (in multiple groups) should not be able to submit.
    And I am on the "c3qbassign1" "qbassign activity" page logged in as student3
    And I should see "Member of more than one group"
    And I should see "The qbassignment requires submission in groups. You are a member of more than one group."
    And I should see "Nothing has been submitted for this qbassignment"
    And I should not see "Add submission"
    And I log out
    And I am on the "c3qbassign1" "qbassign activity" page logged in as teacher1
    And I should see "The setting 'Require group to make submission' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I follow "View all submissions"
    And I should see "Member of more than one group, so unable to make submissions." in the "Student 3" "table_row"

  Scenario: All users are in groups, so no warning messages needed.
    Given the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 0         |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro                       | qbassignsubmission_onlinetex_enabled | preventsubmissionnotingroup | teamsubmission |
      | qbassign   | C1     | qbassign1  | Allow default group | Test qbassignment description | 1                                   | 0                           | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G2    |
    When I am on the "Allow default group" "qbassign activity" page logged in as teacher1
    Then I should not see "The setting 'Require group to make submission\' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I should not see "The setting 'Students submit in groups' is enabled and some users are either not a member of any group, or are a member of more than one group. Please be aware that these students will submit as members of the 'Default group'."

  Scenario: One user is not in a group, so should see a warning about default group submission
    Given the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 0         |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro                       | qbassignsubmission_onlinetex_enabled | preventsubmissionnotingroup | teamsubmission |
      | qbassign   | C1     | qbassign1  | Allow default group | Test qbassignment description | 1                                   | 0                           | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
    When I am on the "Allow default group" "qbassign activity" page logged in as teacher1
    Then I should not see "The setting 'Require group to make submission\' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I should see "The setting 'Students submit in groups' is enabled and some users are either not a member of any group, or are a member of more than one group. Please be aware that these students will submit as members of the 'Default group'."

  Scenario: One user is a member of multiple groups, so should see a warning about default group submission
    Given the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 0         |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro                       | qbassignsubmission_onlinetex_enabled | preventsubmissionnotingroup | teamsubmission |
      | qbassign   | C1     | qbassign1  | Allow default group | Test qbassignment description | 1                                   | 0                           | 1              |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
      | student2 | G2    |
    When I am on the "Allow default group" "qbassign activity" page logged in as teacher1
    Then I should not see "The setting 'Require group to make submission\' is enabled and some users are either not a member of any group, or are a member of more than one group, so are unable to make submissions."
    And I should see "The setting 'Students submit in groups' is enabled and some users are either not a member of any group, or are a member of more than one group. Please be aware that these students will submit as members of the 'Default group'."
