@core @core_course @javascript
Feature: Recommending activities
  As an admin I want to recommend activities in the activity chooser

  Scenario: As an admin I can recommend activities from an admin setting page.
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    And I click on ".activity-recommend-checkbox" "css" in the "Assignment" "table_row"
    And I navigate to "Courses > Add a new course" in site administration
    When I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    Then "input[aria-label=\"Recommend activity: Assignment\"][checked=checked]" "css_element" should exist
    And "input[aria-label=\"Recommend activity: Book\"]:not([checked=checked])" "css_element" should exist

  Scenario: As an admin I can remove recommend activities from an admin setting page.
    Given I log in as "admin"
    And I am on site homepage
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    And I click on ".activity-recommend-checkbox" "css" in the "Assignment" "table_row"
    And I navigate to "Courses > Add a new course" in site administration
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    And "input[aria-label=\"Recommend activity: Assignment\"][checked=checked]" "css_element" should exist
    And "input[aria-label=\"Recommend activity: Book\"]:not([checked=checked])" "css_element" should exist
    And I click on ".activity-recommend-checkbox" "css" in the "Assignment" "table_row"
    And I navigate to "Courses > Add a new course" in site administration
    When I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    Then "input[aria-label=\"Recommend activity: Assignment\"]:not([checked=checked])" "css_element" should exist
    And "input[aria-label=\"Recommend activity: Book\"]:not([checked=checked])" "css_element" should exist
