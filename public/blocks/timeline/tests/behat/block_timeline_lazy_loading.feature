@block @block_timeline @javascript
Feature: The timeline block allows users to use the lazy loading to view more activities

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | 1        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category | startdate     | enddate                    |
      | Course 1 | C1        | 0        | ##yesterday## | ##last day of next month## |
      | Course 2 | C2        | 0        | ##yesterday## | ##last day of next month## |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
    And the following "activities" exist:
      | activity | course | idnumber | name           | intro                   | timeopen      | timeclose    |
      | choice   | C1     | choice1  | Test choice 1  | Test choice description | ##yesterday## | ##tomorrow## |
      | choice   | C1     | choice2  | Test choice 2  | Test choice description | ##yesterday## | ##+1 days##  |
      | choice   | C1     | choice3  | Test choice 3  | Test choice description | ##yesterday## | ##+2 days##  |
      | choice   | C1     | choice4  | Test choice 4  | Test choice description | ##yesterday## | ##+3 days##  |
      | choice   | C1     | choice5  | Test choice 5  | Test choice description | ##yesterday## | ##+4 days##  |
      | choice   | C1     | choice6  | Test choice 6  | Test choice description | ##yesterday## | ##+5 days##  |
      | choice   | C1     | choice7  | Test choice 7  | Test choice description | ##yesterday## | ##+6 days##  |
      | choice   | C1     | choice8  | Test choice 8  | Test choice description | ##yesterday## | ##+7 days##  |
      | choice   | C1     | choice9  | Test choice 9  | Test choice description | ##yesterday## | ##+8 days##  |
      | choice   | C1     | choice10 | Test choice 10 | Test choice description | ##yesterday## | ##+9 days##  |
      | choice   | C2     | choice10 | Test choice 11 | Test choice description | ##yesterday## | ##+9 days##  |
    And the following "activities" exist:
      | activity | course | idnumber | name          | intro                   | timeopen        | duedate       |
      | assign   | C1     | assign1  | Test assign 1 | Test assign description | ##1 month ago## | ##yesterday## |

  Scenario: Lazy loading for date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "All" "link" in the "Timeline" "block"
    Then I should see "Test choice 1" in the "Timeline" "block"
    And "Test assign 1" "link" should exist in the "Timeline" "block"
    And "Test choice 1" "link" should exist in the "Timeline" "block"
    And "Test choice 2" "link" should exist in the "Timeline" "block"
    And "Test choice 3" "link" should exist in the "Timeline" "block"
    And "Test choice 4" "link" should exist in the "Timeline" "block"
    And "Test choice 5" "link" should not exist in the "Timeline" "block"
    And "Test choice 6" "link" should not exist in the "Timeline" "block"
    And "Test choice 7" "link" should not exist in the "Timeline" "block"
    And "Test choice 8" "link" should not exist in the "Timeline" "block"
    And "Test choice 9" "link" should not exist in the "Timeline" "block"
    And "Test choice 10" "link" should not exist in the "Timeline" "block"
    And "Test choice 11" "link" should not exist in the "Timeline" "block"
    And I click on "Show more activities" "button" in the "[data-region='view-dates']" "css_element"
    And "Test choice 5" "link" should exist in the "Timeline" "block"
    And "Test choice 6" "link" should exist in the "Timeline" "block"
    And "Test choice 7" "link" should exist in the "Timeline" "block"
    And "Test choice 8" "link" should exist in the "Timeline" "block"
    And "Test choice 9" "link" should exist in the "Timeline" "block"
    And "Test choice 10" "link" should exist in the "Timeline" "block"
    And "Test choice 11" "link" should exist in the "Timeline" "block"

  Scenario: Lazy loading for course view
    Given I log in as "student1"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    When I click on "Sort by courses" "link" in the "Timeline" "block"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    And "Test choice 1" "link" should exist in the "Timeline" "block"
    And "Test choice 2" "link" should exist in the "Timeline" "block"
    And "Test choice 3" "link" should exist in the "Timeline" "block"
    And "Test choice 4" "link" should exist in the "Timeline" "block"
    And "Test choice 5" "link" should exist in the "Timeline" "block"
    And "Test choice 11" "link" should exist in the "Timeline" "block"
    And "Test choice 6" "link" should not exist in the "Timeline" "block"
    And "Test choice 7" "link" should not exist in the "Timeline" "block"
    And "Test choice 8" "link" should not exist in the "Timeline" "block"
    And "Test choice 9" "link" should not exist in the "Timeline" "block"
    And "Test choice 10" "link" should not exist in the "Timeline" "block"
    And I click on "Show more activities" "button" in the "[data-region='view-courses']" "css_element"
    And "Test choice 6" "link" should exist in the "Timeline" "block"
    And "Test choice 7" "link" should exist in the "Timeline" "block"
    And "Test choice 8" "link" should exist in the "Timeline" "block"
    And "Test choice 9" "link" should exist in the "Timeline" "block"
    And "Test choice 10" "link" should exist in the "Timeline" "block"

  Scenario: The lazy loading button will not be shown if the number of events is smaller than 5
    Given I log in as "student1"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by dates" "link" in the "Timeline" "block"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "Overdue" "link" in the "Timeline" "block"
    Then "Test assign 1" "link" should exist in the "Timeline" "block"
    And "Show more activities" "button" should not exist in the "[data-region='view-courses']" "css_element"
