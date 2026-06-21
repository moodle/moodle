@mod @mod_assign @assignfeedback @assignfeedback_offline
Feature: In an assignment, teachers can update grades, marks and feedback plugin values by uploading a grading worksheet.
  In order to save this data for a student submission
  As a teacher,
  I need to upload a grading worksheet.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | teacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activity" exists:
      | activity                 | assign        |
      | course                   | C1            |
      | idnumber                 | A1            |
      | name                     | Assignment 1  |
      | section                  | 1             |
      | completion               | 1             |
      | markingworkflow          | 1             |
      | markingallocation        | 1             |
      | markercount              | 2             |
      | grade[modgrade_type]     | point         |
      | grade[modgrade_point]    | 100           |
      | assignsubmission_onlinetext_enabled | 1  |
      | assignfeedback_comments_enabled     | 1  |
      | assignfeedback_offline_enabled      | 1  |
    And the following "activities" exist:
      | activity  | course  | name                  | assignsubmission_onlinetext_enabled  | assignfeedback_comments_enabled  |
      | assign    | C1      | Test assignment name  | 1                                    | 1                                |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user          | marker      |
      | Assignment 1 | student1      | teacher1    |
      | Assignment 1 | student2      | teacher1    |

  @javascript @_file_upload
  Scenario: Upload grading worksheet with grades
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I choose the "Upload grading worksheet" item in the "Actions" action menu
    And I upload "mod/assign/feedback/offline/tests/fixtures/assignfeedback_offline_test_worksheet_1.csv" file to "Upload a file" filemanager
    When I click on "Upload grading worksheet" "button" in the "#fgroup_id_buttonar" "css_element"
    Then I should see "Set grade for Student 1 to 50"
    And I should see "Set grade for Student 2 to 75"
    And I click on "Confirm" "button" in the "#fgroup_id_buttonar" "css_element"
    And I should see "Updated 2 grades, 0 marks, and 0 feedback instances"
    And I navigate to "Submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain "50.00"
    And "Student 2" row "Grade" column of "generaltable" table should contain "75.00"

  @javascript @_file_upload
  Scenario: Upload grading worksheet with marks
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I choose the "Upload grading worksheet" item in the "Actions" action menu
    And I upload "mod/assign/feedback/offline/tests/fixtures/assignfeedback_offline_test_worksheet_2.csv" file to "Upload a file" filemanager
    When I click on "Upload grading worksheet" "button" in the "#fgroup_id_buttonar" "css_element"
    Then I should see "Set mark for Student 1 to 10"
    And I should see "Set mark for Student 2 to 30"
    And I click on "Confirm" "button" in the "#fgroup_id_buttonar" "css_element"
    And I should see "Updated 0 grades, 2 marks, and 0 feedback instances"
    And I navigate to "Submissions" in current page administration
    And "Student 1" row "Marker 1" column of "generaltable" table should contain "10.00"
    And "Student 2" row "Marker 1" column of "generaltable" table should contain "30.00"

  @javascript @_file_upload
  Scenario: Upload grading worksheet with feedback plugin values (comments)
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I choose the "Upload grading worksheet" item in the "Actions" action menu
    And I upload "mod/assign/feedback/offline/tests/fixtures/assignfeedback_offline_test_worksheet_3.csv" file to "Upload a file" filemanager
    When I click on "Upload grading worksheet" "button" in the "#fgroup_id_buttonar" "css_element"
    Then I should see "Set field \"Marker 1 comment\" for \"Student 1\" to \"S1-M1\""
    And I should see "Set field \"Marker 1 comment\" for \"Student 2\" to \"S2-M1\""
    And I click on "Confirm" "button" in the "#fgroup_id_buttonar" "css_element"
    And I should see "Updated 0 grades, 0 marks, and 2 feedback instances"
    And I navigate to "Submissions" in current page administration
    And "Student 1" row "Marker 1 comment" column of "generaltable" table should contain "S1-M1"
    And "Student 2" row "Marker 1 comment" column of "generaltable" table should contain "S2-M1"

  @javascript @_file_upload
  Scenario: Upload grading worksheet with grades, marks and feedback plugin values (comments)
    Given I am on the "A1" "assign activity" page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I choose the "Upload grading worksheet" item in the "Actions" action menu
    And I upload "mod/assign/feedback/offline/tests/fixtures/assignfeedback_offline_test_worksheet_4.csv" file to "Upload a file" filemanager
    When I click on "Upload grading worksheet" "button" in the "#fgroup_id_buttonar" "css_element"
    Then I should see "Set grade for Student 1 to 50"
    And I should see "Set grade for Student 2 to 75"
    And I should see "Set mark for Student 1 to 10"
    And I should see "Set mark for Student 2 to 30"
    And I should see "Set field \"Feedback comments\" for \"Student 1\" to \"S1-Overall\""
    And I should see "Set field \"Feedback comments\" for \"Student 2\" to \"S2-Overall\""
    And I should see "Set field \"Marker 1 comment\" for \"Student 1\" to \"S1-M1\""
    And I should see "Set field \"Marker 1 comment\" for \"Student 2\" to \"S2-M1\""
    And I click on "Confirm" "button" in the "#fgroup_id_buttonar" "css_element"
    And I should see "Updated 2 grades, 2 marks, and 4 feedback instances"
    And I navigate to "Submissions" in current page administration
    And "Student 1" row "Grade" column of "generaltable" table should contain "50.00"
    And "Student 2" row "Grade" column of "generaltable" table should contain "75.00"
    And "Student 1" row "Marker 1" column of "generaltable" table should contain "10.00"
    And "Student 2" row "Marker 1" column of "generaltable" table should contain "30.00"
    And "Student 1" row "Feedback comments" column of "generaltable" table should contain "S1-Overall"
    And "Student 2" row "Feedback comments" column of "generaltable" table should contain "S2-Overall"
    And "Student 1" row "Marker 1 comment" column of "generaltable" table should contain "S1-M1"
    And "Student 2" row "Marker 1 comment" column of "generaltable" table should contain "S2-M1"
