@block @block_myoverview @javascript
Feature: The my overview block allows users to easily access their courses and see upcoming activities
  In order to enable the my overview block in a course
  As a student
  I can add the my overview block to my dashboard

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
      | feedback | C3     | feedback3 | Test feedback 3 | Test feedback description | ##first day of +5 months## | ##last day of +5 months## |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |

  Scenario: View courses and upcoming activities on timeline view
    Given I log in as "student1"
    And I click on "Timeline" "link" in the "Course overview" "block"
    When I click on "Sort by dates" "link" in the "Course overview" "block"
    Then I should see "Next 7 days" in the "Course overview" "block"
    And I should see "Choice Test choice 1 closes" in the "Course overview" "block"
    And I should see "View choices" in the "Course overview" "block"
    And I should see "Feedback Test feedback 1 closes" in the "Course overview" "block"
    And I should see "Answer the questions" in the "Course overview" "block"
    And I should see "Future" in the "Course overview" "block"
    And I should see "Choice Test choice 3 closes" in the "Course overview" "block"
    And I should see "Feedback Test feedback 3 closes" in the "Course overview" "block"
    And I log out

  Scenario: Past activities should not be displayed on the timeline view
    Given I log in as "student1"
    And I click on "Timeline" "link" in the "Course overview" "block"
    When I click on "Sort by dates" "link" in the "Course overview" "block"
    And I should not see "Choice Test choice 2 closes" in the "Course overview" "block"
    And I log out

  Scenario: See the courses I am enrolled by their status on courses view
    Given I log in as "student1"
    And I click on "Courses" "link" in the "Course overview" "block"
    And I click on "In progress" "link" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I click on "Future" "link" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I log out

  Scenario: No activities should be displayed if the user is not enrolled
    Given I log in as "student2"
    And I click on "Timeline" "link" in the "Course overview" "block"
    And I should see "No upcoming activities" in the "Course overview" "block"
    When I click on "Courses" "link" in the "Course overview" "block"
    Then I should see "No courses" in the "Course overview" "block"
    And I log out
