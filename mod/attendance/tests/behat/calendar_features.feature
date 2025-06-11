@mod @mod_attendance
Feature: Test the calendar related features in the attendance module

  Background:
    Given the following "courses" exist:
      | fullname | shortname | summary                             | category | timecreated   | timemodified  |
      | Course 1 | C1        | Prove the attendance activity works | 0        | ##yesterday## | ##yesterday## |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | course | user     | role           | timestart     |
      | C1     | student1 | student        | ##yesterday## |
      | C1     | teacher1 | editingteacher | ##yesterday## |
    And the following "activity" exists:
      | activity | attendance      |
      | course   | C1              |
      | idnumber | 00001           |
      | name     | Test attendance |
    And the following "blocks" exist:
      | blockname         | contextlevel | reference | pagetypepattern | defaultregion |
      | calendar_month    | Course       | C1        | course-view-*   | side-pre      |
    And I log in as "teacher1"

  @javascript
  Scenario: Calendar events can be created automatically with sessions creation
    Given I am on the "Test attendance" "mod_attendance > View" page
    And I click on "Add session" "button"
    And I set the following fields to these values:
      | id_sestime_starthour   | 23 |
      | id_sestime_startminute | 00 |
      | id_sestime_endhour     | 23 |
      | id_sestime_endminute   | 55 |
    And I click on "id_submitbutton" "button"
    And I am on "Course 1" course homepage
    And I follow "Course calendar"
    Then I should see "Test attendance"
