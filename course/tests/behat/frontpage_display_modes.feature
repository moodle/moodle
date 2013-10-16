@core @core_course
Feature: Front page displays items in different modes
  In order to show a clean and clear list of the site categories and course
  As an admin
  I need to set different frontpage display modes

  Background:
    Given the following "categories" exists:
      | name                   | category | idnumber |
      | Category 1             | 0        | CAT1     |
      | Category 2             | 0        | CAT2     |
      | Category 1 child       | CAT1     | CAT11    |
      | Category 2 child       | CAT2     | CAT21    |
      | Category 1 child child | CAT11    | CAT111   |
      | Category 3             | 0        | CAT3     |
    And the following "courses" exists:
      | fullname     | shortname   | category |
      | Course 1 1   | COURSE1_1   | CAT1     |
      | Course 2 1   | COURSE2_1   | CAT2     |
      | Course 11 1  | COURSE11_1  | CAT11    |
      | Course 2 2   | COURSE2_2   | CAT2     |
      | Course 21 1  | COURSE21_1  | CAT21    |
      | Course 111 1 | COURSE111_1 | CAT111   |
      | Course 111 2 | COURSE111_2 | CAT111   |
    And I log in as "admin"

  @javascript
  Scenario: Displays a list of categories
    When I set the following administration settings values:
      | Front page items when logged in | List of categories |
      | Maximum category depth | 2 |
    And I am on homepage
    Then I should see "Category 1" in the "region-main" "region"
    And I should see "Category 1 child" in the "region-main" "region"
    And I should not see "Category 1 child child" in the "region-main" "region"
    And I toggle "Category 1" category children visibility in frontpage
    And I should not see "Category 1 child" in the "region-main" "region"
    And I toggle "Category 1" category children visibility in frontpage
    And I should see "Category 1 child" in the "region-main" "region"
    And I follow "Category 1 child"
    And I should see "Category 1 child child" in the "region-main" "region"
    And I should see "Course 11 1" in the "region-main" "region"

  @javascript
  Scenario: Displays a combo list
    When I set the following administration settings values:
      | Front page items when logged in | Combo list |
      | Maximum category depth | 2 |
    And I am on homepage
    Then I should see "Category 1" in the "region-main" "region"
    And I should see "Category 1 child" in the "region-main" "region"
    And I should not see "Category 1 child child" in the "region-main" "region"
    And I should see "Course 1 1" in the "region-main" "region"
    And I should see "Course 2 2" in the "region-main" "region"
    And I should not see "Course 11 1" in the "region-main" "region"
    And I follow "Category 1 child"
    And I should see "Course 11 1" in the "region-main" "region"
    And I should see "Category 1 child child" in the "region-main" "region"
    And I am on homepage
    And I toggle "Category 1" category children visibility in frontpage
    And I should not see "Course 1 1" in the "region-main" "region"
    And I should not see "Category 1 child" in the "region-main" "region"
    And I toggle "Category 1" category children visibility in frontpage
    And I should see "Course 1 1" in the "region-main" "region"
