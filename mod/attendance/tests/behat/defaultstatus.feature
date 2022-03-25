@mod @mod_attendance
Feature: Admin can set default status set for use in new attendance

  Background:
    Given the following "courses" exist:
      | fullname | shortname | summary                             | category | timecreated   | timemodified  |
      | Course 1 | C1        | Prove the attendance activity works | 0        | ##yesterday## | ##yesterday## |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | course | user     | role           | timestart     |
      | C1     | teacher1 | editingteacher | ##yesterday## |
    And I log in as "admin"
    And I navigate to "Plugins > Attendance" in site administration
    And I follow "Default status set"
    And I set the field with xpath "//*[@id='preferencesform']/table/tbody/tr[2]/td[3]/input" to "customstatusdescription"
    And I click on "Update" "button" in the "#preferencesform" "css_element"
    And I should see "Status updated"
    And I log out

  @javascript
  Scenario: Modified default status set added to new attendance
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Attendance" to section "1" and I fill the form with:
      | Name        | Attendance1       |
    And I follow "Attendance1"
    And I follow "Status set"
    Then the field with xpath "//*[@id='preferencesform']/table/tbody/tr[2]/td[3]/input" matches value "customstatusdescription"
