@gradingform @gradingform_guide
Feature: Publish guide as templates
  In order to save time to teachers
  As a manager
  I need to publish guides and make them available to all teachers

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | course | idnumber | name                   | intro | advancedgradingmethod_submissions |
      | assign   | C1     | A1       | Test assignment 1 name | TA1   | guide                             |
      | assign   | C1     | A2       | Test assignment 2 name | TA2   | guide                             |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "system role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |
    And I log in as "manager1"
    And I am on "Course 1" course homepage
    And I go to "Test assignment 1 name" advanced grading definition page
    And I set the following fields to these values:
      | Name        | Assignment 1 marking guide     |
      | Description | Marking guide test description |
    And I define the following marking guide:
      | Criterion name    | Description for students         | Description for markers         | Maximum score |
      | Guide criterion A | Guide A description for students | Guide A description for markers | 40            |
      | Guide criterion B | Guide B description for students | Guide B description for markers | 60            |
    And I define the following frequently used comments:
      | Comment 1 |
    And I press "Save marking guide and make it ready"
    And I publish "Test assignment 1 name" grading form definition as a public template
    And I log out

  Scenario: Pick grading form from public template
    When I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I go to "Test assignment 2 name" advanced grading page
    And I set "Test assignment 2 name" activity to use "Assignment 1 marking guide" grading form
    Then I should see "Ready for use"
    And I should see "Assignment 1 marking guide"
    And I should see "Marking guide test description"
    And I should see "Guide criterion A"
    And I should see "Guide criterion B"
    And I should see "Comment 1"
