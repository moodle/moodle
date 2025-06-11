@core @core_grades @gradereport_singleview @javascript
Feature: Given we have opted to search for a grade item, Lets find and search them.
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
      | student1 | Student   | 1        | student1@example.com | s1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                |
      | assign   | C1     | a1       | Test assignment one |
      | assign   | C1     | a2       | Test assignment two |
    And I am on the "Course 1" "grades > Single view > View" page logged in as "teacher1"
    And I change window size to "large"

  Scenario: A teacher can search for and find a grade item to view
    Given I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And I click on ".gradesearchwidget" "css_element"
    When I confirm "Test assignment one" exists in the "Search items" search combo box
    And I confirm "Test assignment two" exists in the "Search items" search combo box
    Then I set the field "Search items" to "two"
    And I wait until "Test assignment one" "option_role" does not exist
    And I confirm "Test assignment one" does not exist in the "Search items" search combo box
    And I confirm "Test assignment two" exists in the "Search items" search combo box
    And I click on "Test assignment two" in the "Search items" search combo box
    # The search input remains in the field on reload this is in keeping with other search implementations.
    And I click on ".gradesearchwidget" "css_element"
    And the field "Search items" matches value "two"
    Then I set the field "Search items" to "Turtle"
    And I should see "No results for \"Turtle\""

  @accessibility
  Scenario: A teacher can set focus and search using the input with a keyboard
    # Basic tests for the page.
    Given I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And I click on ".gradesearchwidget" "css_element"
    And the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    # Move onto general keyboard navigation testing.
    And I click on "Search items" "field"
    And I wait until "Test assignment one" "option_role" exists
    And I press the down key
    And the focused element is "Search items" "field"
    And ".active" "css_element" should exist in the "Test assignment one" "option_role"
    And I press the up key
    And the focused element is "Search items" "field"
    And ".active" "css_element" should exist in the "Course total" "option_role"
    And I press the down key
    And the focused element is "Search items" "field"
    And ".active" "css_element" should exist in the "Test assignment one" "option_role"
    Then I set the field "Search items" to "Goodmeme"
    And I wait until "Test assignment one" "option_role" does not exist
    And I press the down key
    And the focused element is "Search items" "field"

    # Lets check the tabbing order.
    And I set the field "Search items" to "one"
    And I wait until "Test assignment one" "option_role" exists
    And I press the tab key
    And the focused element is "Clear search input" "button" in the ".grade-search" "css_element"
    And I press the enter key
    And I wait until the page is ready
    And ".gradesearchwidget" "css_element" should exist
