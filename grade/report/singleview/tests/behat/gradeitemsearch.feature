@core @core_grades @gradereport_singleview @javascript
Feature: Given we have opted to search for a grade item, Lets find and search them.
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username | firstname | lastname | email                | idnumber |
      | teacher1 | Teacher   | 1        | teacher1@example.com | t1       |
      | student1 | Student   | 1        | student1@example.com | s1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                |
      | assign   | C1     | a1       | Test assignment one |
      | assign   | C1     | a2       | Test assignment two |

  Scenario: A teacher can search for and find a grade item to view
    Given I am on the "Course 1" "grades > Single view > View" page logged in as "teacher1"
    And I change window size to "large"
    And I click on "Grade items" "link" in the ".page-toggler" "css_element"
    When I click on ".search-widget[data-searchtype='grade']" "css_element"
    Then I confirm "Test assignment one" in "grade" search within the gradebook widget exists
    And I confirm "Test assignment two" in "grade" search within the gradebook widget exists
    And I set the field "Search grade items" to "two"
    And I wait "1" seconds
    And I confirm "Test assignment two" in "grade" search within the gradebook widget exists
    And I confirm "Test assignment one" in "grade" search within the gradebook widget does not exist
