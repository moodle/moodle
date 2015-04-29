@block @block_activity_results
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
      | student6 | Student | 6 | student6@example.com | S6 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
      | Group 3 | C1 | G3 |
      | Group 4 | C1 | G4 |
      | Group 5 | C1 | G5 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
      | student4 | C1 | student |
      | student5 | C1 | student |
      | student6 | C1 | student |

  @javascript
  Scenario: Configure the block on the course page to show 1 high score
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 1 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Percentages |
      | id_config_nameformat | Display full names |
      | id_config_decimalpoints | 0 |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group 1" in the "Activity results" "block"
    And I should see "95%" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show 1 high score as a fraction
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 1 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Fractions |
      | id_config_nameformat | Display full names |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group 1" in the "Activity results" "block"
    And I should see "95.00/100.00" in the "Activity results" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Student 1" in the "Activity results" "block"
    And I should see "100.00/100.00" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show 1 high score as a absolute numbers
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 1 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Absolute numbers |
      | id_config_nameformat | Display full names |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group 1" in the "Activity results" "block"
    And I should see "95.00" in the "Activity results" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Student 1" in the "Activity results" "block"
    And I should see "100.00" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show multiple high scores as percentages
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 3 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Percentages |
      | id_config_nameformat | Display full names |
      | id_config_decimalpoints | 0 |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group 1" in the "Activity results" "block"
    And I should see "95%" in the "Activity results" "block"
    And I should see "Group 2" in the "Activity results" "block"
    And I should see "85%" in the "Activity results" "block"
    And I should see "Group 3" in the "Activity results" "block"
    And I should see "75%" in the "Activity results" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Student 1" in the "Activity results" "block"
    And I should see "100%" in the "Activity results" "block"
    And I should see "Student 2" in the "Activity results" "block"
    And I should see "90%" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show multiple high scores as fractions
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 3 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Fractions |
      | id_config_nameformat | Display full names |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group 1" in the "Activity results" "block"
    And I should see "95.00/100.00" in the "Activity results" "block"
    And I should see "Group 2" in the "Activity results" "block"
    And I should see "85.00/100.00" in the "Activity results" "block"
    And I should see "Group 3" in the "Activity results" "block"
    And I should see "75.00/100.00" in the "Activity results" "block"
    And I log out
    And I log in as "student3"
    And I follow "Course 1"
    And I should see "Student 3" in the "Activity results" "block"
    And I should see "90.00/100.00" in the "Activity results" "block"
    And I should see "Student 4" in the "Activity results" "block"
    And I should see "80.00/100.00" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show multiple high scores as absolute numbers
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 3 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Absolute numbers |
      | id_config_nameformat | Display full names |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group 1" in the "Activity results" "block"
    And I should see "95.00" in the "Activity results" "block"
    And I should see "Group 2" in the "Activity results" "block"
    And I should see "85.00" in the "Activity results" "block"
    And I should see "Group 3" in the "Activity results" "block"
    And I should see "75.00" in the "Activity results" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "Student 1" in the "Activity results" "block"
    And I should see "100.00" in the "Activity results" "block"
    And I should see "Student 2" in the "Activity results" "block"
    And I should see "90.00" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show multiple high scores using ID numbers
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 3 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Percentages |
      | id_config_nameformat | Display only ID numbers |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group" in the "Activity results" "block"
    And I should see "95.00%" in the "Activity results" "block"
    And I should see "85.00%" in the "Activity results" "block"
    And I should see "75.00%" in the "Activity results" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "User S1" in the "Activity results" "block"
    And I should see "100.00%" in the "Activity results" "block"
    And I should see "User S2" in the "Activity results" "block"
    And I should see "90.00%" in the "Activity results" "block"

  @javascript
  Scenario: Try to configure the block on the course page to show multiple high scores using anonymous names
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I expand "Users" node
    And I follow "Groups"
    And I add "Student 1 (student1@example.com)" user to "Group 1" group members
    And I add "Student 2 (student2@example.com)" user to "Group 1" group members
    And I add "Student 3 (student3@example.com)" user to "Group 2" group members
    And I add "Student 4 (student4@example.com)" user to "Group 2" group members
    And I add "Student 5 (student5@example.com)" user to "Group 3" group members
    And I add "Student 6 (student6@example.com)" user to "Group 3" group members
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment |
      | Description | Offline text |
      | assignsubmission_file_enabled | 0 |
      | Group mode | Separate groups |
    And I follow "Course 1"
    And I navigate to "Grades" node in "Course administration"
    And I turn editing mode on
    And I give the grade "100.00" to the user "Student 1" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 2" for the grade item "Test assignment"
    And I give the grade "90.00" to the user "Student 3" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 4" for the grade item "Test assignment"
    And I give the grade "80.00" to the user "Student 5" for the grade item "Test assignment"
    And I give the grade "70.00" to the user "Student 6" for the grade item "Test assignment"
    And I press "Save changes"
    And I follow "Course 1"
    And I add the "Activity results" block
    When I configure the "Activity results" block
    And I set the following fields to these values:
      | id_config_showbest | 3 |
      | id_config_showworst | 0 |
      | id_config_gradeformat | Percentages |
      | id_config_nameformat | Anonymous results |
      | id_config_usegroups | Yes |
    And I press "Save changes"
    Then I should see "Group" in the "Activity results" "block"
    And I should see "95.00%" in the "Activity results" "block"
    And I should see "85.00%" in the "Activity results" "block"
    And I should see "75.00%" in the "Activity results" "block"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I should see "User" in the "Activity results" "block"
    And I should see "100.00%" in the "Activity results" "block"
    And I should see "90.00%" in the "Activity results" "block"