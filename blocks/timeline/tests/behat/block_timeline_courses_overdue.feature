@block @block_timeline @javascript
Feature: The timeline block allows users to see courses with overdue activities
  In order to view overdue activities in the timeline block
  As a student
  I can select the overdue filter in courses view

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | 1        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category | startdate         | enddate      |
      | Course 1 | C1        | 0        | ##now -3 months## | ##tomorrow## |
      | Course 2 | C2        | 0        | ##yesterday##     | ##tomorrow## |
      | Course 3 | C3        | 0        | ##yesterday##     | ##tomorrow## |
      | Course 4 | C4        | 0        | ##yesterday##     | ##tomorrow## |
      | Course 5 | C5        | 0        | ##yesterday##     | ##tomorrow## |
      | Course 6 | C6        | 0        | ##yesterday##     | ##tomorrow## |
    And the following "activities" exist:
      | activity | course | idnumber  | name          | intro                  | timeopen          | duedate             |
      | assign   | C1     | assign1   | Test assign 1 | Assign due last month  | ##now -2 months## | ##now -1 month##    |
      | assign   | C2     | assign2   | Test assign 2 | Assign due yesterday   | ##now -2 days##   | ##yesterday##       |
      | assign   | C3     | assign3   | Test assign 3 | Assign due yesterday   | ##now -2 days##   | ##yesterday##       |
      | assign   | C4     | assign4   | Test assign 4 | Assign due later today | ##yesterday##     | ##now +10 minutes## |
      | assign   | C5     | assign5   | Test assign 5 | Assign due yesterday   | ##now -2 days##   | ##yesterday##       |
      | assign   | C6     | assign6   | Test assign 6 | Assign due tomorrow    | ##yesterday##     | ##tomorrow##        |

  Scenario: No activities to display as overdue displays expected message
    Given the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C4     | student |
      | student1 | C6     | student |
    Given I log in as "student1"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by courses" "link" in the "Timeline" "block"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    When I click on "Overdue" "link" in the "Timeline" "block"
    Then I should see "No activities require action" in the "Timeline" "block"
    And I reload the page
    And I should see "No activities require action" in the "Timeline" "block"

  Scenario: If filtering by overdue, only courses with a matching item are included
    Given the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C4     | student |
      | student1 | C5     | student |
      | student1 | C6     | student |
    When I log in as "student1"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by courses" "link" in the "Timeline" "block"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "Overdue" "link" in the "Timeline" "block"
    Then I should not see "Show more courses" in the "Timeline" "block"
    And I should see "Course 2" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should see "Course 5" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 1" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 3" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 4" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 6" in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 2" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 5" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 1" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 3" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 4" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 6" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And I reload the page
    And I should not see "Show more courses" in the "Timeline" "block"
    And I should see "Course 2" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should see "Course 5" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 1" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 3" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 4" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 6" in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 2" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 5" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 1" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 3" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 4" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 6" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"

  Scenario: If filtering by overdue, only courses with a matching item are included and loading more is supported
    Given the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student1 | C2     | student |
      | student1 | C3     | student |
      | student1 | C4     | student |
      | student1 | C5     | student |
      | student1 | C6     | student |
    When I log in as "student1"
    And I click on "Sort timeline items" "button" in the "Timeline" "block"
    And I click on "Sort by courses" "link" in the "Timeline" "block"
    And I click on "Filter timeline by date" "button" in the "Timeline" "block"
    And I click on "Overdue" "link" in the "Timeline" "block"
    And I click on "Show more courses" "button" in the "Timeline" "block"
    Then I should see "Course 2" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should see "Course 3" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should see "Course 5" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 1" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 4" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 6" in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 2" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 3" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 5" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 1" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 4" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 6" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Show more courses" in the "Timeline" "block"
    And I reload the page
    And I click on "Show more courses" "button" in the "Timeline" "block"
    And I should see "Course 2" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should see "Course 3" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should see "Course 5" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 1" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 4" in the ".block-timeline [data-region='view-courses']" "css_element"
    And I should not see "Course 6" in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 2" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 3" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 5" "link" should exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 1" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 4" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
    And "Test assign 6" "link" should not exist in the ".block-timeline [data-region='view-courses']" "css_element"
