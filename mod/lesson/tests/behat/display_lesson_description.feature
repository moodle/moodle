@mod @mod_lesson
Feature: Display the lesson description in the lesson and optionally in the course
  In order to display the the lesson description description in the course
  As a teacher
  I need to enable the 'Display description on course page' setting.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Lesson" to section "1"
    And I set the following fields to these values:
      | Name | Test lesson |
      | Description | Test lesson description |
    And I click on "Save and display" "button"

  Scenario: Description is displayed in the Lesson
    Given I am on "Course 1" course homepage
    When I follow "Test lesson"
    Then I should see "Test lesson description"

  Scenario: Show lesson description in the course homepage
    Given I am on "Course 1" course homepage
    And I follow "Test lesson"
    And I navigate to "Edit settings" in current page administration
    And the following fields match these values:
      | Display description on course page | |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should see "Test lesson description"

  Scenario: Hide lesson description in the course homepage
    Given I am on "Course 1" course homepage
    And I follow "Test lesson"
    And I navigate to "Edit settings" in current page administration
    And the following fields match these values:
      | Display description on course page | |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should not see "Test lesson description"
