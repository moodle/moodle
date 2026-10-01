@mod @mod_assign @assignfeedback @assignfeedback_file @_file_upload
Feature: In an assignment, teacher can submit feedback files during grading
  In order to provide a feedback file
  As a teacher
  I need to submit a feedback file.

  Background:
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
      | name     | course | idnumber |
      | G1       | C1     | G1       |
    And the following "group members" exist:
      | user     | group |
      | student1 | G1    |
      | student2 | G1    |
    And the following "activity" exists:
      | activity                            | assign                |
      | course                              | C1                    |
      | name                                | Test assignment name  |
      | assignsubmission_file_enabled       | 1                     |
      | assignsubmission_file_maxfiles      | 1                     |
      | assignsubmission_file_maxsizebytes  | 1024                  |
      | assignfeedback_comments_enabled     | 1                     |
      | assignfeedback_file_enabled         | 1                     |
      | maxfilessubmission                  | 2                     |
      | teamsubmission                      | 1                     |
      | submissiondrafts                    | 0                     |
    And the following "mod_assign > submission" exists:
      | assign  | Test assignment name                                    |
      | user    | student1                                                |
      | file    | mod/assign/feedback/file/tests/fixtures/submission.txt  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I click on "Grade" "link" in the ".tertiary-navigation" "css_element"
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager

  @javascript
  Scenario: A teacher can provide a feedback file when grading an assignment.
    Given I set the field "applytoall" to "0"
    And I press "Save changes"
    And I click on "Course 1" "link" in the "[data-region=assignment-info]" "css_element"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And I should see "feedback.txt"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student2
    Then I should not see "feedback.txt"

  @javascript
  Scenario: A teacher can provide a feedback file when grading an assignment and all students in the group will receive the file.
    Given I press "Save changes"
    And I click on "Course 1" "link" in the "[data-region=assignment-info]" "css_element"
    And I log out
    And I am on the "Test assignment name" Activity page logged in as student1
    And I should see "feedback.txt"
    And I log out
    When I am on the "Test assignment name" Activity page logged in as student2
    Then I should see "feedback.txt"

  @javascript
  Scenario: Students should see personalised feedback file labels from each marker in a multi-marker assignment
    Given I log out
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher2 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                        | assign       |
      | course                          | C1           |
      | idnumber                        | A1           |
      | name                            | Assignment 1 |
      | section                         | 1            |
      | markingworkflow                 | 1            |
      | markingallocation               | 1            |
      | markercount                     | 2            |
      | assignfeedback_file_enabled     | 1            |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user     | marker   |
      | Assignment 1 | student1 | teacher1 |
      | Assignment 1 | student1 | teacher2 |
    And the following "role capability" exists:
      | role                              | editingteacher |
      | mod/assign:managerestrictedgrades | allow          |
    # Marker 1 (Teacher 1) uploads their feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Marker 2 (Teacher 2) uploads their feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Release the grade so the student can view the feedback.
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And I click on "Grade actions" "actionmenu" in the "Student 1" "table_row"
    And I choose "Grade" in the open action menu
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    # The student views their assignment and should see each file labelled with the marker's name.
    When I am on the "A1" "assign activity" page logged in as student1
    Then I should see "Marker file (Teacher 1)"
    And I should see "Marker file (Teacher 2)"

  @javascript
  Scenario: Students should see anonymised feedback file labels in a multi-marker assignment if grader identities are hidden
    Given I log out
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher2 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                        | assign       |
      | course                          | C1           |
      | idnumber                        | A1           |
      | name                            | Assignment 1 |
      | section                         | 1            |
      | markingworkflow                 | 1            |
      | markingallocation               | 1            |
      | markercount                     | 2            |
      | assignfeedback_file_enabled     | 1            |
      | hidegrader                      | 1            |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user     | marker   |
      | Assignment 1 | student1 | teacher1 |
      | Assignment 1 | student1 | teacher2 |
    And the following "role capability" exists:
      | role                              | editingteacher |
      | mod/assign:managerestrictedgrades | allow          |
    # Marker 1 (Teacher 1) uploads their feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Marker 2 (Teacher 2) uploads their feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Release the grade so the student can view the feedback.
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And I click on "Grade actions" "actionmenu" in the "Student 1" "table_row"
    And I choose "Grade" in the open action menu
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    # The student views their assignment and should see each file labelled with the marker's position only.
    When I am on the "A1" "assign activity" page logged in as student1
    Then I should see "Marker 1 file"
    And I should see "Marker 2 file"
    And I should not see "Marker file (Teacher 1)"
    And I should not see "Teacher 1"
    And I should not see "Teacher 2"

  @javascript
  Scenario: Students should only see feedback files from currently allocated markers
    Given I log out
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | teacher3 | Teacher   | 3        | teacher3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher2 | C1     | editingteacher |
      | teacher3 | C1     | editingteacher |
    And the following "activity" exists:
      | activity                        | assign       |
      | course                          | C1           |
      | idnumber                        | A1           |
      | name                            | Assignment 1 |
      | section                         | 1            |
      | markingworkflow                 | 1            |
      | markingallocation               | 1            |
      | markercount                     | 2            |
      | assignfeedback_file_enabled     | 1            |
    And the following "mod_assign > marker_allocations" exist:
      | assign       | user     | marker   |
      | Assignment 1 | student1 | teacher1 |
      | Assignment 1 | student1 | teacher2 |
    And the following "role capability" exists:
      | role                               | editingteacher |
      | mod/assign:managerestrictedgrades  | allow          |
      | mod/assign:managemarkedallocations | allow          |
    # Marker 1 (Teacher 1) uploads their feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher1
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Marker 2 (Teacher 2) uploads their feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher2
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Replace Teacher 2 with Teacher 3 as marker 2, who then uploads their own feedback file.
    And I am on the "A1" "assign activity" page logged in as teacher3
    And I change window size to "large"
    And I go to "Student 1" "Assignment 1" activity advanced grading page
    And I set the field "Marking workflow state" to "In marking"
    And I set the field "Marker 2" to "Teacher 3"
    And I press "Save changes"
    And I am on the "A1" "assign activity" page
    And I go to "Student 1" "Assignment 1" activity advanced marking page
    And I upload "mod/assign/feedback/file/tests/fixtures/feedback.txt" file to "Feedback files" filemanager
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    # Release the grade so the student can view the feedback.
    And I am on the "A1" "assign activity" page
    And I navigate to "Submissions" in current page administration
    And I click on "Grade actions" "actionmenu" in the "Student 1" "table_row"
    And I choose "Grade" in the open action menu
    And I set the field "Marking workflow state" to "Released"
    And I press "Save changes"
    # The student should only see feedback files from the currently allocated markers.
    When I am on the "A1" "assign activity" page logged in as student1
    Then I should see "Marker file (Teacher 1)"
    And I should see "Marker file (Teacher 3)"
    And I should not see "Marker file (Teacher 2)"
