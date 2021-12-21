@core @core_course
Feature: Site home displays items in different modes
  In order to show a clean and clear list of the site categories and course
  As an admin
  I need to set different frontpage display modes

  Background:
    Given the following "categories" exist:
      | name                   | category | idnumber |
      | Category A             | 0        | CATA     |
      | Category B             | 0        | CATB     |
      | Category A child       | CATA     | CATA1    |
      | Category B child       | CATB     | CATB1    |
      | Category A child child | CATA1    | CATA11   |
      | Category C             | 0        | CATC     |
    And the following "courses" exist:
      | fullname     | shortname   | category |
      | Course 1 1   | COURSE1_1   | CATA     |
      | Course 2 1   | COURSE2_1   | CATB     |
      | Course 11 1  | COURSE11_1  | CATA1    |
      | Course 2 2   | COURSE2_2   | CATB     |
      | Course 21 1  | COURSE21_1  | CATB1    |
      | Course 111 1 | COURSE111_1 | CATA11   |
      | Course 111 2 | COURSE111_2 | CATA11   |
    And I log in as "admin"

  @javascript
  Scenario: Displays a list of categories
    When I set the following administration settings values:
      | Site home items when logged in | List of categories |
      | Maximum category depth | 2 |
    And I am on site homepage
    Then I should see "Category A" in the "region-main" "region"
    And I should see "Category A child" in the "region-main" "region"
    And I should not see "Category A child child" in the "region-main" "region"
    And I toggle "Category A" category children visibility in frontpage
    And I should not see "Category A child" in the "region-main" "region"
    And I toggle "Category A" category children visibility in frontpage
    And I should see "Category A child" in the "region-main" "region"
    And I toggle "Category A child" category children visibility in frontpage
    And I should see "Category A child child" in the "region-main" "region"

  @javascript
  Scenario: Displays a combo list
    When I set the following administration settings values:
      | Site home items when logged in | Combo list |
      | Maximum category depth | 2 |
    And I am on site homepage
    Then I should see "Category A" in the "region-main" "region"
    And I should see "Category A child" in the "region-main" "region"
    And I should not see "Category A child child" in the "region-main" "region"
    And I should see "Course 1 1" in the "region-main" "region"
    And I should see "Course 2 2" in the "region-main" "region"
    And I should not see "Course 11 1" in the "region-main" "region"
    And I toggle "Category A child" category children visibility in frontpage
    And I should see "Course 11 1" in the "region-main" "region"
    And I should see "Category A child child" in the "region-main" "region"
    And I toggle "Category A" category children visibility in frontpage
    And I should not see "Course 1 1" in the "region-main" "region"
    And I should not see "Category A child" in the "region-main" "region"
    And I toggle "Category A" category children visibility in frontpage
    And I should see "Course 11 1" in the "region-main" "region"
