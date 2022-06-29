@mod @mod_assign
Feature: Group assignment submissions
  In order to allow students to work collaboratively on an assignment
  As a teacher
  I need to group submissions in groups

  @javascript
  Scenario: Switch between group modes
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student0 | Student | 0 | student0@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student0 | C1 | student |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "activity" exists:
      | activity         | assign                      |
      | course           | C1                          |
      | name             | Test assignment name        |
      | intro            | Test assignment description |
      | submissiondrafts | 0                           |
      | teamsubmission   | 1                           |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then "//tr[contains(., 'Student 0')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 1')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 2')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 3')][contains(., 'Default group')]" "xpath_element" should exist
    And I am on the "Test assignment name" "assign activity editing" page
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and return to course"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Group mode | Separate groups |
    And I press "Save and display"
    And the following "group members" exist:
      | user | group |
      | student0 | G1 |
      | student1 | G1 |
    And I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    And I set the field "Separate groups" to "Group 1"
    And "//tr[contains(., 'Student 0')][contains(., 'Group 1')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 1')][contains(., 'Group 1')]" "xpath_element" should exist
    And I should not see "Student 2"
    And I set the field "Separate groups" to "All participants"
    And "//tr[contains(., 'Student 0')][contains(., 'Group 1')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 1')][contains(., 'Group 1')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 2')][contains(., 'Default group')]" "xpath_element" should exist
    And "//tr[contains(., 'Student 3')][contains(., 'Default group')]" "xpath_element" should exist

  @javascript
  Scenario: Confirm that the grading status changes for each group member
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
    And the following "activity" exists:
      | activity                            | assign                      |
      | course                              | C1                          |
      | name                                | Test assignment name        |
      | intro                               | Test assignment description |
      | submissiondrafts                    | 0                           |
      | assignsubmission_onlinetext_enabled | 1                           |
      | assignsubmission_file_enabled       | 0                           |
      | teamsubmission                      | 1                           |
      | preventsubmissionnotingroup         | 0                           |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 3" row "Status" column of "generaltable" table should not contain "Submitted for grading"
    And "Student 4" row "Status" column of "generaltable" table should not contain "Submitted for grading"

    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student3  | I'm the student's first submission  |

    And I am on the "Test assignment name" Activity page
    And I follow "View all submissions"
    And "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 3" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 4" row "Status" column of "generaltable" table should contain "Submitted for grading"

  @javascript
  Scenario: Confirm that group submissions can be reopened
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
    And the following "activity" exists:
      | activity                            | assign                      |
      | course                              | C1                          |
      | name                                | Test assignment name        |
      | intro                               | Test assignment description |
      | submissiondrafts                    | 0                           |
      | assignsubmission_onlinetext_enabled | 1                           |
      | assignsubmission_file_enabled       | 0                           |
      | teamsubmission                      | 1                           |
      | attemptreopenmethod                 | manual                      |
      | requireallteammemberssubmit         | 0                           |

    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
      | Apply grades and feedback to entire group | 1 |
    And I press "Save changes"
    And I set the following fields to these values:
      | Allow another attempt | 1 |
    And I press "Save changes"
    When I am on "Course 1" course homepage
    And I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Reopened"
    And "Student 2" row "Status" column of "generaltable" table should contain "Reopened"

  Scenario: Confirm groups and submission counts are correct
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
      | student4 | Student | 4 | student4@example.com |
      | student5 | Student | 5 | student5@example.com |
      | student6 | Student | 6 | student6@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
      | student5 | C1 | student |
      | student6 | C1 | student |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
      | Group 3 | C1 | G3 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
      | student3 | G2 |
      | student4 | G2 |
      | student5 | G3 |
      | student6 | G3 |
    And the following "groupings" exist:
      | name | course | idnumber |
      | Grouping 1 | C1 | GG1 |
    And the following "grouping groups" exist:
      | grouping | group |
      | GG1      | G1    |
      | GG1      | G2    |
      # Groupmode 1 = Separate Groups
    And the following "activity" exists:
      | activity                             | assign                       |
      | course                               | C1                           |
      | name                                 | Test assignment name         |
      | intro                                | Test assignment description  |
      | submissiondrafts                     | 0                            |
      | assignsubmission_onlinetext_enabled  | 1                            |
      | assignsubmission_file_enabled        | 0                            |
      | teamsubmission                       | 1                            |
      | attemptreopenmethod                  | manual                       |
      | requireallteammemberssubmit          | 0                            |
      | groupmode                            | 1                            |
      | teamsubmissiongroupingid             | GG1                          |
      | submissiondrafts                     | 0                            |

    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |
      | Test assignment name  | student3  | I'm the student's first submission  |
      | Test assignment name  | student5  | I'm the student's first submission  |

    And I am on the "Test assignment name" Activity page logged in as admin
    And I should see "3" in the "Groups" "table_row"
    And I should see "3" in the "Submitted" "table_row"
    When I select "Group 1" from the "Separate groups" singleselect
    Then I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
    When I select "Group 2" from the "Separate groups" singleselect
    And I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"
    When I select "Group 3" from the "Separate groups" singleselect
    And I should see "1" in the "Groups" "table_row"
    And I should see "1" in the "Submitted" "table_row"

  Scenario: Confirm that the submission status changes for each group member
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
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
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G1 |
    And the following "activity" exists:
      | activity                             | assign                       |
      | course                               | C1                           |
      | name                                 | Test assignment name         |
      | intro                                | Test assignment description  |
      | submissiondrafts                     | 1                            |
      | assignsubmission_onlinetext_enabled  | 1                            |
      | assignsubmission_file_enabled        | 0                            |
      | teamsubmission                       | 1                            |
      | attemptreopenmethod                  | manual                       |
      | requireallteammemberssubmit          | 0                            |
      # Groupmode 0 = No Groups
      | groupmode                            | 0                            |
      | preventsubmissionnotingroup          | 0                            |
      | submissiondrafts                     | 0                            |
      | teamsubmission                       | 1                            |

    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                          |
      | Test assignment name  | student1  | I'm the student's first submission  |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I log out

    And I am on the "C1" Course page logged in as student1
    And I click on "Assignments" "link" in the "Activities" "block"
    And I should see "Submitted for grading"
    And I log out

    And I am on the "C1" Course page logged in as student2
    And I click on "Assignments" "link" in the "Activities" "block"
    And I should see "Submitted for grading"
    And I log out

    And I am on the "Test assignment name" Activity page logged in as teacher1
    When I follow "View all submissions"
    Then "Student 1" row "Status" column of "generaltable" table should contain "Submitted for grading"
    And "Student 2" row "Status" column of "generaltable" table should contain "Submitted for grading"
