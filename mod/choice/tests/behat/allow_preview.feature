@mod @mod_choice
Feature: Allow choice preview
  In order to allow students to preview options before a choice activity is opened for submission
  As a teacher
  I need to enable the choice preview option

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
    And the following "activity" exists:
      | activity | choice             |
      | course   | C1                 |
      | idnumber | choice1            |
      | name     | Choice name        |
      | intro    | Choice Description |
      | section  | 1                  |
      | option   | Option 1, Option 2 |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  Scenario: Enable the choice preview option and view the activity as a student before the opening time
    And I follow "Choice name"
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | timeopen[enabled] | 1 |
      | timeclose[enabled] | 1 |
      | timeopen[day] | 30 |
      | timeopen[month] | December |
      | timeopen[year] | 2037 |
      | timeclose[day] | 31 |
      | timeclose[month] | December |
      | timeclose[year] | 2037 |
      | Show preview | 1 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Choice name"
    Then I should see "This is just a preview of the available options for this activity"
    And the "choice_1" "radio" should be disabled
    And the "choice_2" "radio" should be disabled
    And "Save my choice" "button" should not exist
