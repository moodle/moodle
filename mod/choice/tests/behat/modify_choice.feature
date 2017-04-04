@mod @mod_choice
Feature: Teacher can modify choices of the students
  In order to have all students choices
  As a teacher
  I need to be able to make choice for studnets

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | student3 | Student | 3 | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | student3 | C1 | student |
    And the following "activities" exist:
      | activity | name        | intro                   | course | idnumber | option |
      | choice   | Choice name | Test choice description | C1     | choice1  | Option 1, Option 2, Option 3 |

  @javascript
  Scenario: Delete students choice response as a teacher
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose "Option 1" from "Choice name" choice activity
    Then I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "View 1 responses" in current page administration
    And I click on "Student 1 Option 1" "checkbox"
    And I select "Delete" from the "With selected" singleselect
    And "Student 1 Option 1" "checkbox" should not exist
    And "Student 1 Not answered yet" "checkbox" should exist
    And I log out

  @javascript
  Scenario: Teacher set answers of students who did not respond or change existing answers
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose "Option 1" from "Choice name" choice activity
    Then I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "View 1 responses" in current page administration
    And I click on "Student 1 Option 1" "checkbox"
    And I click on "Student 2 Not answered yet" "checkbox"
    And I click on "Student 3 Not answered yet" "checkbox"
    And I select "Choose: Option 2" from the "With selected" singleselect
    And "Student 1 Option 1" "checkbox" should not exist
    And "Student 2 Not answered yet" "checkbox" should not exist
    And "Student 3 Not answered yet" "checkbox" should not exist
    And "Student 1 Option 2" "checkbox" should exist
    And "Student 2 Option 2" "checkbox" should exist
    And "Student 3 Option 2" "checkbox" should exist
    And I log out

  @javascript
  Scenario: Teacher can delete answers in the multiple answer choice
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Edit settings" in current page administration
    And I set the field "Allow more than one choice to be selected" to "Yes"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose options "Option 1","Option 2" from "Choice name" choice activity
    And I should see "Your selection: Option 1; Option 2"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "View 1 responses" in current page administration
    And I click on "Student 1 Option 2" "checkbox"
    And I select "Delete" from the "With selected" singleselect
    And I click on "Student 1 Option 1" "checkbox"
    And I select "Choose: Option 3" from the "With selected" singleselect
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I should see "Your selection: Option 1; Option 3"
    And I log out

  @javascript
  Scenario: Teacher can manage answers on view page if the names are displayed
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose "Option 1" from "Choice name" choice activity
    Then I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Publish results | Always show results to students |
      | Privacy of results | Publish full results, showing names and their choices |
      | Show column for unanswered | Yes |
    And I press "Save and display"
    And I click on "Student 1 Option 1" "checkbox"
    And I click on "Student 2 Not answered yet" "checkbox"
    And I select "Choose: Option 3" from the "With selected" singleselect
    And "Student 1 Option 1" "checkbox" should not exist
    And "Student 1 Option 3" "checkbox" should exist
    And "Student 2 Not answered yet" "checkbox" should not exist
    And "Student 2 Option 3" "checkbox" should exist
    And I click on "Student 1 Option 3" "checkbox"
    And I select "Delete" from the "With selected" singleselect
    And "Student 1 Option 3" "checkbox" should not exist
    And "Student 1 Not answered yet" "checkbox" should exist
    And I log out
