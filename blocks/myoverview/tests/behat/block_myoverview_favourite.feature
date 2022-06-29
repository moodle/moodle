@block @block_myoverview @javascript
Feature: The my overview block allows users to favourite their courses
  In order to enable the my overview block in a course
  As a student
  I can add the my overview block to my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
      | Course 3 | C3        | 0        |
      | Course 4 | C4        | 0        |
      | Course 5 | C5        | 0        |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
      | student1 | C4 | student |
      | student1 | C5 | student |

  Scenario: Favourite a course on a course card
    Given I am on the "My courses" page logged in as "student1"
    And I hover "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    When I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I click on "Star this course" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I reload the page
    Then "//div[@class='card dashboard-card' and contains(.,'Course 2')]//span[@data-region='is-favourite' and @aria-hidden='false']" "xpath_element" should exist
    And "//div[@class='card dashboard-card' and contains(.,'Course 2')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should not exist
    And "//div[@class='card dashboard-card' and contains(.,'Course 1')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should exist
    And "//div[@class='card dashboard-card' and contains(.,'Course 3')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should exist

  Scenario: Star a course and switch display to list
    Given I am on the "My courses" page logged in as "student1"
    And I hover "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    When I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Star this course" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    And I click on "List" "link" in the "Course overview" "block"
    Then "//li[contains(concat(' ', normalize-space(@class), ' '), 'list-group-item') and contains(.,'Course 5')]//span[@data-region='is-favourite' and @aria-hidden='false']" "xpath_element" should exist
    And "//li[contains(concat(' ', normalize-space(@class), ' '), 'list-group-item') and contains(.,'Course 5')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should not exist
    And "//li[contains(concat(' ', normalize-space(@class), ' '), 'list-group-item') and contains(.,'Course 1')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should exist
    And "//li[contains(concat(' ', normalize-space(@class), ' '), 'list-group-item') and contains(.,'Course 3')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should exist

  Scenario: Star a course and switch display to summary
    Given I am on the "My courses" page logged in as "student1"
    And I hover "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    When I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Star this course" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    And I click on "Summary" "link" in the "Course overview" "block"
    Then "//div[contains(concat(' ', normalize-space(@class), ' '), 'course-summaryitem') and contains(.,'Course 5')]//span[@data-region='is-favourite' and @aria-hidden='false']" "xpath_element" should exist
    And "//div[contains(concat(' ', normalize-space(@class), ' '), 'course-summaryitem') and contains(.,'Course 5')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should not exist
    And "//div[contains(concat(' ', normalize-space(@class), ' '), 'course-summaryitem') and contains(.,'Course 1')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should exist
    And "//div[contains(concat(' ', normalize-space(@class), ' '), 'course-summaryitem') and contains(.,'Course 3')]//span[@data-region='is-favourite' and @aria-hidden='true']" "xpath_element" should exist
