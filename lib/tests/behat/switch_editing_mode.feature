@core @turn_edit_mode_on @javascript
Feature: Turn editing mode on
  Users should be able to turn editing mode on and off

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I turn editing mode off
    And I log out

  Scenario: Edit mode on page Gradebook
    Given the following "activities" exist:
      | activity | course | idnumber | name              | intro             |
      | assign   | C1     | assign1  | Test Assignment 1 | Test Assignment 1 |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"
    And I turn editing mode on
    And I click on grade item menu "Test Assignment 1" of type "gradeitem" on "grader" page
    And "Edit grade item" "link" should exist
    And I turn editing mode off
    And I click on grade item menu "Test Assignment 1" of type "gradeitem" on "grader" page
    Then "Edit grade item" "link" should not exist

  Scenario: Edit mode on page Homepage
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I should see "Add an activity or resource"
    And I turn editing mode off
    Then I should not see "Add an activity or resource"

  Scenario: Edit mode on page Default profile
    Given I log in as "admin"
    And I navigate to "Appearance > Default profile page" in site administration
    And I turn editing mode on
    And I should see "Add a block"
    And I turn editing mode off
    Then I should not see "Add a block"

  Scenario: Edit mode on page Profile
    Given I log in as "admin"
    And I follow "View profile"
    And I turn editing mode on
    And I should see "Add a block"
    And I turn editing mode off
    Then I should not see "Add a block"

  Scenario: Edit mode on page Default dashboard
    Given I log in as "admin"
    And I navigate to "Appearance > Default Dashboard page" in site administration
    And I turn editing mode on
    And I should see "Add a block"
    And I turn editing mode off
    Then I should not see "Add a block"

  Scenario: Edit mode on page Dashboard
    And I log in as "teacher1"
    And I turn editing mode on
    And I should see "Add a block"
    Then I turn editing mode off
    Then I should not see "Add a block"
