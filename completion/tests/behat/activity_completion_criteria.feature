@core @core_completion
Feature: Allow to mark course as completed without cron for activity completion criteria
  In order for students to see instant course completion updates
  I need to be able update completion state without cron

  Background:
    Given the following "courses" exist:
      | fullname          | shortname | category |
      | Completion course | CC1       | 0        |
    And the following "users" exist:
      | username | firstname | lastname  | email                |
      | student1 | Student   | First     | student1@example.com |
      | student2 | Student   | Second    | student2@example.com |
      | teacher1 | Teacher   | First     | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | CC1    | student        |
      | student2 | CC1    | student        |
      | teacher1 | CC1    | editingteacher |
    And the following "activity" exists:
      | activity                            | assign                      |
      | course                              | CC1                         |
      | name                                | Test assignment name        |
      | idnumber                            | assign1                     |
      | description                         | Test assignment description |
    And the following "blocks" exist:
      | blockname        | contextlevel | reference | pagetypepattern | defaultregion |
      | completionstatus | Course       | CC1       | course-view-*   | side-pre      |
    And I am on the "Completion course" "course editing" page logged in as admin
    And I expand all fieldsets
    And I set the field "Enable completion tracking" to "Yes"
    And I click on "Save and display" "button"
    And I am on the "Test assignment name" "assign activity editing" page
    And I follow "Expand all"
    And I set the field "Completion tracking" to "Show activity as complete when conditions are met"
    And I set the field "completionusegrade" to "1"
    And I press "Save and return to course"
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the field "Assignment - Test assignment name" to "1"
    And I press "Save changes"

  @javascript
  Scenario: Update course completion when student marks activity as complete
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I follow "Expand all"
    And I set the field "Completion tracking" to "Students can manually mark the activity as completed"
    And I press "Save and return to course"
    When I am on the "Completion course" course page logged in as student1
    And I should see "Status: Not yet started"
    And I press "Mark as done"
    And I wait until "Done" "button" exists
    And "Mark as done" "button" should not exist
    And I reload the page
    Then I should see "Status: Complete"

  @javascript
  Scenario: Update course completion when teacher grades a single assignment
    Given I am on the "Test assignment name" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "student1@example.com" "table_row"
    And I set the field "Grade out of 100" to "40"
    And I click on "Save changes" "button"
    And I am on "Completion course" course homepage
    When I am on the "Completion course" course page logged in as student1
    Then I should see "Status: Complete"

  @javascript
  Scenario: Update course completion with multiple activity criteria
    Given the following "activity" exists:
      | activity                            | assign                      |
      | course                              | CC1                         |
      | name                                | Test assignment name2       |
      | idnumber                            | assign2                     |
    And I am on the "Test assignment name2" "assign activity editing" page logged in as admin
    And I follow "Expand all"
    And I set the field "Completion tracking" to "Show activity as complete when conditions are met"
    And I set the field "completionusegrade" to "1"
    And I press "Save and return to course"
    And I navigate to "Course completion" in current page administration
    And I expand all fieldsets
    And I set the field "Assignment - Test assignment name" to "1"
    And I set the field "Assignment - Test assignment name2" to "1"
    And I press "Save changes"
    And I am on the "Test assignment name" "assign activity" page
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "student1@example.com" "table_row"
    And I set the field "Grade out of 100" to "40"
    And I click on "Save changes" "button"
    And I am on "Completion course" course homepage
    And I am on the "Completion course" course page logged in as student1
    And I should see "Status: In progress"
    And I am on the "Test assignment name2" "assign activity" page logged in as teacher1
    And I follow "View all submissions"
    And I click on "Grade" "link" in the "student1@example.com" "table_row"
    And I set the field "Grade out of 100" to "40"
    And I click on "Save changes" "button"
    And I am on "Completion course" course homepage
    When I am on the "Completion course" course page logged in as student1
    Then I should see "Status: Complete"

  @javascript
  Scenario: Course completion should not be updated when teacher grades assignment on course grader report page
    Given I am on the "Completion course" course page logged in as teacher1
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "57" to the user "Student First" for the grade item "Test assignment name"
    And I press "Save changes"
    When I am on the "Completion course" course page logged in as student1
    Then I should see "Status: Pending"
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    And I reload the page
    And I should see "Status: Complete"

  @javascript
  Scenario: Course completion should not be updated when teacher grades assignment on activity grader report page
    Given I am on the "Completion course" course page logged in as teacher1
    And I navigate to "View > Single view" in the course gradebook
    And I click on "Users" "link" in the ".page-toggler" "css_element"
    And I turn editing mode on
    And I click on "Student First" in the "user" search widget
    And I set the field "Override for Test assignment name" to "1"
    When I set the following fields to these values:
      | Grade for Test assignment name | 10.00 |
      | Feedback for Test assignment name | test data |
    And I press "Save"
    When I am on the "Completion course" course page logged in as student1
    And I should see "Status: Pending"
    And I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    And I reload the page
    Then I should see "Status: Complete"

  @javascript @_file_upload
  Scenario: Course completion should not be updated when teacher imports grades with csv file
    Given I am on the "Completion course" course page logged in as teacher1
    And I navigate to "CSV file" import page in the course gradebook
    And I upload "lib/tests/fixtures/upload_grades.csv" file to "File" filemanager
    And I press "Upload grades"
    And I set the field "Map to" to "Email address"
    And I set the field "Test assignment name" to "Assignment: Test assignment name"
    And I press "Upload grades"
    And I press "Continue"
    And I should see "10.00" in the "Student First" "table_row"
    And I am on the "Completion course" course page logged in as student1
    And I should see "Status: Pending"
    When I run the scheduled task "core\task\completion_regular_task"
    And I wait "1" seconds
    And I run the scheduled task "core\task\completion_regular_task"
    And I reload the page
    Then I should see "Status: Complete"
