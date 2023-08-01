@core @core_grades @javascript
Feature: We carry over data from modal to advanced grade item settings
  In order to setup grade items quickly
  As an teacher
  I need to ensure data is carried over from modal to advanced grade item settings

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "grade categories" exist:
      | fullname                 | course |
      | Some cool grade category | C1     |
    Given I log in as "teacher1"
    And I am on the "Course 1" course page logged in as teacher1
    And I navigate to "Setup > Gradebook setup" in the course gradebook
    And I press "Add grade item"

  Scenario: Defaults are used when creating a new grade item
    Given I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the following fields match these values:
      | Item name         |          |
      | Minimum grade     | 0        |
      | Maximum grade     | 100      |
      | Weight adjusted   | 0        |
      | aggregationcoef2  | 0        |
      | Grade category    | Course 1 |

  Scenario: We carry over data from modal to advanced grade item settings
    Given I set the following fields to these values:
      | Item name         | Manual item 1            |
      | Minimum grade     | 1                        |
      | Maximum grade     | 99                       |
      | Weight adjusted   | 1                        |
      | aggregationcoef2  | 100                      |
      | Grade category    | Some cool grade category |
    When I click on "Show more..." "link" in the ".modal-dialog" "css_element"
    Then the following fields match these values:
      | Item name         | Manual item 1            |
      | Minimum grade     | 1                        |
      | Maximum grade     | 99                       |
      | Weight adjusted   | 1                        |
      | aggregationcoef2  | 100                      |
      | Grade category    | Some cool grade category |
