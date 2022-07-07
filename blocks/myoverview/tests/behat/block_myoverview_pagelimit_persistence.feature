@block @block_myoverview @javascript
Feature: The my overview block allows users to persistence of their page limits

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C01        | 0        |
      | Course 2 | C02        | 0        |
      | Course 3 | C03        | 0        |
      | Course 4 | C04        | 0        |
      | Course 5 | C05        | 0        |
      | Course 6 | C06        | 0        |
      | Course 7 | C07        | 0        |
      | Course 8 | C08        | 0        |
      | Course 9 | C09        | 0        |
      | Course 10 | C10        | 0        |
      | Course 11 | C11        | 0        |
      | Course 12 | C12        | 0        |
      | Course 13 | C13        | 0        |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C01 | student |
      | student1 | C02 | student |
      | student1 | C03 | student |
      | student1 | C04 | student |
      | student1 | C05 | student |
      | student1 | C06 | student |
      | student1 | C07 | student |
      | student1 | C08 | student |
      | student1 | C09 | student |
      | student1 | C10 | student |
      | student1 | C11 | student |
      | student1 | C12 | student |
      | student1 | C13 | student |

  Scenario: Toggle the page limit between page reloads
    Given I log in as "student1"
    When I click on "[data-action='limit-toggle']" "css_element" in the "Course overview" "block"
    And I click on "All" "link" in the ".dropdown-menu.show" "css_element"
    Then I should see "Course 13"
    And I reload the page
    Then I should see "Course 13"
    And I should see "All" in the "[data-action='limit-toggle']" "css_element"

  Scenario: Toggle the page limit between grouping changes
    Given I log in as "student1"
    When I click on "[data-action='limit-toggle']" "css_element" in the "Course overview" "block"
    And I click on "All" "link" in the ".dropdown-menu.show" "css_element"
    And I click on "All (except removed from view)" "button" in the "Course overview" "block"
    And I click on "In progress" "link" in the "Course overview" "block"
    Then I should see "Course 13"
    And I should see "All" in the "[data-action='limit-toggle']" "css_element"
