@block @block_completionstatus @javascript
Feature: Course completion state should match completion criteria
  In order to understand the configuration or status of an course's completion
  As a user
  I need to see the appropriate completion information on course and dashboard pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | teacher2 | Teacher | 2 | teacher1@example.com | T2 |
      | student1 | Student | 1 | student1@example.com | S1 |
    And the following "courses" exist:
      | fullname | shortname | category | enablecompletion | showcompletionconditions |
      | Course 1 | C1        | 0        | 1                | 1                        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | teacher2 | C1     | teacher        |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity                            | assign                  |
      | course                              | C1                      |
      | name                                | Test assignment name    |
      | completion                          | 1                       |
      | assignsubmission_onlinetext_enabled | 1                       |
      | grade[modgrade_type] | Point |
      | grade[modgrade_point] | 100 |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | completionstatus | Course       | C1        | course-view-*   | side-pre      |
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Course completion" in current page administration
    And I click on "Condition: Activity completion" "link"
    And I set the field "Assignment - Test assignment name" to "1"
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity editing" page
    And I set the following fields to these values:
      | Completion tracking       | Show activity as complete when conditions are met |
      | completionusegrade        | 1                                                 |
      | completionpassgrade       | 1                                                 |
      | gradepass                 | 70                                                |
    And I press "Save and return to course"

  Scenario: Completion status show match completion criteria when passgrage condition is set.
    Given I am on the "Course 1" course page logged in as "student1"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "todo"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "todo"
    And I should see "Status: Not yet started" in the "Course completion status" "block"
    And I am on the "Test assignment name" "assign activity" page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
    And I press "Save changes"
    And I am on the "Course 1" course page
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Student 1, Test assignment name: Completed (did not achieve pass grade)" "icon" should exist in the "Student 1" "table_row"
    And I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    And "Student 1, Test assignment name: Completed (did not achieve pass grade)" "icon" should exist in the "Student 1" "table_row"
    And "Student 1, Course complete: Not completed" "icon" should exist in the "Student 1" "table_row"
    When I am on the "Course 1" course page logged in as "student1"
    And I should see "Status: Pending" in the "Course completion status" "block"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "failed"
    And I am on the "My courses" page
    And I should not see "100%" in the "Course overview" "block"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 75.0 |
    And I press "Save changes"
    And I am on the "Course 1" course page
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Student 1, Test assignment name: Completed (achieved pass grade)" "icon" should exist in the "Student 1" "table_row"
    And I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    And "Student 1, Test assignment name: Completed (achieved pass grade)" "icon" should exist in the "Student 1" "table_row"
    And "Student 1, Course complete: Completed" "icon" should exist in the "Student 1" "table_row"
    And I am on the "Course 1" course page logged in as "student1"
    And I should see "Status: Complete" in the "Course completion status" "block"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And the "Receive a passing grade" completion condition of "Test assignment name" is displayed as "done"
    And I am on the "My courses" page
    Then I should see "100%" in the "Course overview" "block"

  Scenario: Completion status show match completion criteria when passgrage condition is not set.
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I set the following fields to these values:
      | Completion tracking       | Show activity as complete when conditions are met |
      | completionusegrade        | 1                                                 |
      | completionpassgrade       | 0                                                 |
      | gradepass                 | 70                                                |
    And I press "Save and return to course"
    And I am on the "Course 1" course page logged in as "student1"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "todo"
    And I should see "Status: Not yet started" in the "Course completion status" "block"
    And I am on the "Test assignment name" "assign activity" page
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I press "Submit assignment"
    And I press "Continue"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
    And I press "Save changes"
    And I am on the "Course 1" course page
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Student 1, Test assignment name: Completed (did not achieve pass grade)" "icon" should exist in the "Student 1" "table_row"
    And I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    And "Student 1, Test assignment name: Completed (did not achieve pass grade)" "icon" should exist in the "Student 1" "table_row"
    And "Student 1, Course complete: Completed" "icon" should exist in the "Student 1" "table_row"
    When I am on the "Course 1" course page logged in as "student1"
    And I should see "Status: Complete" in the "Course completion status" "block"
    # Once MDL-75582 is fixed "failed" should be changed to "done"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "failed"
    And I am on the "My courses" page
    And I should see "100%" in the "Course overview" "block"
    And I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 75.0 |
    And I press "Save changes"
    And I am on the "Course 1" course page
    And I navigate to "Reports" in current page administration
    And I click on "Activity completion" "link"
    And "Student 1, Test assignment name: Completed (achieved pass grade)" "icon" should exist in the "Student 1" "table_row"
    And I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    And "Student 1, Test assignment name: Completed (achieved pass grade)" "icon" should exist in the "Student 1" "table_row"
    And "Student 1, Course complete: Completed" "icon" should exist in the "Student 1" "table_row"
    And I am on the "Course 1" course page logged in as "student1"
    And I should see "Status: Complete" in the "Course completion status" "block"
    And the "Receive a grade" completion condition of "Test assignment name" is displayed as "done"
    And I am on the "My courses" page
    Then I should see "100%" in the "Course overview" "block"
