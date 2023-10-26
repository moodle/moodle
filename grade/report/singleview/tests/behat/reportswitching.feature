@core @core_grades @gradereport_singleview @javascript
Feature: Given we land on the index page, select what type of report we wish to view.
  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher   | 1         | teacher1@example.com  | t1        |
      | student1  | Student   | 1         | student1@example.com  | s1        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
    And the following "activities" exist:
      | activity | course | idnumber | name                |
      | assign   | C1     | a1       | Test assignment one |

  Scenario: I switch between the two report types within singleview
    Given I am on the "Course 1" "grades > Single view > View" page logged in as "teacher1"
    And I click on "Grade items" "link" in the ".page-toggler" "css_element"
    And ".search-widget[data-searchtype='user']" "css_element" should not exist
    And ".search-widget[data-searchtype='grade']" "css_element" should exist
    And I confirm "assign" in "grade" search within the gradebook widget exists
    When I click on "Users" "link" in the ".page-toggler" "css_element"
    Then ".search-widget[data-searchtype='grade']" "css_element" should not exist
    And ".search-widget[data-searchtype='user']" "css_element" should exist
    And I confirm "student1" in "user" search within the gradebook widget exists
