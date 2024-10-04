@block @block_activity_results @javascript
Feature: The activity results block displays student high scores
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
      | activity   | name            | intro          | course | section | idnumber | assignsubmission_file_enabled |
      | assign     | Test assignment | Offline text   | C1     | 1       | assign1  | 0                             |
    And the following "grade grades" exist:
      | gradeitem       | user     | grade |
      | Test assignment | student1 | 90.00 |
      | Test assignment | student2 | 80.00 |
      | Test assignment | student3 | 70.00 |
      | Test assignment | student4 | 60.00 |
      | Test assignment | student5 | 50.00 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Configure the block on the course page to show 0 high scores
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 0 |
      | config_showworst | 0 |
      | config_gradeformat | Percentages |
      | config_nameformat | Display full names |
    Then I should see "This block's configuration currently does not allow it to show any results." in the "Activity results" "block"

  Scenario: Configure the block on the course page to show 1 high score
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 1 |
      | config_showworst | 0 |
      | config_gradeformat | Percentages |
      | config_nameformat | Display full names |
      | config_decimalpoints | 0 |
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90%" in the "Activity results" "block"

  Scenario: Try to configure the block on the course page to show 1 high score as a fraction
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 1 |
      | config_showworst | 0 |
      | config_gradeformat | Fractions |
      | config_nameformat | Display full names |
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90.00/100.00" in the "Activity results" "block"

  Scenario: Try to configure the block on the course page to show 1 high score as a absolute numbers
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 1 |
      | config_showworst | 0 |
      | config_gradeformat | Absolute numbers |
      | config_nameformat | Display full names |
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90.00" in the "Activity results" "block"

  Scenario: Try to configure the block on the course page to show multiple high scores as percentages
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 3 |
      | config_showworst | 0 |
      | config_gradeformat | Percentages |
      | config_nameformat | Display full names |
      | config_decimalpoints | 0 |
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90%" in the "Activity results" "block"
    And I should see "Student 2" in the "Activity results" "block"
    And I should see "80%" in the "Activity results" "block"
    And I should see "Student 3" in the "Activity results" "block"
    And I should see "70%" in the "Activity results" "block"

  Scenario: Try to configure the block on the course page to show multiple high scores as fractions
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 3 |
      | config_showworst | 0 |
      | config_gradeformat | Fractions |
      | config_nameformat | Display full names |
    Then I should see "Student 1" in the "Activity results" "block"
    And I should see "90.00/100.00" in the "Activity results" "block"
    And I should see "Student 2" in the "Activity results" "block"
    And I should see "80.00/100.00" in the "Activity results" "block"
    And I should see "Student 3" in the "Activity results" "block"
    And I should see "70.00/100.00" in the "Activity results" "block"

  Scenario: Try to configure the block on the course page to show multiple high scores as absolute numbers
    Given I add the "Activity results" block to the default region with:
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

  Scenario: Try to configure the block on the course page to show multiple high scores using ID numbers
    Given the following config values are set as admin:
      | showuseridentity | idnumber,email |
    And I add the "Activity results" block to the default region with:
      | config_showbest | 3 |
      | config_showworst | 0 |
      | config_gradeformat | Percentages |
      | config_nameformat | Display only ID numbers |
    Then I should see "User S1" in the "Activity results" "block"
    And I should see "90.00%" in the "Activity results" "block"
    And I should see "User S2" in the "Activity results" "block"
    And I should see "80.00%" in the "Activity results" "block"
    And I should see "User S3" in the "Activity results" "block"
    And I should see "70.00%" in the "Activity results" "block"

  Scenario: Try to configure the block on the course page to show multiple high scores using anonymous names
    Given I add the "Activity results" block to the default region with:
      | config_showbest | 3 |
      | config_showworst | 0 |
      | config_gradeformat | Percentages |
      | config_nameformat | Anonymous results |
    Then I should see "User" in the "Activity results" "block"
    And I should see "90.00%" in the "Activity results" "block"
    And I should see "80.00%" in the "Activity results" "block"
    And I should see "70.00%" in the "Activity results" "block"
