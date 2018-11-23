@block @block_timeline @javascript
Feature: The timeline block allows user persistence of their page limits

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | 1        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category | startdate                   | enddate         |
      | Course 1 | C1        | 0        | ##1 month ago##             | ##15 days ago## |
      | Course 2 | C2        | 0        | ##yesterday##               | ##tomorrow## |
      | Course 3 | C3        | 0        | ##first day of next month## | ##last day of next month## |
      | Course 4 | C4        | 0        | ##first day of next month## | ##last day of next month## |
      | Course 5 | C5        | 0        | ##first day of next month## | ##last day of next month## |
      | Course 6 | C6        | 0        | ##first day of next month## | ##last day of next month## |
    And the following "activities" exist:
      | activity | course | idnumber  | name            | intro                   | timeopen      | timeclose     |
      | choice   | C2     | choice1   | Test choice 1   | Test choice description | ##yesterday## | ##tomorrow##  |
      | choice   | C1     | choice2   | Test choice 2   | Test choice description | ##1 month ago## | ##15 days ago##  |
      | choice   | C3     | choice3   | Test choice 3   | Test choice description | ##first day of +5 months## | ##last day of +5 months##  |
      | feedback | C2     | feedback1 | Test feedback 1 | Test feedback description | ##yesterday## | ##tomorrow##  |
      | feedback | C1     | feedback2 | Test feedback 2 | Test feedback description | ##first day of +10 months## | ##last day of +10 months##  |
      | feedback | C3     | feedback3 | Test feedback 3 | Test feedback description | ##first day of +5 months## | ##last day of +5 months## |
    And the following "activities" exist:
      | activity | course | idnumber  | name            | intro                   | timeopen      | duedate     |
      | assign   | C1     | assign1   | Test assign 1   | Test assign description | ##1 month ago## | ##yesterday##  |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
      | student1 | C4 | student |
      | student1 | C5 | student |
      | student1 | C6 | student |

  Scenario: Toggle the page limit 5 - 25
    Given I log in as "student1"
    And I click on "Next 30 days" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    And I click on "Sort" "button" in the "Timeline" "block"
    And I click on "Sort by dates" "link" in the "Timeline" "block"
    When I click on "5" "button" in the "Timeline" "block"
    And I click on "25" "link"
    Then I should see "Test feedback 2" in the "Timeline" "block"
    And I reload the page
    Then I should see "Test feedback 2" in the "Timeline" "block"
    And I log out

  Scenario: Toggle the page limit 25 - 5
    Given I log in as "student1"
    And I click on "Next 30 days" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    And I click on "Sort" "button" in the "Timeline" "block"
    And I click on "Sort by dates" "link" in the "Timeline" "block"
    When I click on "5" "button" in the "Timeline" "block"
    And I click on "25" "link"
    And I should see "Test feedback 2" in the "Timeline" "block"
    And I click on "25" "button" in the "Timeline" "block"
    And I click on "5" "link" in the "[data-region='timeline'] [data-region='paging-control-limit-container'] .dropdown-menu" "css_element"
    Then I should not see "Test feedback 2" in the "Timeline" "block"
    And I reload the page
    And I should not see "Test feedback 2" in the "Timeline" "block"
    And I log out
