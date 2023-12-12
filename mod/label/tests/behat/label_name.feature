@mod @mod_label

Feature: Set label name
  As a teacher
  I should be able to create a label activity and set a name

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Test | C1 | 0 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | First | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher | C1 | editingteacher |
    And the following "activities" exist:
      | activity | course | section | intro        | idnumber |
      | label    | C1     | 1       | Intro Text   | C1LABEL1 |

  Scenario: label name input box should be shown and can be set
    When I log in as "teacher"
    And I am on "Test" course homepage
    And "Intro Text" activity should be visible
    And I am on the "Intro Text" "label activity editing" page logged in as teacher
    And I should see "Title in course index" in the "General" "fieldset"
    And I set the field "Title in course index" to "Test Label 1"
    And I press "Save and return to course"
    And I am on "Test" course homepage
    Then "Test Label 1" activity should be visible
