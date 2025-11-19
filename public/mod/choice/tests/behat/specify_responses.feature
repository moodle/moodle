@mod @mod_choice
Feature: Specify any number of choice responses
  In order to make a choice activity
  As a teacher
  I need to be able to add any number of responses

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name       | intro                   | course | option          |
      | choice     | Choice 1   | Test choice description | C1     | one, two, three |

  Scenario: Teacher can add and display any number of responses in a choice activity
    Given I am on the "Choice 1" "choice activity" page logged in as "teacher1"
    And I should see "one"
    And I should see "two"
    And I should see "three"
    And I should not see "four"
    And I should not see "five"
    And I should not see "six"
    When I am on the "Choice 1" "choice activity editing" page
    And I press "Add 3 field(s) to form"
    And I set the following fields to these values:
      | Option 4  | four  |
      | Option 5  | five  |
      | Option 6  | six   |
    And I press "Save and display"
    Then I should see "one"
    And I should see "two"
    And I should see "three"
    And I should see "four"
    And I should see "five"
    And I should see "six"
