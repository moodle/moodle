@theme_boost
Feature: Edit mode switch
  In order to safely toggle editing on a page
  As a teacher
  I need the Edit mode switch to keep working after it moved onto the design system Switch component

  Background:
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname |
      | teacher1 | Teacher   | One      |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Turning editing mode on and off with JavaScript enabled
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And ".mds-switch" "css_element" should exist
    And "Edit mode" "field" should exist
    When I turn editing mode on
    Then the field "Edit mode" matches value "1"
    And I turn editing mode off
    And the field "Edit mode" matches value "0"

  Scenario: Turning editing mode on and off without JavaScript falls back to the noscript submit
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And ".mds-switch" "css_element" should exist
    And "Edit mode" "field" should exist
    When I turn editing mode on
    Then the field "Edit mode" matches value "1"
    And I turn editing mode off
    And the field "Edit mode" matches value "0"
