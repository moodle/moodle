@mod @mod_choice
Feature: Restrict availability of the choice module to a deadline
  In order to limit the time a student can mace a selection
  As a teacher
  I need to restrict answering to within a time period

  Background:
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
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Enable the choice activity with a start deadline in the future
    Given I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | timeopen[enabled] | 1 |
      | timeopen[day] | 30 |
      | timeopen[month] | December |
      | timeopen[year] | 2037 |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    Then I should see "Sorry, this activity is not available until"

  Scenario: Enable the choice activity with a start deadline in the past
    Given I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | timeopen[enabled] | 1 |
      | timeopen[day] | 30 |
      | timeopen[month] | December |
      | timeopen[year] | 2007 |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And "choice_1" "radio" should exist
    And "choice_2" "radio" should exist
    And "Save my choice" "button" should exist

  Scenario: Enable the choice activity with a end deadline in the future
    Given I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | timeclose[enabled] | 1 |
      | timeclose[day] | 30 |
      | timeclose[month] | December |
      | timeclose[year] | 2037 |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And "choice_1" "radio" should exist
    And "choice_2" "radio" should exist
    And "Save my choice" "button" should exist

  Scenario: Enable the choice activity with a end deadline in the past
    Given I add a "Choice" to section "1" and I fill the form with:
      | Choice name | Choice name |
      | Description | Choice Description |
      | option[0] | Option 1 |
      | option[1] | Option 2 |
      | timeclose[enabled] | 1 |
      | timeclose[day] | 30 |
      | timeclose[month] | December |
      | timeclose[year] | 2007 |
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    Then I should see "Sorry, this activity closed on"
