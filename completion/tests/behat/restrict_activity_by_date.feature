@core @core_completion
Feature: Restrict activity availability through date conditions
  In order to control activity access through date condition
  As a teacher
  I need to set allow access dates to restrict activity access

  Background:
    Given the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@asd.com |
      | student1 | Student | First | student1@asd.com |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable conditional access | 1 |
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    # Adding the page like this because id_available*_enabled needs to be clicked to trigger the action.
    And I add a "Assignment" to section "1"
    And I expand all fieldsets

  @javascript
  Scenario: Show activity greyed-out to students when available from date is in future
    Given I click on "id_availablefrom_enabled" "checkbox"
    And I fill the moodle form with:
      | Assignment name | Test assignment 1 |
      | Description | This assignment is restricted by date |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | id_availablefrom_day | 31 |
      | id_availablefrom_month | 12 |
      | id_availablefrom_year | 2037 |
      | id_showavailability | 1 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    Then I should see "Available from 31 December 2037."
    And "Test assignment 1" activity should be hidden
    And I log out

  @javascript
  Scenario: Show activity hidden to students when available until date is in past
    Given I click on "id_availableuntil_enabled" "checkbox"
    And I fill the moodle form with:
      | Assignment name | Test assignment 2 |
      | Description | This assignment is restricted by date |
      | assignsubmission_onlinetext_enabled | 1 |
      | assignsubmission_file_enabled | 0 |
      | id_availableuntil_day | 1 |
      | id_availableuntil_month | 2 |
      | id_availableuntil_year | 2013 |
      | id_showavailability | 0 |
    And I press "Save and return to course"
    And I log out
    When I log in as "student1"
    And I follow "Course 1"
    Then I should not see "Test assignment 2"
