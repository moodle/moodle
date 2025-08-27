@mod @mod_feedback
Feature: Testing overview integration in mod_feedback
  In order to list all feedbacks in a course
  As a user
  I need to be able to see the feedback overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Username  | 1        |
      | student2 | Username  | 2        |
      | student3 | Username  | 3        |
      | student4 | Username  | 4        |
      | student5 | Username  | 5        |
      | student6 | Username  | 6        |
      | student7 | Username  | 7        |
      | student8 | Username  | 8        |
      | teacher1 | Teacher   | T        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1         |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | student4 | C1     | student        |
      | student5 | C1     | student        |
      | student6 | C1     | student        |
      | student7 | C1     | student        |
      | student8 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name                   | course | idnumber  | timeclose            |
      | feedback | Date feedback          | C1     | feedback1 | ##1 Jan 2040 08:00## |
      | feedback | Not responded feedback | C1     | feedback2 | ##tomorrow noon##    |
      | feedback | No date feedback       | C1     | feedback3 |                      |
    Given the following "mod_feedback > question" exists:
      | activity     | feedback1                               |
      | name         | Do you like this course?                |
      | questiontype | multichoice                             |
      | label        | multichoice1                            |
      | subtype      | r                                       |
      | hidenoselect | 1                                       |
      | values       | Yes of course\nNot at all\nI don't know |
    And the following "mod_feedback > responses" exist:
      | activity  | user     | Do you like this course? |
      | feedback1 | student1 | Not at all               |
      | feedback1 | student2 | I don't know             |
      | feedback1 | student3 | Not at all               |
      | feedback1 | student4 | Yes of course            |
      | feedback3 | student1 | Not at all               |
      | feedback3 | student2 | I don't know             |
      | feedback3 | student3 | Not at all               |

  Scenario: Teacher can see the feedback relevant information in the feedback overview
    When I am on the "Course 1" "course > activities > feedback" page logged in as "teacher1"
    Then the following should exist in the "Table listing all Feedback activities" table:
      | Name                   | Due date       | Responses | Actions  |
      | Date feedback          | 1 January 2040 | 4         | View     |
      | Not responded feedback | Tomorrow       | 0         | View     |
      | No date feedback       | -              | 3         | View     |
    And I should not see "Responded" in the "feedback_overview_collapsible" "region"
    And I click on "View" "link" in the "Date feedback" "table_row"
    And I should see "Show responses"

  Scenario: Students can see the feedback relevant information in the feedback overview
    When I am on the "Course 1" "course > activities > feedback" page logged in as "student1"
    Then the following should exist in the "Table listing all Feedback activities" table:
      | Name                   | Due date       | Responded |
      | Date feedback          | 1 January 2040 |           |
      | Not responded feedback | Tomorrow       | -         |
      | No date feedback       | -              |           |
    And "You have already submitted this feedback" "icon" should exist in the "Date feedback" "table_row"
    And "You have already submitted this feedback" "icon" should exist in the "No date feedback" "table_row"

  Scenario: The feedback overview report should generate log events
    Given I am on the "Course 1" "course > activities > feedback" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the instance list for the module 'feedback'"
