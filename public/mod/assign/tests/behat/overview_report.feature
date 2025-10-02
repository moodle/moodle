@mod @mod_assign
Feature: Testing overview integration in mod_assign
  In order to summarize the assignments
  As a user
  I need to be able to see the assignment overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Username  | 1        |
      | student2 | Username  | 2        |
      | student3 | Username  | 3        |
      | student4 | Username  | 4        |
      | student5 | Username  | 5        |
      | student6 | Username  | 6        |
      | student7 | Username  | 7        |
      | student8 | Username  | 8        |
      | teacher1  | Teacher  | T        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
      | student6 | C1     | student        |
      | student7 | C1     | student        |
      | student8 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name           | course | idnumber | duedate              | assignsubmission_onlinetext_enabled | submissiondrafts |
      | assign   | Date assign    | C1     | assign1  | ##1 Jan 2040 08:00## | 1                                   | 0                |
      | assign   | No submissions | C1     | assign2  | ##tomorrow noon##    | 1                                   | 0                |
      | assign   | Pending grades | C1     | assign3  |                      | 1                                   | 0                |
    And the following "mod_assign > submissions" exist:
      | assign         | user     | onlinetext                          |
      | Date assign    | student1 | This is a submission for assignment |
      | Pending grades | student1 | This is a submission for assignment |
      | Pending grades | student2 | This is a submission for assignment |
    And the following "grade grades" exist:
      | gradeitem      | user     | grade |
      | Pending grades | student1 | 50    |

  Scenario: The assign overview report should generate log events
    Given I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'assign'"

  Scenario: Teachers can see relevant columns in the assign overview
    When I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    Then the following should exist in the "Table listing all Assignment activities" table:
      | Name           | Due date       | Submissions | Actions   |
      | Date assign    | 1 January 2040 | 1 of 8      | Grade (1) |
      | No submissions | Tomorrow       | 0 of 8      | View      |
      | Pending grades | -              | 2 of 8      | Grade (2) |
    # Check grade link.
    And I click on "Grade" "link" in the "Date assign" "table_row"
    And I should see "Submitted for grading" in the "Username 1" "table_row"
    And I should see "No submission" in the "Username 2" "table_row"

  @javascript
  Scenario: The assign overview actions has information about the number of pending elements to grade
    Given I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    # Validate the grade alert count data attribute.
    And "[data-mdl-overview-alertcount='1']" "css_element" should exist in the "Date assign" "table_row"
    And "[data-mdl-overview-alertlabel='Needs grading']" "css_element" should exist in the "Date assign" "table_row"
    And "[data-mdl-overview-alertcount]" "css_element" should not exist in the "No submissions" "table_row"
    And "[data-mdl-overview-alertlabel]" "css_element" should not exist in the "No submissions" "table_row"
    And "[data-mdl-overview-alertcount='2']" "css_element" should exist in the "Pending grades" "table_row"
    And "[data-mdl-overview-alertlabel='Needs grading']" "css_element" should exist in the "Pending grades" "table_row"
    # Validate alert badge updates.
    And the following "grade grades" exist:
      | gradeitem      | user     | grade |
      | Date assign    | student1 | 50    |
      | Pending grades | student1 | 50    |
    When I am on the "Course 1" "course > activities > assign" page logged in as "teacher1"
    Then the following should exist in the "Table listing all Assignment activities" table:
      | Name           | Due date       | Submissions | Actions   |
      | Date assign    | 1 January 2040 | 1 of 8      | View      |
      | No submissions | Tomorrow       | 0 of 8      | View      |
      | Pending grades | -              | 2 of 8      | Grade (1) |
    # Validate the grade alert count data attribute update.
    And "[data-mdl-overview-alertcount]" "css_element" should not exist in the "Date assign" "table_row"
    And "[data-mdl-overview-alertlabel]" "css_element" should not exist in the "Date assign" "table_row"
    And "[data-mdl-overview-alertcount]" "css_element" should not exist in the "No submissions" "table_row"
    And "[data-mdl-overview-alertlabel]" "css_element" should not exist in the "No submissions" "table_row"
    And "[data-mdl-overview-alertcount='1']" "css_element" should exist in the "Pending grades" "table_row"
    And "[data-mdl-overview-alertlabel='Needs grading']" "css_element" should exist in the "Pending grades" "table_row"

  Scenario: The assign overview actions has different label action based on assignment configuration
    Given the following "activity" exists:
      | course                              | C1            |
      | activity                            | assign        |
      | name                                | Feedback only |
      | idnumber                            | assign4       |
      | submissiondrafts                    | 0             |
      | assignsubmission_onlinetext_enabled | 1             |
      | assignfeedback_comments_enabled     | 1             |
      | assignfeedback_editpdf_enabled      | 1             |
    And the following "activity" exists:
      | course                              | C1            |
      | activity                            | assign        |
      | name                                | Neither grading nor feedback |
      | idnumber                            | assign5       |
      | submissiondrafts                    | 0             |
      | assignsubmission_onlinetext_enabled | 1             |
      | assignfeedback_comments_enabled     | 0             |
      | assignfeedback_editpdf_enabled      | 0             |
    And the following "mod_assign > submissions" exist:
      | assign                       | user     | onlinetext                              |
      | Feedback only                | student1 | Feedback only assignment                |
      | Neither grading nor feedback | student1 | Neither grading nor feedback submission |
    And I am on the "Feedback only" "assign activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "None"
    And I press "Save and return to course"
    And I am on the "Neither grading nor feedback" "assign activity editing" page
    And I expand all fieldsets
    And I set the field "grade[modgrade_type]" to "None"
    And I press "Save and return to course"
    When I am on the "Course 1" "course > activities > assign" page
    Then the following should exist in the "Table listing all Assignment activities" table:
      | Name                         | Submissions | Actions   |
      | Date assign                  | 1 of 8      | Grade (1) |
      | Feedback only                | 1 of 8      | View      |
      | Neither grading nor feedback | 1 of 8      | View      |

  Scenario: Students can see relevant columns in the assign overview
    When I am on the "Course 1" "course > activities > assign" page logged in as "student1"
    Then the following should exist in the "Table listing all Assignment activities" table:
      | Name           | Due date       | Submission status     | Grade |
      | Date assign    | 1 January 2040 | Submitted for grading | -     |
      | No submissions | Tomorrow       | No submission         | -     |
      | Pending grades | -              | Submitted for grading | 50.00 |

  Scenario: The assign index redirect to the activities overview
    When I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Activities" block
    And I click on "Assignments" "link" in the "Activities" "block"
    Then I should see "An overview of all activities in the course"
    And I should see "Name" in the "assign_overview_collapsible" "region"
    And I should see "Due date" in the "assign_overview_collapsible" "region"
    And I should see "Submissions" in the "assign_overview_collapsible" "region"
    And I should see "Actions" in the "assign_overview_collapsible" "region"
