@block @block_navigation
Feature: Test expand my courses navigation setting
  As a student
  I visit my My Moodle page and observe the the My Courses branch

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | cat1  | 0        | cat1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course1  | c1        | cat1     |
      | Course2  | c2        | cat1     |
      | Course3  | c3        | cat1     |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | c1     | student |
      | student1 | c2    | student |

  Scenario: The My Courses branch is expanded on the My Moodle page by default
    When I log in as "student1"
    And I click on "My home" "link" in the "Navigation" "block"
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should not see "c3" in the "Navigation" "block"

  @javascript
  Scenario: The My Courses branch is collapsed when expand my courses is off
    Given I log in as "admin"
    And I set the following administration settings values:
      | Show My courses expanded on My home | 0 |
    And I log out
    When I log in as "student1"
    And I click on "My home" "link" in the "Navigation" "block"
    Then I should not see "c1" in the "Navigation" "block"
    And I should not see "c2" in the "Navigation" "block"
    And I should not see "c3" in the "Navigation" "block"

  @javascript
  Scenario: My Courses can be expanded on the My Moodle page when expand my courses is off
    Given I log in as "admin"
    And I set the following administration settings values:
      | Show My courses expanded on My home | 0 |
    And I log out
    When I log in as "student1"
    And I click on "My home" "link" in the "Navigation" "block"
    And I should not see "c1" in the "Navigation" "block"
    And I should not see "c2" in the "Navigation" "block"
    And I should not see "c3" in the "Navigation" "block"
    And I expand "My courses" node
    Then I should see "c1" in the "Navigation" "block"
    And I should see "c2" in the "Navigation" "block"
    And I should not see "c3" in the "Navigation" "block"
