@core @core_grades @gradereport_grader @javascript
Feature: Group searching functionality within the grader report.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher   | 1         | teacher1@example.com  | t1        |
      | student1  | Student   | 1         | student1@example.com  | s1        |
      | student2  | Student   | 2         | student2@example.com  | s2        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
    And the following "groups" exist:
      | name          | course | idnumber |
      | Default group | C1     | dg       |
      | Group 2       | C1     | g2       |
      | Tutor group   | C1     | tg       |
      | Marker group  | C1     | mg       |
    And the following "group members" exist:
      | user     | group |
      | student1 | dg    |
      | student2 | g2    |
    And I am on the "Course 1" "grades > Grader report > View" page logged in as "teacher1"

  Scenario: A teacher can see the 'group' search widget only when group mode is enabled in the course
    Given ".search-widget[data-searchtype='group']" "css_element" should exist
    And I am on the "C1" "course editing" page
    And I set the following fields to these values:
      | id_groupmode | No groups |
    And I press "Save and display"
    When I navigate to "View > Grader report" in the course gradebook
    Then ".search-widget[data-searchtype='group']" "css_element" should not exist

  Scenario: A teacher can search for and find a group to display
    Given I click on ".search-widget[data-searchtype='group']" "css_element"
    And I confirm "Tutor group" in "group" search within the gradebook widget exists
    And I confirm "Marker group" in "group" search within the gradebook widget exists
    When I set the field "Search groups" to "tutor"
    And I wait "1" seconds
    Then I confirm "Tutor group" in "group" search within the gradebook widget exists
    And I confirm "Marker group" in "group" search within the gradebook widget does not exist

  Scenario: A teacher can only see the group members in the 'user' search widget after selecting a group option
    Given I click on "Default group" in the "group" search widget
    And I should see "Student 1"
    And I should not see "Student 2"
    When I click on "Group 2" in the "group" search widget
    Then I should not see "Student 1"
    And I should see "Student 2"
