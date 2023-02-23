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
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: View future courses
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Future" "link" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"

  Scenario: View inprogress courses
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "In progress" "link" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: View all (except removed) courses
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "All" "link" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"

  Scenario: View all (including removed from view) courses
    Given the following config values are set as admin:
      | config                            | value | plugin           |
      | displaygroupingallincludinghidden | 1     | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    # We have to click on the data attribute instead of the button element text as we might risk to click on the false positive "All (including removed from view)" element instead
    When I click on "[data-value='allincludinghidden']" "css_element" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"

  Scenario: View inprogress courses - test persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    And I click on "In progress" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "In progress" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: View all (except removed) courses - w/ persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "All" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "All" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    Then I should see "Course 2" in the "Course overview" "block"
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"

  Scenario: View past courses - w/ persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Past" in the "Course overview" "block"
    Then I should see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: View future courses - w/ persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Future" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Future" in the "Course overview" "block"
    Then I should see "Course 5" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"

  Scenario: View favourite courses - w/ persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I click on "Star this course" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Starred" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Starred" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: List display  persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    And I click on "List" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "List" in the "Course overview" "block"
    And "[data-display='list']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Cards display  persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    And I click on "Card" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Card" in the "Course overview" "block"
    And "[data-display='card']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Summary display  persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    And I click on "Summary" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Summary" in the "Course overview" "block"
    And "[data-display='summary']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Course name sort persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "sortingdropdown" "button" in the "Course overview" "block"
    And I click on "Sort by course name" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Sort by course name" in the "Course overview" "block"
    And "[data-sort='fullname']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Last accessed sort persistence
    Given I am on the "My courses" page logged in as "student1"
    And I click on "sortingdropdown" "button" in the "Course overview" "block"
    And I click on "Sort by last accessed" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Sort by last accessed" in the "Course overview" "block"
    And "[data-sort='ul.timeaccess desc']" "css_element" in the "Course overview" "block" should be visible

  Scenario: Short name sort persistence
    Given I am on the "My courses" page logged in as "student1"
    When I click on "sortingdropdown" "button" in the "Course overview" "block"
    Then I should not see "Sort by short name" in the "Course overview" "block"
    When the following config values are set as admin:
      | config               | value |
      | courselistshortnames | 1     |
    And I reload the page
    And I click on "sortingdropdown" "button" in the "Course overview" "block"
    And I click on "Sort by short name" "link" in the "Course overview" "block"
    And I reload the page
    Then I should see "Sort by short name" in the "Course overview" "block"
    And "[data-sort='shortname']" "css_element" in the "Course overview" "block" should be visible

  Scenario: View inprogress courses with hide persistent functionality
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "In progress" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I click on "Remove from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 2')]" "xpath_element"
    And I reload the page
    Then I should see "Course 3" in the "Course overview" "block"
    Then I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: View past courses with hide persistent functionality
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Past" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 1')]" "xpath_element"
    And I click on "Remove from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 1')]" "xpath_element"
    And I reload the page
    Then I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"

  Scenario: View future courses with hide persistent functionality
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "Future" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Remove from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I reload the page
    Then I should not see "Course 5" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 2" in the "Course overview" "block"
    And I should not see "Course 3" in the "Course overview" "block"
    And I should not see "Course 4" in the "Course overview" "block"

  Scenario: View all (except hidden) courses with hide persistent functionality
    Given I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    When I click on "All" "link" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Remove from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I reload the page
    Then I should not see "Course 5" in the "Course overview" "block"
    And I should see "Course 1" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should see "Course 4" in the "Course overview" "block"

  Scenario: View all (including removed from view) courses with hide persistent functionality
    Given the following config values are set as admin:
      | config                            | value | plugin           |
      | displaygroupingallincludinghidden | 1     | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "All" "button" in the "Course overview" "block"
    # We have to click on the data attribute instead of the button element text as we might risk to click on the false positive "All (including removed from view)" element instead
    When I click on "[data-value='allincludinghidden']" "css_element" in the "Course overview" "block"
    And I click on ".coursemenubtn" "css_element" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I click on "Remove from view" "link" in the "//div[@class='card dashboard-card' and contains(.,'Course 5')]" "xpath_element"
    And I reload the page
    Then I should see "Course 5" in the "Course overview" "block"
    And I should see "Course 1" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should see "Course 4" in the "Course overview" "block"

  Scenario: Show course category in cards display
    Given the following config values are set as admin:
      | displaycategories | 1 | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    When I click on "Card" "link" in the "Course overview" "block"
    Then I should see "Category 1" in the "Course overview" "block"

  Scenario: Show course category in list display
    Given the following config values are set as admin:
      | displaycategories | 1 | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    When I click on "List" "link" in the "Course overview" "block"
    Then I should see "Category 1" in the "Course overview" "block"

  Scenario: Show course category in summary display
    Given the following config values are set as admin:
      | displaycategories | 1 | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    When I click on "Summary" "link" in the "Course overview" "block"
    Then I should see "Category 1" in the "Course overview" "block"

  Scenario: Hide course category in cards display
    Given the following config values are set as admin:
      | displaycategories | 0 | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    When I click on "Card" "link" in the "Course overview" "block"
    Then I should not see "Category 1" in the "Course overview" "block"

  Scenario: Hide course category in list display
    Given the following config values are set as admin:
      | displaycategories | 0 | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    When I click on "List" "link" in the "Course overview" "block"
    Then I should not see "Category 1" in the "Course overview" "block"

  Scenario: Show course category in summary display
    Given the following config values are set as admin:
      | displaycategories | 0 | block_myoverview |
    And I am on the "My courses" page logged in as "student1"
    And I click on "Display drop-down menu" "button" in the "Course overview" "block"
    When I click on "Summary" "link" in the "Course overview" "block"
    Then I should not see "Category 1" in the "Course overview" "block"

  @accessibility
  Scenario: The dashboard page must have sufficient colour contrast
    When I am on the "My courses" page logged in as "student1"
    Then the page should meet "wcag143" accessibility standards
