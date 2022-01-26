@block @block_timeline @javascript
Feature: The timeline block allows users to search for upcoming activities
  As a student
  I can search for the upcoming activities in the timeline block

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student  | 1        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category | startdate                  | enddate                    |
      | Course 1 | C1        | 0        | ##1 month ago##            | ##15 days ago##            |
      | Course 2 | C2        | 0        | ##yesterday##              | ##tomorrow##               |
      | Course 3 | C3        | 0        | ##first day of next month## | ##last day of next month## |
    And the following "activities" exist:
      | activity | course | idnumber  | name            | intro                     | timeopen                  | timeclose                  |
      | choice   | C2     | choice1   | Test choice 1   | Test choice description   | ##yesterday##             | ##tomorrow##               |
      | choice   | C1     | choice2   | Test choice 2   | Test choice description   | ##first day of +5 months## | ##last day of +5 months##  |
      | choice   | C3     | choice3   | Test choice 3   | Test choice description   | ##first day of +5 months## | ##last day of +10 months## |
      | choice   | C2     | choice4   | Test choice 4   | Test choice description   | ##first day of +5 months## | ##last day of +15 months## |
      | choice   | C1     | choice5   | Test choice 5   | Test choice description   | ##first day of +5 months## | ##last day of +20 months## |
      | choice   | C3     | choice6   | Test choice 6   | Test choice description   | ##first day of +5 months## | ##last day of +25 months## |
      | feedback | C2     | feedback1 | Test feedback 1 | Test feedback description | ##yesterday##             | ##tomorrow##               |
      | feedback | C1     | feedback2 | Test feedback 2 | Test feedback description | ##first day of +5 months## | ##last day of +5 months##  |
      | feedback | C3     | feedback3 | Test feedback 3 | Test feedback description | ##first day of +5 months## | ##last day of +10 months## |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C3     | student |

  Scenario: The search should return no events if I enter the wrong value
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    When I set the field "Search" in the "Timeline" "block" to "Fake example"
    Then I should see "No activities require action" in the "Timeline" "block"

  Scenario: Search for Course name
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    When I set the field "Search" in the "Timeline" "block" to "Course 1"
    Then I should see "Test choice 2" in the "Timeline" "block"
    And I should see "Test choice 5" in the "Timeline" "block"
    And I should see "Test feedback 2" in the "Timeline" "block"
    And I should not see "Test choice 1" in the "Timeline" "block"
    And I should not see "Test choice 3" in the "Timeline" "block"
    And I should not see "Test choice 4" in the "Timeline" "block"
    And I should not see "Test feedback 1" in the "Timeline" "block"
    And I should not see "Test feedback 3" in the "Timeline" "block"

  Scenario: Search for Activity name
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    When I set the field "Search" in the "Timeline" "block" to "Test choice 1"
    And I wait until "Test choice 2" "text" does not exist
    Then I should see "Test choice 1" in the "Timeline" "block"
    And I should not see "Test choice 2" in the "Timeline" "block"
    And I should not see "Test choice 3" in the "Timeline" "block"
    And I should not see "Test choice 4" in the "Timeline" "block"
    And I should not see "Test choice 5" in the "Timeline" "block"
    And I should not see "Test choice 6" in the "Timeline" "block"
    And I should not see "Test feedback 1" in the "Timeline" "block"
    And I should not see "Test feedback 2" in the "Timeline" "block"
    And I should not see "Test feedback 3" in the "Timeline" "block"

  Scenario: Search for Activity type
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    When I set the field "Search" in the "Timeline" "block" to "feedback"
    Then I should see "Test feedback 1" in the "Timeline" "block"
    And I should see "Test feedback 2" in the "Timeline" "block"
    And I should see "Test feedback 3" in the "Timeline" "block"
    And I should not see "Test choice 1" in the "Timeline" "block"
    And I should not see "Test choice 2" in the "Timeline" "block"
    And I should not see "Test choice 3" in the "Timeline" "block"
    And I should not see "Test choice 4" in the "Timeline" "block"
    And I should not see "Test choice 5" in the "Timeline" "block"

  Scenario: Timeline paginated search
    Given I log in as "student1"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "All" "link" in the "Timeline" "block"
    When I set the field "Search" in the "Timeline" "block" to "choice"
    Then I should see "Test choice 1" in the "Timeline" "block"
    And I should see "Test choice 2" in the "Timeline" "block"
    And I should see "Test choice 3" in the "Timeline" "block"
    And I should see "Test choice 4" in the "Timeline" "block"
    And I should see "Test choice 5" in the "Timeline" "block"
    And I should not see "Test choice 6" in the "Timeline" "block"
    And I click on "Show more activities" "button"
    And I should see "Test choice 6" in the "Timeline" "block"
