@block @block_myoverview @javascript
Feature: The my overview block allows users to easily access their courses
  In order to enable the my overview block in a course
  As a student
  I can add the my overview block to my dashboard

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | student1 | Student   | X        | student1@example.com | S1       |
    And the following "categories" exist:
      | name        | category | idnumber |
      | Category 1  | 0        | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category | startdate                   | enddate         |
      | Course 1 | C1        | 0        | ##1 month ago##             | ##15 days ago## |
      | Course 2 | C2        | 0        | ##yesterday##               | ##tomorrow## |
      | Course 3 | C3        | 0        | ##yesterday##               | ##tomorrow## |
      | Course 4 | C4        | CAT1     | ##yesterday##               | ##tomorrow## |
      | Course 5 | C5        | 0        | ##first day of next month## | ##last day of next month## |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student1 | C2 | student |
      | student1 | C3 | student |
      | student1 | C4 | student |
      | student1 | C5 | student |

  Scenario: View past courses
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View future courses
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Future" "link" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I log out

  Scenario: View inprogress courses
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "In progress" "link" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View all courses
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "All" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View inprogress courses - test persistence
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    And I click on "In progress" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "In progress" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View all courses - w/ persistence
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "All" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "All" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View past courses - w/ persistence
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Past" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View future courses - w/ persistence
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Future" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Future" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I log out

  Scenario: List display  persistence
    Given I log in as "student1"
    And I click on "Display dropdown" "button" in the "Course overview" "block"
    And I click on "List" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "List" in the "Course overview" "block"
    And "[data-display='list']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Cards display  persistence
    Given I log in as "student1"
    And I click on "Display dropdown" "button" in the "Course overview" "block"
    And I click on "Card" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Card" in the "Course overview" "block"
    And "[data-display='cards']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Summary display  persistence
    Given I log in as "student1"
    And I click on "Display dropdown" "button" in the "Course overview" "block"
    And I click on "Summary" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Summary" in the "Course overview" "block"
    And "[data-display='summary']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Course name sort persistence
    Given I log in as "student1"
    And I click on "sortingdropdown" "button" in the "Course overview" "block"
    And I click on "Course name" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Course name" in the "Course overview" "block"
    And "[data-sort='fullname']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Last accessed sort persistence
    Given I log in as "student1"
    And I click on "sortingdropdown" "button" in the "Course overview" "block"
    And I click on "Last accessed" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Last accessed" in the "Course overview" "block"
    And "[data-sort='ul.timeaccess desc']" "css_element" in the "Course overview" "block" should be visible

  Scenario: View inprogress courses with hide persistent functionality
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "In progress" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I click on "Hide from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I reload the page
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View past courses with hide persistent functionality
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 1')]" "xpath_element"
    And I click on "Hide from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 1')]" "xpath_element"
    And I reload the page
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: View future courses with hide persistent functionality
    Given I log in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Future" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Hide from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I reload the page
    Then I should not see "Course 5" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I log out

  Scenario: Show course category in cards display
    Given I log in as "student1"
    And I click on "Display dropdown" "button" in the "Course overview" "block"
    When I click on "Card" "link" in the "Course overview" "block"
    Then I should see "Category 1" in the "Course overview" "block"

  Scenario: Show course category in list display
    Given I log in as "student1"
    And I click on "Display dropdown" "button" in the "Course overview" "block"
    When I click on "List" "link" in the "Course overview" "block"
    Then I should see "Category 1" in the "Course overview" "block"

  Scenario: Show course category in summary display
    Given I log in as "student1"
    And I click on "Display dropdown" "button" in the "Course overview" "block"
    When I click on "Summary" "link" in the "Course overview" "block"
    Then I should see "Category 1" in the "Course overview" "block"
