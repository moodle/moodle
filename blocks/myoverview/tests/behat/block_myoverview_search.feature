@block @block_myoverview @javascript
Feature: My overview block searching

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
      | student2 | Student   | Y        | student2@example.com | S2       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 01    | C1       | 0        |
      | Course 02    | C2       | 0        |
      | Course 03    | C3       | 0        |
      | Course 04    | C4       | 0        |
      | Course 05    | C5       | 0        |
      | Course 06    | C6       | 0        |
      | Course 07    | C7       | 0        |
      | Course 08    | C8       | 0        |
      | Course 09    | C9       | 0        |
      | Course 10    | C10      | 0        |
      | Course 11    | C11      | 0        |
      | Course 12    | C12      | 0        |
      | Course 13    | C13      | 0        |
      | Fake example | Fake     | 0        |
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

  Scenario: There is no search if I am not enrolled in any course
    When I am on the "My courses" page logged in as "student2"
    Then I should see "You're not enrolled in any course" in the "Course overview" "block"
    And "Search courses" "field" should not exist in the "Course overview" "block"
    And I log out

  Scenario: Single page search
    Given I am on the "My courses" page logged in as "student1"
    And I set the field "Search courses" in the "Course overview" "block" to "Course 0"
    Then I should see "Course 01" in the "Course overview" "block"
    And I should not see "Course 13" in the "Course overview" "block"
    And I log out

  Scenario: Paginated search
    Given I am on the "My courses" page logged in as "student1"
    And I set the field "Search courses" in the "Course overview" "block" to "Course"
    And I should see "Course 01" in the "Course overview" "block"
    And I should not see "Course 13" in the "Course overview" "block"
    And I click on "[data-control='next']" "css_element" in the "Course overview" "block"
    And I wait until ".block_myoverview [data-control='next']" "css_element" exists
    Then I should see "Course 13" in the "Course overview" "block"
    And I should not see "Course 01" in the "Course overview" "block"
