@block @block_timeline @javascript
Feature: The timeline block allows users to see upcoming activities
  In order to enable the timeline block
  As a student
  I can add the timeline block to my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | 1        | student1@example.com | S1       |
      | student2 | Student   | 2        | student2@example.com | S2       |
    And the following "courses" exist:
      | fullname | shortname | category | startdate                   | enddate         |
      | Course 1 | C1        | 0        | ##1 month ago##             | ##15 days ago## |
      | Course 2 | C2        | 0        | ##yesterday##               | ##tomorrow## |
      | Course 3 | C3        | 0        | ##first day of next month## | ##last day of next month## |
    And the following "activities" exist:
      | activity | course | idnumber  | name            | intro                   | timeopen      | timeclose     |
      | choice   | C2     | choice1   | Test choice 1   | Test choice description | ##yesterday## | ##tomorrow##  |
      | choice   | C1     | choice2   | Test choice 2   | Test choice description | ##1 month ago## | ##15 days ago##  |
      | choice   | C3     | choice3   | Test choice 3   | Test choice description | ##first day of +5 months## | ##last day of +5 months##  |
      | feedback | C2     | feedback1 | Test feedback 1 | Test feedback description | ##yesterday## | ##tomorrow##  |
      | feedback | C1     | feedback2 | Test feedback 2 | Test feedback description | ##first day of +10 months## | ##last day of +10 months##  |
      | feedback | C3     | feedback3 | Test feedback 3 | Test feedback description | ##first day of +5 months## | ##last day of +5 months## |
    And the following "activities" exist:
      | activity | course | idnumber  | name            | intro                   | timeopen        | duedate     |
      | assign   | C1     | assign1   | Test assign 1   | Test assign description | ##1 month ago## | ##yesterday##  |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |

  Scenario: Next 7 days in date view
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    When I click on "Next 7 days" "link" in the "Timeline" "block"
    Then I should see "Test choice 1 closes" in the "Timeline" "block"
    And I should see "Test feedback 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"
    And I should not see "Test choice 3 closes" in the "Timeline" "block"
    And I should not see "Test feedback 3 closes" in the "Timeline" "block"
    And I should not see "Test assign 1 is due" in the "Timeline" "block"

  Scenario: Overdue in date view
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    When I click on "Overdue" "link" in the "Timeline" "block"
    Then I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"
    And I should not see "Test feedback 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 3 closes" in the "Timeline" "block"
    And I should not see "Test feedback 3 closes" in the "Timeline" "block"

  Scenario: All in date view
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    When I click on "All" "link" in the "Timeline" "block"
    Then I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should see "Test feedback 1 closes" in the "Timeline" "block"
    And I should see "Test choice 1 closes" in the "Timeline" "block"
    And I should see "Test choice 3 closes" in the "Timeline" "block"
    And I should see "Test feedback 3 closes" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"
    And I should not see "Test feedback 2 closes" in the "Timeline" "block"
    And I click on "[data-region='paging-bar'] [data-control='next'] [data-region='page-link']" "css_element" in the "Timeline" "block"
    And I should see "Test feedback 2 closes" in the "Timeline" "block"
    And I should not see "Test assign 1 is due" in the "Timeline" "block"
    And I should not see "Test feedback 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 3 closes" in the "Timeline" "block"
    And I should not see "Test feedback 3 closes" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"

  Scenario: All in date view no next
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    And I click on "5" "button" in the "Timeline" "block"
    When I click on "25" "link" in the "Timeline" "block"
    Then I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should see "Test feedback 1 closes" in the "Timeline" "block"
    And I should see "Test choice 1 closes" in the "Timeline" "block"
    And I should see "Test choice 3 closes" in the "Timeline" "block"
    And I should see "Test feedback 3 closes" in the "Timeline" "block"
    And I should see "Test feedback 2 closes" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"

  Scenario: Persistent All in date view
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    When I click on "All" "link" in the "Timeline" "block"
    And I reload the page
    Then I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should see "Test feedback 1 closes" in the "Timeline" "block"
    And I should see "Test choice 1 closes" in the "Timeline" "block"
    And I should see "Test choice 3 closes" in the "Timeline" "block"
    And I should see "Test feedback 3 closes" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"
    And I should not see "Test feedback 2 closes" in the "Timeline" "block"
    And I click on "[data-region='paging-bar'] [data-control='next']" "css_element" in the "Timeline" "block"
    And I should see "Test feedback 2 closes" in the "Timeline" "block"
    And I should not see "Test assign 1 is due" in the "Timeline" "block"
    And I should not see "Test feedback 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 3 closes" in the "Timeline" "block"
    And I should not see "Test feedback 3 closes" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"

  Scenario: Persistent Overdue in date view
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    When I click on "Overdue" "link" in the "Timeline" "block"
    And I reload the page
    Then I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should not see "Test choice 2 closes" in the "Timeline" "block"
    And I should not see "Test feedback 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 1 closes" in the "Timeline" "block"
    And I should not see "Test choice 3 closes" in the "Timeline" "block"
    And I should not see "Test feedback 3 closes" in the "Timeline" "block"

  Scenario: Current filtering always applies in date view
    Given I log in as "student1"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    And I click on "Overdue" "link" in the "Timeline" "block"
    And I reload the page
    # Expand to 25 results, to ensure we see the earliest and latest timeline items.
    And I click on "Show 5 activities per page" "button" in the "Timeline" "block"
    And I click on "25" "link"
    And I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should not see "Test feedback 2 closes" in the "Timeline" "block"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by courses" "link" in the "Timeline" "block"
    And I click on "Filter timeline items" "button" in the "Timeline" "block"
    # Confirm that when we switch back to date view, the "All" filer continues to be applied (and not "overdue")
    When I click on "All" "link" in the "Timeline" "block"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by dates" "link" in the "Timeline" "block"
    Then I should see "Test assign 1 is due" in the "Timeline" "block"
    And I should see "Test feedback 2 closes" in the "Timeline" "block"
