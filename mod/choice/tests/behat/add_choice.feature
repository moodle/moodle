@mod @mod_choice
Feature: Add choice activity
  In order to ask questions as a choice of multiple responses
  As a teacher
  I need to add choice activities to courses

  Scenario: Add a choice activity and complete the activity as a student
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option             | section |
      | choice   | Choice name | Choice Description | C1     | choice1  | Option 1, Option 2 | 1       |
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I choose "Option 1" from "Choice name" choice activity
    Then I should see "Your selection: Option 1"
    And I should see "Your choice has been saved"
