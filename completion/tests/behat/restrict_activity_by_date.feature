@core @core_completion
Feature: Restrict activity availability through date conditions
  In order to control activity access through date condition
  As a teacher
  I need to set allow access dates to restrict activity access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | First    | teacher1@example.com |
      | student1 | Student   | First    | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity                            | assign                                |
      | course                              | C1                                    |
      | section                             | 1                                     |
      | name                                | Test assignment 1                     |
      | intro                               | This assignment is restricted by date |
      | assignsubmission_onlinetext_enabled | 1                                     |
      | assignsubmission_file_enabled       | 0                                     |
    And I am on the "Test assignment 1" "assign activity" page logged in as "teacher1"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets

  @javascript
  Scenario: Show activity greyed-out to students when available from date is in future
    Given I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | x[day] | 31 |
      | x[month] | 12 |
      | x[year] | 2037 |
    And I press "Save and return to course"
    And I log out
    When I am on the "Course 1" course page logged in as student1
    Then I should see "Available from 31 December 2037"
    And "Test assignment 1" "link" should not exist in the "page" "region"

  @javascript
  Scenario: Show activity hidden to students when available until date is in past
    Given I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the following fields to these values:
      | x[day] | 1 |
      | x[month] | 2 |
      | x[year] | 2013 |
      | Direction | until |
    # Click eye icon to hide it when not available.
    And I click on ".availability-item .availability-eye img" "css_element"
    And I press "Save and return to course"
    And I log out
    When I am on the "Course 1" course page logged in as student1
    Then I should not see "Test assignment 1" in the "page" "region"
