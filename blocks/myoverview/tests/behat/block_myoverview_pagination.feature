@block @block_myoverview @javascript
Feature: My overview block pagination

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 01 | C1       | 0        |
      | Course 02 | C2       | 0        |
      | Course 03 | C3       | 0        |
      | Course 04 | C4       | 0        |
      | Course 05 | C5       | 0        |
      | Course 06 | C6       | 0        |
      | Course 07 | C7       | 0        |
      | Course 08 | C8       | 0        |
      | Course 09 | C9       | 0        |
      | Course 10 | C10      | 0        |
      | Course 11 | C11      | 0        |
      | Course 12 | C12      | 0        |
      | Course 13 | C13      | 0        |
      | Course 14 | C14      | 0        |
      | Course 15 | C15      | 0        |
      | Course 16 | C16      | 0        |
      | Course 17 | C17      | 0        |
      | Course 18 | C18      | 0        |
      | Course 19 | C19      | 0        |
      | Course 20 | C20      | 0        |
      | Course 21 | C21      | 0        |
      | Course 22 | C22      | 0        |
      | Course 23 | C23      | 0        |
      | Course 24 | C24      | 0        |
      | Course 25 | C25      | 0        |

  Scenario: The pagination controls should be hidden if I am not enrolled in any courses
    When I am on the "My courses" page logged in as "student1"
    Then I should see "No courses" in the "Course overview" "block"
    And I should not see "Show" in the "Course overview" "block"
    And ".block_myoverview .dropdown-menu.show" "css_element" should not be visible
    And ".block_myoverview [data-control='next']" "css_element" should not be visible
    And ".block_myoverview [data-control='previous']" "css_element" should not be visible
    And I log out

  Scenario: The pagination controls should be hidden if I am enrolled in 12 courses or less
    Given the following "course enrolments" exist:
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
    When I am on the "My courses" page logged in as "student1"
    Then I should not see "Show" in the "Course overview" "block"
    And ".block_myoverview .dropdown-menu.show" "css_element" should not be visible
    And ".block_myoverview [data-control='next']" "css_element" should not be visible
    And ".block_myoverview [data-control='previous']" "css_element" should not be visible
    And I log out

  Scenario: The default pagination should be 12 courses
    Given the following "course enrolments" exist:
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
    When I am on the "My courses" page logged in as "student1"
    Then I should see "12" in the ".block_myoverview [data-action='limit-toggle']" "css_element"
    And I log out

  Scenario: I should only see pagination limit options less than total number of enrolled courses
    Given the following "course enrolments" exist:
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
    And I am on the "My courses" page logged in as "student1"
    When I click on "[data-action='limit-toggle']" "css_element" in the "Course overview" "block"
    Then I should see "All" in the ".dropdown-menu.show" "css_element"
    And I should see "12" in the ".dropdown-menu.show" "css_element"
    And ".block_myoverview [data-control='next']" "css_element" should be visible
    And ".block_myoverview [data-control='previous']" "css_element" should be visible
    But I should not see "24" in the ".block_myoverview .dropdown-menu.show" "css_element"
    And I log out

  Scenario: Previous page button should be disabled when on the first page of courses
    Given the following "course enrolments" exist:
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
    When I am on the "My courses" page logged in as "student1"
    Then the "class" attribute of ".block_myoverview [data-control='previous']" "css_element" should contain "disabled"
    And I log out

  Scenario: Next page button should be disabled when on the last page of courses
    Given the following "course enrolments" exist:
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
    When I am on the "My courses" page logged in as "student1"
    And I click on "[data-control='next']" "css_element" in the "Course overview" "block"
    Then the "class" attribute of ".block_myoverview [data-control='next']" "css_element" should contain "disabled"
    And I log out

  Scenario: Next and previous page buttons should both be enabled when not on last or first page of courses
    Given the following "course enrolments" exist:
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
      | student1 | C14 | student |
      | student1 | C15 | student |
      | student1 | C16 | student |
      | student1 | C17 | student |
      | student1 | C18 | student |
      | student1 | C19 | student |
      | student1 | C20 | student |
      | student1 | C21 | student |
      | student1 | C22 | student |
      | student1 | C23 | student |
      | student1 | C24 | student |
      | student1 | C25 | student |
    When I am on the "My courses" page logged in as "student1"
    And I wait until ".block_myoverview [data-control='next']" "css_element" exists
    And I click on "[data-control='next']" "css_element" in the "Course overview" "block"
    Then the "class" attribute of ".block_myoverview [data-control='next']" "css_element" should not contain "disabled"
    And the "class" attribute of ".block_myoverview [data-control='previous']" "css_element" should not contain "disabled"
    And I should see "Course 13" in the "Course overview" "block"
    And I should see "Course 24" in the "Course overview" "block"
    But I should not see "Course 12" in the "Course overview" "block"
    And I should not see "Course 25" in the "Course overview" "block"
    And I log out
