@mod @mod_assign @javascript
Feature: In an assignment, teachers grade multiple students on one page
  In order to quickly give students grades and feedback
  As a teacher
  I need to grade multiple students on one page

  Scenario: Saving but not grading a grade should
    not indicate the grade is graded.
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity                                      | assign                  |
      | course                                        | C1                      |
      | name                                          | Test assignment name    |
      | intro                                         | Submit your online text |
      | submissiondrafts                              | 0                       |
      | assignsubmission_onlinetext_enabled           | 1                       |
    And the following "mod_assign > submissions" exist:
      | assign                | user      | onlinetext                   |
      | Test assignment name  | student1  | I'm the student1 submission  |

    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "View all submissions" in current page administration
    When I click on "Grade" "link" in the "Student 1" "table_row"
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    Then I should see "1" in the "Needs grading" "table_row"

  @skip_chrome_zerosize
  Scenario: Grade multiple students on one page
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
    And the following config values are set as admin:
      | enableoutcomes | 1 |
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Outcomes" in current page administration
    And I follow "Edit outcomes"
    And I press "Add a new outcome"
    And I follow "Add a new scale"
    And I set the following fields to these values:
      | Name | 1337dom scale |
      | Scale | Noob, Nub, 1337, HaXor |
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I navigate to "Outcomes" in current page administration
    And I follow "Edit outcomes"
    And I press "Add a new outcome"
    And I set the following fields to these values:
      | Full name | M8d skillZ! |
      | Short name | skillZ! |
      | Scale | 1337dom scale |
    And I press "Save changes"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | M8d skillZ! | 1 |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student2 submission |
    And I press "Save changes"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Grade" "link" in the "Student 1" "table_row"
    And I set the following fields to these values:
      | Grade out of 100 | 50.0 |
      | M8d skillZ! | 1337 |
      | Feedback comments | I'm the teacher first feedback |
    And I press "Save changes"
    And I click on "Edit settings" "link"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    Then I click on "Quick grading" "checkbox"
    And I set the field "User grade" to "60.0"
    And I press "Save all quick grading changes"
    And I should see "The grade changes were saved"
    And I press "Continue"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "I'm the teacher first feedback"
    And I should see "60.0"
    And I follow "Grades" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I should see "1337"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "I'm the teacher first feedback"
    And I should not see "60.0"
    And I follow "Grades" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I should not see "1337"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
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
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should see "I'm the teacher first feedback"
    And I should see "60.0"
    And I follow "Grades" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I should see "1337"
    And I log out
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I should not see "I'm the teacher first feedback"
    And I should not see "60.0"
    And I follow "Grades" in the user menu
    And I click on "Course 1" "link" in the "region-main" "region"
    And I should not see "1337"
