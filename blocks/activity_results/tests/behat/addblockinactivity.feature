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
    And the following "grade grades" exist:
      | gradeitem         | user     | grade |
      | Test assignment 1 | student1 | 90.00 |
      | Test assignment 1 | student2 | 80.00 |
      | Test assignment 1 | student3 | 70.00 |
      | Test assignment 1 | student4 | 60.00 |
      | Test assignment 1 | student5 | 50.00 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Configure the block on a non-graded activity to show 3 high scores
    Given I am on the "Test page name" "page activity" page
    And I add the "Activity results" block to the default region with:
      | config_activitygradeitemid | Test assignment 1 |
      | config_showbest | 3 |
      | config_showworst | 0 |
      | config_gradeformat | Absolute numbers |
      | config_nameformat | Display full names |
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90.00" in the "Activity results" "block"
    And I should see "Student 2" in the "Activity results" "block"
    And I should see "80.00" in the "Activity results" "block"
    And I should see "Student 3" in the "Activity results" "block"
    And I should see "70.00" in the "Activity results" "block"

  @javascript @addablocklink
  Scenario: Block should select current activity by default
    Given I click on "Test assignment 1" "link" in the "region-main" "region"
    When I add the "Activity results..." block
    Then the field "config_activitygradeitemid" in the "Add Activity results block" "dialogue" matches value "Test assignment 1"
    And I click on "Save changes" "button" in the "Add Activity results block" "dialogue"
    And I am on "Course 1" course homepage
    And I click on "Test assignment 2" "link" in the "region-main" "region"
    And I add the "Activity results..." block
    And the field "config_activitygradeitemid" in the "Add Activity results block" "dialogue" matches value "Test assignment 2"
    And I click on "Save changes" "button" in the "Add Activity results block" "dialogue"
    And I am on "Course 1" course homepage
    And I click on "Test assignment 3" "link" in the "region-main" "region"
    And I add the "Activity results..." block
    And the field "config_activitygradeitemid" in the "Add Activity results block" "dialogue" matches value "Test assignment 3"
    And I click on "Save changes" "button" in the "Add Activity results block" "dialogue"
    And I am on "Course 1" course homepage
    And I click on "Test page name" "link" in the "region-main" "region"
    And I add the "Activity results..." block
    And the field "config_activitygradeitemid" in the "Add Activity results block" "dialogue" does not match value "Test page name"
    And I click on "Save changes" "button" in the "Add Activity results block" "dialogue"
