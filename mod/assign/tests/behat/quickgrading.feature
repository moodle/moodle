@mod @mod_assign
Feature: In an assignment, teachers grade multiple students on one page
  In order to quickly give students grades and feedback
  As a teacher
  I need to grade multiple students on one page

  @javascript
  Scenario: Grade multiple students on one page
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
      | student1 | Student | 1 | student1@asd.com |
      | student2 | Student | 2 | student2@asd.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    When I log in as "admin"
    And I set the following administration settings values:
      | Enable outcomes | 1 |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Outcomes"
    And I follow "Edit outcomes"
    And I press "Add a new outcome"
    And I press "Continue"
    And I set the following fields to these values:
      | Name | 1337dom scale |
      | Scale | Noob, Nub, 1337, HaXor |
    And I press "Save changes"
    And I follow "Course 1"
    And I follow "Outcomes"
    And I follow "Edit outcomes"
    And I press "Add a new outcome"
    And I set the following fields to these values:
      | Full name | M8d skillZ! |
      | Short name | skillZ! |
      | Scale | 1337dom scale |
    And I press "Save changes"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | M8d skillZ! | 1 |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Grade Student 1" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
      | M8d skillZ! | 1337 |
      | Feedback comments | I'm the teacher first feedback |
    And I press "Save changes"
    And I press "Continue"
    Then I click on "Quick grading" "checkbox"
    And I set the field "User grade" to "60.0"
    And I press "Save all quick grading changes"
    And I should see "The grade changes were saved"
    And I press "Continue"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "I'm the teacher first feedback"
    And I should see "60.0"
    And I follow "Course 1"
    And I follow "Grades"
    And I should see "1337"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should not see "I'm the teacher first feedback"
    And I should not see "60.0"
    And I follow "Course 1"
    And I follow "Grades"
    And I should not see "1337"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I follow "View/grade all submissions"
    And I click on "Hide User picture" "link"
    And I click on "Hide Full name" "link"
    And I click on "Hide Email address" "link"
    And I click on "Hide Status" "link"
    And I click on "Hide Grade" "link"
    And I click on "Hide Edit" "link"
    And I click on "Hide Last modified (submission)" "link"
    And I click on "Hide Online text" "link"
    And I click on "Hide Submission comments" "link"
    And I click on "Hide Last modified (grade)" "link"
    And I click on "Hide Feedback comments" "link"
    And I click on "Hide Final grade" "link"
    And I click on "Hide Outcomes" "link"
    And I press "Save all quick grading changes"
    And I should see "The grade changes were saved"
    And I press "Continue"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should see "I'm the teacher first feedback"
    And I should see "60.0"
    And I follow "Course 1"
    And I follow "Grades"
    And I should see "1337"
    And I log out
    And I log in as "student2"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I should not see "I'm the teacher first feedback"
    And I should not see "60.0"
    And I follow "Course 1"
    And I follow "Grades"
    And I should not see "1337"
