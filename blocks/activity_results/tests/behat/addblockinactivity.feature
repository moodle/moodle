@block @block_activity_results @javascript
Feature: The activity results block displays student scores
  In order to be display student scores
  As a user
  I need to see the activity results block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | teacher1 | Teacher | 1 | teacher1@example.com | T1 |
      | student1 | Student | 1 | student1@example.com | S1 |
      | student2 | Student | 2 | student2@example.com | S2 |
      | student3 | Student | 3 | student3@example.com | S3 |
      | student4 | Student | 4 | student4@example.com | S4 |
      | student5 | Student | 5 | student5@example.com | S5 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
      | student5 | C1 | student |
    And the following "activities" exist:
      | activity   | name                | intro          | course | section | idnumber | assignsubmission_file_enabled |
      | assign     | Test assignment 1   | Offline text   | C1     | 1       | assign1  | 0                             |
      | assign     | Test assignment 2   | Offline text   | C1     | 1       | assign2  | 0                             |
      | assign     | Test assignment 3   | Offline text   | C1     | 1       | assign3  | 0                             |
    And the following "activities" exist:
      | activity   | name                | content          | course | section | idnumber |
      | page       |  Test page name     | This is a page   | C1     | 1       | page1    |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I should see "Test page name"
    And I navigate to "View > Grader report" in the course gradebook
    And I change window size to "large"
    And I give the grade "90.00" to the user "Student 1" for the grade item "Test assignment 1"
    And I give the grade "80.00" to the user "Student 2" for the grade item "Test assignment 1"
    And I give the grade "70.00" to the user "Student 3" for the grade item "Test assignment 1"
    And I give the grade "60.00" to the user "Student 4" for the grade item "Test assignment 1"
    And I give the grade "50.00" to the user "Student 5" for the grade item "Test assignment 1"
    And I press "Save changes"
    And I am on "Course 1" course homepage

  Scenario: Configure the block on a non-graded activity to show 3 high scores
    Given I am on the "Test page name" "page activity" page
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | config_activitygradeitemid | Test assignment 1 |
      | config_showbest | 3 |
      | config_showworst | 0 |
      | config_gradeformat | Absolute numbers |
      | config_nameformat | Display full names |
    And I press "Save changes"
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90.00" in the "Activity results" "block"
    And I should see "Student 2" in the "Activity results" "block"
    And I should see "80.00" in the "Activity results" "block"
    And I should see "Student 3" in the "Activity results" "block"
    And I should see "70.00" in the "Activity results" "block"

  Scenario: Block should select current activity by default
    Given I am on the "Test assignment 1" "assign activity" page
    When I add the "Activity results" block
    And I configure the "Activity results" block
    Then the field "config_activitygradeitemid" matches value "Test assignment 1"
    And I press "Cancel"
    And I am on "Course 1" course homepage
    And I am on the "Test assignment 2" "assign activity" page
    And I add the "Activity results" block
    And I configure the "Activity results" block
    And the field "config_activitygradeitemid" matches value "Test assignment 2"
    And I press "Cancel"
    And I am on "Course 1" course homepage
    And I am on the "Test assignment 3" "assign activity" page
    And I add the "Activity results" block
    And I configure the "Activity results" block
    And the field "config_activitygradeitemid" matches value "Test assignment 3"
    And I press "Cancel"
    And I am on "Course 1" course homepage
    And I am on the "Test page name" "page activity" page
    And I add the "Activity results" block
    And I configure the "Activity results" block
    And the field "config_activitygradeitemid" does not match value "Test page name"
