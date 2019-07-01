@block @block_myoverview @javascript
Feature: The my overview block allows admins to easily configure the students' course list
  In order to adapt the my overview block to my users' needs
  As an admin
  I can configure the appearance of the my overview block

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

  Scenario: Enable 'All' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "All" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    # We have to check for the data attribute instead of the list element text as we would get false positives from the "All (except hidden)" element otherwise
    Then "[data-value='allincludinghidden']" "css_element" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'All' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "All" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    # We have to check for the data attribute instead of the list element text as we would get false negatives "All (except hidden)" element otherwise
    Then "[data-value='allincludinghidden']" "css_element" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Enable 'All (except hidden)' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "All (except hidden)" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "All (except hidden)" "list_item" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'All (except hidden)' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "All (except hidden)" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    # 'All (except hidden)' option has been disabled, so the button is falling back to the 'In progress' option which is the next enabled option.
    And I click on "In progress" "button" in the "Course overview" "block"
    Then "All (except hidden)" "list_item" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Enable 'In progress' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "In progress" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "In progress" "list_item" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'In progress' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "In progress" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "In progress" "list_item" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Enable 'Future' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Future" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Future" "list_item" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'Future' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Future" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Future" "list_item" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Enable 'Past' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Past" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Past" "list_item" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'Past' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Past" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Past" "list_item" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Enable 'Starred' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Starred" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Starred" "list_item" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'Starred' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Starred" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Starred" "list_item" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Enable 'Hidden' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Hidden" to "1"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Hidden" "list_item" should exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable 'Hidden' course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "Hidden" to "0"
    And I press "Save"
    And I log out
    Then I log in as "student1"
    And I click on "All (except hidden)" "button" in the "Course overview" "block"
    Then "Hidden" "list_item" should not exist in the ".block_myoverview .dropdown-menu" "css_element"
    And I log out

  Scenario: Disable all course filter options
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "All" to "0"
    And I set the field "All (except hidden)" to "0"
    And I set the field "In progress" to "0"
    And I set the field "Future" to "0"
    And I set the field "Past" to "0"
    And I set the field "Starred" to "0"
    And I set the field "Hidden" to "0"
    And I press "Save"
    And I log out
    And I log in as "student1"
    Then "button#groupingdropdown" "css_element" should not exist in the ".block_myoverview" "css_element"
    And I should see "Course 1" in the "Course overview" "block"
    And I should see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should see "Course 4" in the "Course overview" "block"
    And I should see "Course 5" in the "Course overview" "block"
    And I log out

  Scenario: Disable all but one course filter option
    Given I log in as "admin"
    And I navigate to "Plugins > Blocks > Course overview" in site administration
    And I set the field "All" to "0"
    And I set the field "All (except hidden)" to "0"
    And I set the field "In progress" to "1"
    And I set the field "Future" to "0"
    And I set the field "Past" to "0"
    And I set the field "Starred" to "0"
    And I set the field "Hidden" to "0"
    And I press "Save"
    And I log out
    And I log in as "student1"
    Then "button#groupingdropdown" "css_element" should not exist in the ".block_myoverview" "css_element"
    And I should see "Course 2" in the "Course overview" "block"
    And I should see "Course 3" in the "Course overview" "block"
    And I should see "Course 4" in the "Course overview" "block"
    And I should not see "Course 1" in the "Course overview" "block"
    And I should not see "Course 5" in the "Course overview" "block"
    And I log out
