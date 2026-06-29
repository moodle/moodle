@javascript @theme_boost
Feature: Navbar user menu
  To keep the header layout consistent
  As a logged-in user
  I need the user menu to remain at the right edge of the navbar

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | Student   | One      | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  Scenario: User first name is displayed alongside the avatar on wider viewports
    Given I log in as "student1"
    And I am on site homepage
    Then the "class" attribute of ".userfirstname" "css_element" should contain "d-xl-block"
    And I should see "Student" in the ".userfirstname" "css_element"
    And I change window size to "large"
    And ".userfirstname" "css_element" should be visible
    And I change window size to "mobile"
    And ".userfirstname" "css_element" should not be visible

  Scenario Outline: User menu is in the right edge of the navbar
    Given I log in as "<username>"
    When I am on <location>
    Then "#usernavigation .usermenu-container" "css_element" should appear after "#usernavigation <preselector>" "css_element"

    Examples:
      | username | location                    | preselector                |
      | teacher1 | site homepage               | .popover-region-container  |
      | student1 | site homepage               | .popover-region-container  |
      | teacher1 | "Course 1" course homepage  | .editmode-switch-form      |
      | student1 | "Course 1" course homepage  | .popover-region-container  |
