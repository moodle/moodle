@block_starredcourses
Feature: Starred courses
  In order for me to quickly navigate to my favourite courses
  As a user
  I must be able to add them to the Starred courses block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
      | C2        | Course 2 |
      | C3        | Course 3 |
    And the following "course enrolments" exist:
      | user      | course | role    |
      | student1  | C1     | student |
      | student1  | C2     | student |
      | student1  | C3     | student |
    And the following "blocks" exist:
      | blockname       | contextlevel | reference | pagetypepattern | defaultregion  |
      | starredcourses  | User         | student1  | my-index        | content        |

  @accessibility @javascript
  Scenario: User has no starred courses
    Given I log in as "student1"
    Then I should see "No starred courses"
    And the page should meet accessibility standards

  @accessibility @javascript
  Scenario: User has starred courses
    Given I am on the "My courses" page logged in as "student1"
    And I click on "Actions for course Course 1" "button"
    And I click on "Star for Course 1" "link"
    And I click on "Actions for course Course 3" "button"
    And I click on "Star for Course 3" "link"
    When I follow "Dashboard"
    Then the page should meet accessibility standards
    And I should see "Course 1" in the "Starred courses" "block"
    And I should see "Course 3" in the "Starred courses" "block"
    But I should not see "Course 2" in the "Starred courses" "block"
