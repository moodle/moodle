@mod @mod_forum @core_grades
Feature: I can grade a students by group with separate groups
  In order to assess a student's contributions
  As a teacher
  I can assign grades to a student based on their separate groups

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
    And the following "groups" exist:
      | name | course | idnumber |
      | Group A | C1 | G1 |
      | Group B | C1 | G2 |
      | Group C | C1 | G3 |
    And the following "group members" exist:
      | user | group |
      | student1 | G1 |
      | student2 | G2 |
      | student1 | G3 |
      | student2 | G3 |
      | student3 | G3 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I turn editing mode on
    And I add a "Forum" to section "1"
    And I expand all fieldsets
    And I set the following fields to these values:
      | Forum name | Test Forum 1 |
      | Description | Test |
    And I set the field "Whole forum grading > Type" to "Point"
    And I set the field "Common module settings > Group mode" to "Separate groups"
    And I press "Save and return to course"
    And I follow "Test Forum 1"

  @javascript
  Scenario: Grade users by group A
    When I select "Group A" from the "Separate groups" singleselect
    And I click on "Grade users" "button"
    Then I should see "1 out of 1"
    And I should not see "1 out of 2"
    And I should not see "1 out of 3"
    And I should see "Student 1"
    And I should not see "Student 2"

  @javascript
  Scenario: Grade users by group B
    And I select "Group B" from the "Separate groups" singleselect
    And I click on "Grade users" "button"
    Then I should see "1 out of 1"
    And I should not see "1 out of 2"
    And I should not see "1 out of 3"
    And I should not see "Student 1"
    And I should see "Student 2"

  @javascript
  Scenario: Grade users by group C
    And I select "Group C" from the "Separate groups" singleselect
    And I click on "Grade users" "button"
    Then I should not see "1 out of 1"
    And I should not see "1 out of 2"
    And I should see "1 out of 3"
