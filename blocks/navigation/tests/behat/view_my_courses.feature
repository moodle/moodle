@block @block_navigation
Feature: View my courses in navigation block
  In order to navigate to my courses
  As a student
  I need my courses displayed in the navigation block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | cat1  | 0        | cat1     |
      | cat2  | 0        | cat2     |
      | cat3  | 0        | cat3     |
      | cat31 | cat3     | cat31    |
      | cat32 | cat3     | cat32    |
      | cat33 | cat3     | cat33    |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course1  | c1        | cat1     |
      | Course2  | c2        | cat2     |
      | Course31 | c31       | cat31    |
      | Course32 | c32       | cat32    |
      | Course331| c331      | cat33    |
      | Course332| c332      | cat33    |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | c1     | student |
      | student1 | c31    | student |
      | student1 | c331   | student |

  @javascript
  Scenario: The plain list of enrolled courses is shown
    Given the following config values are set as admin:
      | navshowmycoursecategories | 0 |
    And I log in as "student1"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should not see "cat1" in the "Navigation" "block"
    And I should not see "cat2" in the "Navigation" "block"
    And I should see "c1" in the "Navigation" "block"
    And I should see "c31" in the "Navigation" "block"
    And I should see "c331" in the "Navigation" "block"
    And I should not see "c2" in the "Navigation" "block"
    And I should not see "c32" in the "Navigation" "block"
    And I should not see "c332" in the "Navigation" "block"

  @javascript
  Scenario: The nested list of enrolled courses is shown
    Given the following config values are set as admin:
      | navshowmycoursecategories | 1 |
    And I log in as "student1"
    When I click on "Dashboard" "link" in the "Navigation" "block"
    Then I should see "cat1" in the "Navigation" "block"
    And I should see "cat3" in the "Navigation" "block"
    And I should not see "cat2" in the "Navigation" "block"
    And I expand "cat3" node
    And I should see "cat31" in the "Navigation" "block"
    And I should see "cat33" in the "Navigation" "block"
    And I should not see "cat32" in the "Navigation" "block"
    And I expand "cat31" node
    And I should see "c31" in the "Navigation" "block"
    And I expand "cat33" node
    And I should see "c331" in the "Navigation" "block"
    And I should not see "c332" in the "Navigation" "block"

  @javascript
  Scenario: I can expand categories and courses as guest
    Given the following config values are set as admin:
      | navshowmycoursecategories | 1 |
      | navshowallcourses         | 1 |
    And I expand "Courses" node
    And I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should see "cat3" in the "Navigation" "block"
    And I should not see "cat31" in the "Navigation" "block"
    And I should not see "cat32" in the "Navigation" "block"
    And I should not see "cat331" in the "Navigation" "block"
    And I should not see "c1" in the "Navigation" "block"
    And I should not see "c2" in the "Navigation" "block"
    And I should not see "c31" in the "Navigation" "block"
    And I should not see "c32" in the "Navigation" "block"
    When I expand "cat3" node
    And I expand "cat31" node
    And I expand "cat1" node
    Then I should see "cat1" in the "Navigation" "block"
    And I should see "cat2" in the "Navigation" "block"
    And I should see "cat3" in the "Navigation" "block"
    And I should see "cat31" in the "Navigation" "block"
    And I should see "cat32" in the "Navigation" "block"
    And I should not see "cat331" in the "Navigation" "block"
    And I should see "c1" in the "Navigation" "block"
    And I should not see "c2" in the "Navigation" "block"
    And I should see "c31" in the "Navigation" "block"
    And I should not see "c32" in the "Navigation" "block"
