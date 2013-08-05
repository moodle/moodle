@block @block_navigation
Feature: View my courses in navigation block
  In order to navigate to my courses
  As a student
  I need my courses displayed in the navigation block

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@asd.com |
    And the following "categories" exists:
      | name  | category | idnumber |
      | cat1  | 0        | cat1     |
      | cat2  | 0        | cat2     |
      | cat3  | 0        | cat3     |
      | cat31 | cat3     | cat31    |
      | cat32 | cat3     | cat32    |
      | cat33 | cat3     | cat33    |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course1  | c1        | cat1     |
      | Course2  | c2        | cat2     |
      | Course31 | c31       | cat31    |
      | Course32 | c32       | cat32    |
      | Course331| c331      | cat33    |
      | Course332| c332      | cat33    |
    And the following "course enrolments" exists:
      | user     | course | role    |
      | student1 | c1     | student |
      | student1 | c31    | student |
      | student1 | c331   | student |
    And I log in as "admin"

  @javascript
  Scenario: The plain list of enrolled courses is shown
    Given I set the following administration settings values:
      | Show my course categories | 0 |
    And I log out
    And I log in as "student1"
    When I follow "My home"
    Then I should not see "cat1" in the "div.block_navigation .type_system" "css_element"
    And I should not see "cat2" in the "div.block_navigation .type_system" "css_element"
    And I should see "c1" in the "div.block_navigation .type_system" "css_element"
    And I should see "c31" in the "div.block_navigation .type_system" "css_element"
    And I should see "c331" in the "div.block_navigation .type_system" "css_element"
    And I should not see "c2" in the "div.block_navigation .type_system" "css_element"
    And I should not see "c32" in the "div.block_navigation .type_system" "css_element"
    And I should not see "c332" in the "div.block_navigation .type_system" "css_element"

  @javascript
  Scenario: The nested list of enrolled courses is shown
    Given I set the following administration settings values:
      | Show my course categories | 1 |
    And I log out
    And I log in as "student1"
    When I follow "My home"
    Then I should see "cat1" in the "div.block_navigation .type_system" "css_element"
    And I should see "cat3" in the "div.block_navigation .type_system" "css_element"
    And I should not see "cat2" in the "div.block_navigation .type_system" "css_element"
    And I expand "cat3" node
    And I wait "2" seconds
    And I should see "cat31" in the "div.block_navigation .type_system" "css_element"
    And I should see "cat33" in the "div.block_navigation .type_system" "css_element"
    And I should not see "cat32" in the "div.block_navigation .type_system" "css_element"
    And I expand "cat31" node
    And I wait "2" seconds
    And I should see "c31" in the "div.block_navigation .type_system" "css_element"
    And I expand "cat33" node
    And I wait "2" seconds
    And I should see "c331" in the "div.block_navigation .type_system" "css_element"
    And I should not see "c332" in the "div.block_navigation .type_system" "css_element"
