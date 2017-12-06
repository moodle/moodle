@mod @mod_attendance @javascript
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
      | course | user        | role           | timestart     |
      | C1     | student1    | student        | ##yesterday## |
      | C1     | teacher1    | editingteacher | ##yesterday## |

    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Upcoming events" block
    And I add a "Attendance" to section "1" and I fill the form with:
      | Name | Test attendance |
    And I log out

  Scenario: Calendar events can be created automatically with sessions creation
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Test attendance"
    And I follow "Add session"
    And I set the following fields to these values:
      | id_sestime_starthour | 01 |
      | id_sestime_endhour   | 02 |
    And I click on "id_submitbutton" "button"
    And I am on "Course 1" course homepage
    And I follow "Go to calendar"
    And I should see "Test attendance"
    And I log out
    And I log in as "student1"
    And I follow "Go to calendar"
    Then I should see "Test attendance"
