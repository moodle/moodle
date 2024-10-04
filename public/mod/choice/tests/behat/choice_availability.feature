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
    Given the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option             | section |
      | choice   | Choice name | Choice Description | C1     | choice1  | Option 1, Option 2 | 1       |
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | timeopen[enabled] | 1 |
      | timeopen[day] | 30 |
      | timeopen[month] | December |
      | timeopen[year] | 2037 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    Then "choice_1" "radio" should not exist
    And "choice_2" "radio" should not exist
    And "Save my choice" "button" should not exist

  Scenario: Enable the choice activity with a start deadline in the past
    Given the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option             | section |
      | choice   | Choice name | Choice Description | C1     | choice1  | Option 1, Option 2 | 1       |
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | timeopen[enabled] | 1 |
      | timeopen[day] | 30 |
      | timeopen[month] | December |
      | timeopen[year] | 2007 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And "choice_1" "radio" should exist
    And "choice_2" "radio" should exist
    And "Save my choice" "button" should exist

  Scenario: Enable the choice activity with a end deadline in the future
    Given the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option             | section |
      | choice   | Choice name | Choice Description | C1     | choice1  | Option 1, Option 2 | 1       |
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | timeclose[enabled] | 1 |
      | timeclose[day] | 30 |
      | timeclose[month] | December |
      | timeclose[year] | 2037 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And "choice_1" "radio" should exist
    And "choice_2" "radio" should exist
    And "Save my choice" "button" should exist

  Scenario: Enable the choice activity with a end deadline in the past
    Given the following "activities" exist:
      | activity | name        | intro              | course | idnumber | option             | section |
      | choice   | Choice name | Choice Description | C1     | choice1  | Option 1, Option 2 | 1       |
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | timeclose[enabled] | 1 |
      | timeclose[day] | 30 |
      | timeclose[month] | December |
      | timeclose[year] | 2007 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    Then "choice_1" "radio" should not exist
    And "choice_2" "radio" should not exist
    And "Save my choice" "button" should not exist
