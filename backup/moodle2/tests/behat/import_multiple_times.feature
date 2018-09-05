@core @core_backup
Feature: Import course's content's twice
  In order to import content from a course more than one
  As a teacher
  I need to confirm that errors will not happen

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | 1 |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher1 | C2 | editingteacher |
    And the following "blocks" exist:
      | blockname | contextlevel | reference | pagetypepattern | defaultregion |
      | online_users | Course       | C1        | course-view-*   | site-post     |
    And the following "activities" exist:
      | activity   | name      | course | idnumber |
      | quiz       | Test quiz | C1     | quiz1    |
    And I log in as "teacher1"

  Scenario: Import course's contents to another course
    Given I am on "Course 2" course homepage
    And I should not see "Online users"
    And I should not see "Test quiz"
    And I import "Course 1" course into "Course 2" course using this options:
    And I am on "Course 2" course homepage
    And I should see "Online users"
    And I should see "Test quiz"
    When I import "Course 1" course into "Course 2" course using this options:
    And I am on "Course 2" course homepage
    Then I should see "Online users"
    And I should see "Test quiz"
