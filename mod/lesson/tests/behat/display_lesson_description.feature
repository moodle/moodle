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
    Given the following "activity" exists:
      | activity | lesson                  |
      | course   | C1                      |
      | idnumber | 0001                    |
      | name     | Test lesson name        |
      | intro    | Test lesson description |
    And I am on the "Test lesson name" "lesson activity" page logged in as teacher1
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title  | Test lesson part 1        |
      | Description | Lesson part 1 description |
      | Jump        | Next page                 |
    And I click on "Save page" "button"

  Scenario: Description is displayed in the Lesson
    When I am on the "Test lesson name" "lesson activity" page
    Then I should see "Test lesson description"

  Scenario: Show lesson description in the course homepage
    Given I am on the "Test lesson name" "lesson activity editing" page
    And the following fields match these values:
      | Display description on course page | |
    And I set the following fields to these values:
      | Display description on course page | 1 |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should see "Test lesson description"

  Scenario: Hide lesson description in the course homepage
    Given I am on the "Test lesson name" "lesson activity editing" page
    And the following fields match these values:
      | Display description on course page | |
    And I press "Save and return to course"
    When I am on "Course 1" course homepage
    Then I should not see "Test lesson description"
