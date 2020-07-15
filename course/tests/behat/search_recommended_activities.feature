@core @core_course
Feature: Search recommended activities
  As an admin I am able to search for activities in the "Recommended activities" admin setting page

  Scenario: Search results are returned if the search query matches any activity names
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    When I set the field "search" to "assign"
    And I click on "Submit search" "button"
    Then I should see "Search results"
    And "Assignment" "table_row" should exist
    And "Book" "table_row" should not exist

  Scenario: Search results are not returned if the search query does not match with any activity names
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    When I set the field "search" to "random query"
    And I click on "Submit search" "button"
    Then I should see "Search results: 0"
