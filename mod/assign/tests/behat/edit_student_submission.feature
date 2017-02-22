@mod @mod_assign @javascript
Feature: In an assignment, the administrator can edit students' submissions
  In order to edit a student's submissions
  As an administrator
  I need to grade multiple students on one page

  Scenario: Editing a student's submission
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1 | 0 | 1 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
    When I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name | Test assignment name |
      | Description | Submit your online text |
      | assignsubmission_onlinetext_enabled | 1 |
      | groupmode | No groups |
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I press "Add submission"
    And I set the following fields to these values:
      | Online text | I'm the student1 submission |
    And I press "Save changes"
    And I log out
    And I log in as "admin"
    And I am on site homepage
    And I follow "Course 1"
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | Have you seen the movie Chef? |
    And I press "Save changes"
    And I navigate to "View all submissions" in current page administration
    Then I should see "Have you seen the movie Chef?"
    And I click on "Edit" "link" in the "Student 1" "table_row"
    And I choose "Edit submission" in the open action menu
    And I set the following fields to these values:
      | Online text | I have seen the movie chef. |
    And I press "Save changes"
    And I navigate to "View all submissions" in current page administration
    Then I should see "I have seen the movie chef."
