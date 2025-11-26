@mod @mod_assign
Feature: Assign group override
  In order to grant a group special access to an assignment
  As a teacher
  I need to create an override for that group.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Tina | Teacher1 | teacher1@example.com |
      | student1 | Sam1 | Student1 | student1@example.com |
      | student2 | Sam2 | Student2 | student2@example.com |
      | student3 | Sam3 | Student3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
      | Group 2 | C1     | G2       |
    Given the following "group members" exist:
      | user     | group   |
      | student1 | G1 |
      | student2 | G2 |
      | student3 | G1 |
    And the following "activities" exist:
      | activity | name                 | intro                   | course | assignsubmission_onlinetext_enabled |
      | assign   | Test assignment name | Submit your online text | C1     | 1                                   |

  @javascript
  Scenario: Add, modify then delete a group override
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1              |
      | Due date       | ##1 Jan 2020 08:00## |
    And I press "Save"
    Then I should see "Wednesday, 1 January 2020, 8:00"
    And I click on "Edit" "link" in the "Group 1" "table_row"
    And I set the following fields to these values:
      | Due date       | ##1 Jan 2030 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I click on "Delete" "link"
    And I press "Continue"
    And I should not see "Group 1"

  Scenario: Duplicate a user override
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1              |
      | Due date       | ##1 Jan 2020 08:00## |
    And I press "Save"
    Then I should see "Wednesday, 1 January 2020, 8:00"
    And I click on "copy" "link"
    And I set the following fields to these values:
      | Override group | Group 2              |
      | Due date       | ##1 Jan 2030 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I should see "Group 2"

  Scenario: Allow a group to have a different due date
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Allow submissions from | disabled             |
      | Due date               | ##1 Jan 2000 08:00## |
      | Cut-off date           | disabled             |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1              |
      | Due date       | ##1 Jan 2020 08:00## |
    And I press "Save"
    And I should see "Wednesday, 1 January 2020, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    Then the activity date in "Test assignment name" should contain "Due: Saturday, 1 January 2000, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And the activity date in "Test assignment name" should contain "Due: Wednesday, 1 January 2020, 8:00"

  Scenario: Allow a group to have a different cut off date
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Due date               | disabled             |
      | Allow submissions from | disabled             |
      | Cut-off date           | ##1 Jan 2000 08:00## |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1              |
      | Cut-off date   | ##1 Jan 2030 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    Then I should not see "You have not made a submission yet."
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And I should see "No submissions have been made yet"

  Scenario: Allow a group to have a different start date
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Due date               | disabled                 |
      | Allow submissions from | ##1 January 2030 08:00## |
      | Cut-off date           | disabled                 |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 1              |
      | Allow submissions from | ##1 Jan 2015 08:00## |
    And I press "Save"
    And I should see "Thursday, 1 January 2015, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    Then the activity date in "Test assignment name" should contain "Opens: Tuesday, 1 January 2030, 8:00"
    And I should not see "Add submission"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And I should not see "Tuesday, 1 January 2030, 8:00"

  @javascript
  Scenario: Add both a user and group override and verify that both are applied correctly
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    When I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | Due date               | disabled                 |
      | Allow submissions from | ##1 January 2040 08:00## |
      | Cut-off date           | disabled                 |
      | Group mode             | Visible groups           |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 1                  |
      | Allow submissions from | ##1 January 2030 08:00## |
    And I press "Save"
    And I should see "Tuesday, 1 January 2030, 8:00"
    And I am on the "Test assignment name" Activity page
    And I navigate to "Overrides" in current page administration
    And I press "Add user override"
    And I set the following fields to these values:
      | Override user          | Student1                 |
      | Allow submissions from | ##1 January 2031 08:00## |
    And I press "Save"
    And I should see "Wednesday, 1 January 2031, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And the activity date in "Test assignment name" should contain "Opens: Wednesday, 1 January 2031, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    And the activity date in "Test assignment name" should contain "Opens: Sunday, 1 January 2040, 8:00"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student3
    And the activity date in "Test assignment name" should contain "Opens: Tuesday, 1 January 2030, 8:00"

  Scenario: Override a group when teacher is in no group, and does not have accessallgroups permission, and the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | 1         |
    And I am on the "Assignment 2" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    Then I should see "There are no groups in this course."
    And the "Add group override" "button" should be disabled

  Scenario: A teacher without accessallgroups permission should only be able to add group override for groups that he/she is a member of,
        when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | 1         |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    And I am on the "Assignment 2" Activity page logged in as teacher1
    When I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    Then the "Override group" select box should contain "Group 1"
    And the "Override group" select box should not contain "Group 2"

  Scenario: A teacher without accessallgroups permission should only be able to see the group overrides for groups that he/she is a member of,
        when the activity's group mode is "separate groups"
    Given the following "permission overrides" exist:
      | capability                  | permission | role           | contextlevel | reference |
      | moodle/site:accessallgroups | Prevent    | editingteacher | Course       | C1        |
    And the following "activities" exist:
      | activity | name         | intro                    | course | groupmode |
      | assign   | Assignment 2 | Assignment 2 description | C1     | 1         |
    And the following "group members" exist:
      | user     | group |
      | teacher1 | G1    |
    And I am on the "Assignment 2" Activity page logged in as admin
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 1                  |
      | Allow submissions from | ##1 January 2020 08:00## |
    And I press "Save and enter another override"
    And I set the following fields to these values:
      | Override group         | Group 2                  |
      | Allow submissions from | ##1 January 2020 08:00## |
    And I press "Save"
    And I log out

    When I am on the "Assignment 2" Activity page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    Then I should see "Group 1" in the ".generaltable" "css_element"
    And I should not see "Group 2" in the ".generaltable" "css_element"

  Scenario: "Not visible" groups should not be available for group overrides
    Given the following "groups" exist:
      | name                                 | course | idnumber | visibility | participation |
      | Visible to everyone/Participation         | C1     | VP       | 0          | 1             |
      | Only visible to members/Participation     | C1     | MP       | 1          | 1             |
      | Only see own membership                   | C1     | O        | 2          | 0             |
      | Not visible                          | C1     | N        | 3          | 0             |
      | Visible to everyone/Non-Participation     | C1     | VN       | 0          | 0             |
      | Only visible to members/Non-Participation | C1     | MN       | 1          | 0             |
    When I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    Then I should see "Visible to everyone/Participation" in the "Override group" "select"
    And I should see "Visible to everyone/Non-Participation" in the "Override group" "select"
    And I should see "Only visible to members" in the "Override group" "select"
    And I should see "Only visible to members/Non-Participation" in the "Override group" "select"
    And I should see "Only see own membership" in the "Override group" "select"
    And I should not see "Not visible" in the "Override group" "select"

  @javascript
  Scenario: Teachers can trigger grade penalty recalculation when modifying or deleting group overrides
    Given I enable grade penalties for assignment
    And the following "activity" exists:
      | activity                             | assign                      |
      | course                               | C1                          |
      | name                                 | Test assignment penalty     |
      | intro                                | Test assignment description |
      | grade                                | 100                         |
      | duedate                              | ##tomorrow##                |
      | gradepenalty                         | 1                           |
      | assignsubmission_onlinetext_enabled  | 1                           |
      | submissiondrafts                     | 0                           |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                        |
      | Test assignment name  | student1  | I'm the student first submission  |
    # Add a group override with a due date set in the future.
    And I am on the "Test assignment penalty" Activity page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group | Group 1            |
      | Due date       | ##tomorrow +1day## |
    And I press "Save"
    And I change window size to "large"
    And I go to "Sam1 Student1" "Test assignment penalty" activity advanced grading page
    And I set the field "Grade out of 100" to "90"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    And "Sam1 Student1" row "Grade" column of "submissions" table should contain "90.00"
    And "Sam1 Student1" row "Final grade" column of "submissions" table should contain "90.00"
    # Modify the group override by changing the due date to a past date.
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I click on "Edit" "link" in the "Group 1" "table_row"
    When I set the following fields to these values:
      | Recalculate penalty | Yes                |
      | Due date            | ##yesterday##      |
    And I press "Save"
    And I navigate to "Submissions" in current page administration
    Then "Sam1 Student1" row "Grade" column of "submissions" table should contain "90.00"
    And "Sam1 Student1" row "Final grade" column of "submissions" table should contain "80.00"
    # Delete the group override.
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I click on "Delete" "link" in the "Group 1" "table_row"
    And I click on "Recalculate penalty for user(s) in the override" "checkbox" in the "Confirm" "dialogue"
    And I click on "Continue" "button" in the "Confirm" "dialogue"
    And I navigate to "Submissions" in current page administration
    And "Sam1 Student1" row "Grade" column of "submissions" table should contain "90.00"
    And "Sam1 Student1" row "Final grade" column of "submissions" table should contain "90.00"

  @javascript
  Scenario: Assign activity group overrides are displayed on the timeline block
    Given the following "group members" exist:
      | user     | group |
      | student1 | G2    |
    And I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | allowsubmissionsfromdate[enabled]  | 1            |
      | duedate[enabled]                   | 1            |
      | allowsubmissionsfromdate           | ##today##    |
      | duedate                            | ##tomorrow## |
    And I press "Save and display"
    When I log in as "student1"
    Then I should see "##tomorrow##%A, %d %B %Y##" in the "Timeline" "block"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 1            |
      | Allow submissions from | ##tomorrow##       |
      | Due date               | ##tomorrow +1day## |
    And I press "Save"
    And I log in as "student1"
    And I should see "##tomorrow +1day##%A, %d %B %Y##" in the "Timeline" "block"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 2             |
      | Allow submissions from | ##tomorrow +1day##  |
      | Due date               | ##tomorrow +3days## |
    And I press "Save"
    And I click on "Move up" "link" in the "Group 2" "table_row"
    And I log in as "student1"
    And I should see "##tomorrow +3days##%A, %d %B %Y##" in the "Timeline" "block"

  @javascript
  Scenario: Assign activity user override is displayed even if group override exists on the timeline block
    Given the following "group members" exist:
      | user     | group |
      | student1 | G2    |
    And I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | allowsubmissionsfromdate[enabled]  | 1            |
      | duedate[enabled]                   | 1            |
      | allowsubmissionsfromdate           | ##today##    |
      | duedate                            | ##tomorrow## |
    And I press "Save and display"
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 1            |
      | Allow submissions from | ##tomorrow##       |
      | Due date               | ##tomorrow +1day## |
    And I press "Save"
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 2             |
      | Allow submissions from | ##tomorrow +1day##  |
      | Due date               | ##tomorrow +3days## |
    And I navigate to "Overrides > Add user override" in current page administration
    And I set the following fields to these values:
      | Override user            | Sam1 Student1     |
      | allowsubmissionsfromdate | ##tomorrow##      |
      | duedate                  | ##tomorrow noon## |
    And I press "Save"
    When I log in as "student1"
    Then I should see "##tomorrow noon##%A, %d %B %Y##" in the "Timeline" "block"

  @javascript
  Scenario: Assign activity override are not visible on timeline block when student is unenrolled
    Given the following "group members" exist:
      | user     | group |
      | student1 | G2    |
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I navigate to "Overrides" in current page administration
    And I select "Group overrides" from the "jump" singleselect
    And I press "Add group override"
    And I set the following fields to these values:
      | Override group         | Group 2             |
      | Allow submissions from | ##tomorrow +1day##  |
      | Due date               | ##tomorrow +3days## |
    And I navigate to "Overrides > Add user override" in current page administration
    And I set the following fields to these values:
      | Override user            | Sam1 Student1     |
      | allowsubmissionsfromdate | ##tomorrow##      |
      | duedate                  | ##tomorrow noon## |
    And I press "Save"
    And I am on "Course 1" course homepage
    And I navigate to course participants
    And I click on "Unenrol" "icon" in the "student1" "table_row"
    And I click on "Unenrol" "button" in the "Unenrol" "dialogue"
    When I log in as "student1"
    Then "Test assignment name" "link" should not exist in the "Timeline" "block"
    And I should not see "##tomorrow noon##%A, %d %B %Y##" in the "Timeline" "block"
