@mod @mod_lesson
Feature: A teacher can set available from and deadline dates to access a lesson
  In order to schedule lesson activities
  As a teacher
  I need to set available from and deadline dates

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

  Scenario: Forbidding lesson accesses until a specified date
    Given I add a "Lesson" to section "1"
    And I expand all fieldsets
    And I set the field "id_available_enabled" to "1"
    And I set the following fields to these values:
      | Name | Test lesson |
      | Description | Test lesson description |
      | available[day] | 1 |
      | available[month] | January |
      | available[year] | 2030 |
      | available[hour] | 08 |
      | available[minute] | 00 |
    And I press "Save and display"
    And I follow "Test lesson"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | Description | The first one |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test lesson"
    Then I should see "This lesson will be open on Tuesday, 1 January 2030, 8:00"
    And I should not see "First page contents"

  Scenario: Forbidding lesson accesses until a specified date
    Given I add a "Lesson" to section "1"
    And I set the field "id_deadline_enabled" to "1"
    And I set the following fields to these values:
      | Name | Test lesson |
      | Description | Test lesson description |
      | deadline[day] | 1 |
      | deadline[month] | January |
      | deadline[year] | 2000 |
      | deadline[hour] | 08 |
      | deadline[minute] | 00 |
    And I press "Save and display"
    And I follow "Test lesson"
    And I follow "Add a content page"
    And I set the following fields to these values:
      | Page title | First page name |
      | Page contents | First page contents |
      | Description | The first one |
    And I press "Save page"
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    When I follow "Test lesson"
    Then I should see "This lesson closed on Saturday, 1 January 2000, 8:00"
    And I should not see "First page contents"
