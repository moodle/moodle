@gradingform @gradingform_guide
Feature: Marking guides can be created and edited
  In order to use and refine marking guide to grade students
  As a teacher
  I need to edit previously used marking guides

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment 1 name      |
      | Description     | Test assignment description |
      | Grading method  | Marking guide               |
    # Defining a marking guide
    When I go to "Test assignment 1 name" advanced grading definition page
    And I set the following fields to these values:
      | Name        | Assignment 1 marking guide     |
      | Description | Marking guide test description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Guide criterion A | Guide A description for students | Guide A description for markers | 30            |
      | Guide criterion B | Guide B description for students | Guide B description for markers | 30            |
      | Guide criterion C | Guide C description for students | Guide C description for markers | 40            |
    And I define the following frequently used comments:
      | Comment 1 |
      | Comment 2 |
      | Comment 3 |
      | Comment 4 |
    And I press "Save marking guide and make it ready"
    Then I should see "Ready for use"
    And I should see "Guide criterion A"
    And I should see "Guide criterion B"
    And I should see "Guide criterion C"
    And I should see "Comment 1"
    And I should see "Comment 2"
    And I should see "Comment 3"
    And I should see "Comment 4"

  @javascript
  Scenario: Deleting criterion and comment
    # Deleting criterion
    When I go to "Test assignment 1 name" advanced grading definition page
    And I click on "Delete criterion" "button" in the "Guide criterion B" "table_row"
    And I press "Yes"
    And I press "Save"
    Then I should see "Guide criterion A"
    And I should see "Guide criterion C"
    And I should see "WARNING: Your marking guide has a maximum grade of 70 points"
    But I should not see "Guide criterion B"
    # Deleting a frequently used comment
    When I go to "Test assignment 1 name" advanced grading definition page
    And I click on "Delete comment" "button" in the "Comment 3" "table_row"
    And I press "Yes"
    And I press "Save"
    Then I should see "Comment 1"
    And I should see "Comment 2"
    And I should see "Comment 4"
    But I should not see "Comment 3"

  @javascript
  Scenario: Grading and viewing graded marking guide
    # Grading a student.
    When I go to "Student 1" "Test assignment 1 name" activity advanced grading page
    And I grade by filling the marking guide with:
      | Guide criterion A | 25 | Very good  |
      | Guide criterion B | 20 |            |
      | Guide criterion C | 35 | Nice!      |
    # Inserting frequently used comment.
    And I click on "Insert frequently used comment" "button" in the "Guide criterion B" "table_row"
    And I wait "1" seconds
    And I press "Comment 4"
    And I wait "1" seconds
    Then the field "Guide criterion B criterion remark" matches value "Comment 4"
    When I press "Save changes"
    And I press "Ok"
    And I follow "Edit settings"
    And I follow "Test assignment 1 name"
    And I navigate to "View all submissions" in current page administration
    # Checking that the user grade is correct.
    Then I should see "80" in the "Student 1" "table_row"
    And I log out
    # Viewing it as a student.
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment 1 name"
    And I should see "80" in the ".feedback" "css_element"
    And I should see "Marking guide test description" in the ".feedback" "css_element"
    And I should see "Very good"
    And I should see "Comment 4"
    And I should see "Nice!"

  Scenario: I can use marking guides to grade and edit them later updating students grades with Javascript disabled
