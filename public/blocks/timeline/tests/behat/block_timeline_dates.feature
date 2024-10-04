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
      | feedback | C2     | feedback4 | Test feedback 4 | Test feedback description | ##yesterday## | ##now +1 minute## |
    And the following "activities" exist:
      | activity | course | idnumber  | name            | intro                   | timeopen        | duedate           |
      | assign   | C1     | assign1   | Test assign 1   | Test assign description | ##1 month ago## | ##yesterday##     |
      | assign   | C2     | assign2   | Test assign 2   | Test assign description | ##yesterday##   | ##now -1 minute## |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
    And I change window size to "large"

  Scenario: Next 7 days in date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "Next 7 days" "link" in the "Timeline" "block"
    Then "Test choice 1" "link" should exist in the "Timeline" "block"
    And I should see "Choice closes · Course 2" in the "Timeline" "block"
    And "Test feedback 1" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 2" in the "Timeline" "block"
    And "Test assign 2" "link" should exist in the "Timeline" "block"
    And "Test feedback 4" "link" should exist in the "Timeline" "block"
    And "Test choice 2" "link" should not exist in the "Timeline" "block"
    And "Test choice 3" "link" should not exist in the "Timeline" "block"
    And "Test feedback 3" "link" should not exist in the "Timeline" "block"
    And "Test assign 1" "link" should not exist in the "Timeline" "block"
    And I should not see "Assignment is due · Course 1" in the "Timeline" "block"

  Scenario: Overdue in date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "Overdue" "link" in the "Timeline" "block"
    Then "Test assign 1" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 1" in the "Timeline" "block"
    And "Test assign 2" "link" should exist in the "Timeline" "block"
    And "Test choice 2" "link" should not exist in the "Timeline" "block"
    And "Test feedback 1" "link" should not exist in the "Timeline" "block"
    And "Test choice 1" "link" should not exist in the "Timeline" "block"
    And "Test choice 3" "link" should not exist in the "Timeline" "block"
    And "Test feedback 3" "link" should not exist in the "Timeline" "block"
    And "Test feedback 4" "link" should not exist in the "Timeline" "block"

  Scenario: All in date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "All" "link" in the "Timeline" "block"
    Then "Test assign 1" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 1" in the "Timeline" "block"
    And "Test assign 2" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 2" in the "Timeline" "block"
    And "Test feedback 1" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 2" in the "Timeline" "block"
    And "Test choice 1" "link" should exist in the "Timeline" "block"
    And I should see "Choice closes · Course 2" in the "Timeline" "block"
    And "Test feedback 4" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 2" in the "Timeline" "block"
    And "Test choice 2" "link" should not exist in the "Timeline" "block"
    And "Test feedback 2" "link" should not exist in the "Timeline" "block"
    And I click on "Show more activities" "button"
    And "Test feedback 2" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 1" in the "Timeline" "block"
    And "Test choice 3" "link" should exist in the "Timeline" "block"
    And I should see "Test assign 1" in the "Timeline" "block"
    And I should see "Test feedback 1" in the "Timeline" "block"
    And I should see "Test choice 1" in the "Timeline" "block"
    And I should see "Test choice 3" in the "Timeline" "block"
    And I should see "Test feedback 3" in the "Timeline" "block"
    And I should not see "Test choice 2" in the "Timeline" "block"

  Scenario: Persistent All in date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "All" "link" in the "Timeline" "block"
    And I reload the page
    Then "Test assign 1" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 1" in the "Timeline" "block"
    And "Test assign 2" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 2" in the "Timeline" "block"
    And "Test feedback 1" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 2" in the "Timeline" "block"
    And "Test choice 1" "link" should exist in the "Timeline" "block"
    And I should see "Choice closes · Course 2" in the "Timeline" "block"
    And "Test feedback 4" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 2" in the "Timeline" "block"
    And I should not see "Test choice 2" in the "Timeline" "block"
    And I should not see "Test feedback 2" in the "Timeline" "block"
    And I click on "Show more activities" "button"
    And "Test feedback 2" "link" should exist in the "Timeline" "block"
    And I should see "Feedback closes · Course 1" in the "Timeline" "block"
    And I should see "Test assign 1" in the "Timeline" "block"
    And I should see "Test feedback 1" in the "Timeline" "block"
    And I should see "Test feedback 3" in the "Timeline" "block"
    And I should see "Test choice 1" in the "Timeline" "block"
    And I should not see "Test choice 2" in the "Timeline" "block"
    And I should see "Test choice 3" in the "Timeline" "block"

  Scenario: Persistent Overdue in date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "Overdue" "link" in the "Timeline" "block"
    And I reload the page
    Then "Test assign 1" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 1" in the "Timeline" "block"
    And "Test assign 2" "link" should exist in the "Timeline" "block"
    And I should see "Assignment is due · Course 2" in the "Timeline" "block"
    And "Test feedback 1" "link" should not exist in the "Timeline" "block"
    And "Test feedback 3" "link" should not exist in the "Timeline" "block"
    And "Test feedback 4" "link" should not exist in the "Timeline" "block"
    And "Test choice 1" "link" should not exist in the "Timeline" "block"
    And "Test choice 2" "link" should not exist in the "Timeline" "block"
    And "Test choice 3" "link" should not exist in the "Timeline" "block"

  Scenario: Current filtering always applies in date view
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "Overdue" "link" in the "Timeline" "block"
    And I reload the page
    And "Test assign 1" "link" should exist in the "Timeline" "block"
    And "Test feedback 2" "link" should not exist in the "Timeline" "block"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by courses" "link" in the "Timeline" "block"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    # Confirm that when we switch back to date view, the "All" filer continues to be applied (and not "overdue")
    When I click on "All" "link" in the "Timeline" "block"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by dates" "link" in the "Timeline" "block"
    Then "Test assign 1" "link" should exist in the "Timeline" "block"
    And I click on "Show more activities" "button"
    And "Test feedback 2" "link" should exist in the "Timeline" "block"

  Scenario: Student not enrolled in any courses sees a message
    Given I log in as "student2"
    When I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by dates" "link" in the "Timeline" "block"
    Then I should see "No in-progress courses" in the "Timeline" "block"
    And I should not see "Test choice 1"
