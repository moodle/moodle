@block @block_myoverview @javascript
Feature: The my overview block allows users to persistence of their page limits

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
      | Course 3 | C3        | 0        |
      | Course 4 | C4        | 0        |
      | Course 5 | C5        | 0        |
      | Course 6 | C6        | 0        |
      | Course 7 | C7        | 0        |
      | Course 8 | C8        | 0        |
      | Course 9 | C9        | 0        |
      | Course 10 | C10        | 0        |
      | Course 11 | C11        | 0        |
      | Course 12 | C12        | 0        |
      | Course 13 | C13        | 0        |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
      | student1 | C4 | student |
      | student1 | C5 | student |
      | student1 | C6 | student |
      | student1 | C7 | student |
      | student1 | C8 | student |
      | student1 | C9 | student |
      | student1 | C10 | student |
      | student1 | C11 | student |
      | student1 | C12 | student |
      | student1 | C13 | student |

  Scenario: Toggle the page limit between page reloads
    Given I log in as "student1"
    When I click on "Show 12 items per page" "button" in the "Course overview" "block"
    And I click on "24" "link"
    Then I should see "Course 9"
    And I reload the page
    Then I should see "Course 9"
    And I should see "24" in the "[data-action='limit-toggle']" "css_element"
    And I log out

  Scenario: Toggle the page limit between grouping changes
    Given I log in as "student1"
    When I click on "Show 12 items per page" "button" in the "Course overview" "block"
    And I click on "24" "link"
    And I click on "All" "button" in the "Course overview" "block"
    And I click on "In progress" "link" in the "Course overview" "block"
    Then I should see "Course 9"
    And I should see "24" in the "[data-action='limit-toggle']" "css_element"
    And I log out
