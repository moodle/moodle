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
    Given ".groupsearchwidget" "css_element" should exist
    And I am on the "C1" "course editing" page
    And I set the following fields to these values:
      | id_groupmode | No groups |
    And I press "Save and display"
    When I navigate to "View > Grader report" in the course gradebook
    Then ".groupsearchwidget" "css_element" should not exist

  Scenario: A teacher can search for and find a group to display
    Given I confirm "Tutor group" in "group" search within the gradebook widget exists
    And I confirm "Marker group" in "group" search within the gradebook widget exists
    When I set the field "Search groups" to "tutor"
    And I wait until "Marker group" "option_role" does not exist
    Then I confirm "Tutor group" in "group" search within the gradebook widget exists
    And I confirm "Marker group" in "group" search within the gradebook widget does not exist
    And I click on "Tutor group" in the "group" search widget
    # The search input remains in the field on reload this is in keeping with other search implementations.
    And I click on ".groupsearchwidget" "css_element"
    And the field "Search groups" matches value "tutor"
    Then I set the field "Search groups" to "Turtle"
    And I should see "No results for \"Turtle\""

  Scenario: A teacher can only see the group members in the 'user' search widget after selecting a group option
    # Confirm that all users are initially displayed in the 'user' search widget.
    Given I set the field "Search users" to "Student"
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget exists
    # Select a particular group from the 'group' search widget.
    When I click on "Default group" in the "group" search widget
    # Confirm that only users which are members of the selected group are displayed in the 'user' search widget.
    And I set the field "Search users" to "Student"
    Then I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget does not exist
    And I click on "Tutor group" in the "group" search widget
    And I set the field "Search users" to "Student"
    And I confirm "Student 1" in "user" search within the gradebook widget does not exist
    And I confirm "Student 2" in "user" search within the gradebook widget does not exist
    And I click on "All participants" in the "group" search widget
    And I set the field "Search users" to "Student"
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget exists

  @accessibility
  Scenario: A teacher can set focus and search using the input with a keyboard
    Given I click on ".groupsearchwidget" "css_element"
    # Basic tests for the page.
    And the page should meet accessibility standards
    And the page should meet "wcag131, wcag141, wcag412" accessibility standards
    And the page should meet accessibility standards with "wcag131, wcag141, wcag412" extra tests
    # Move onto general keyboard navigation testing.
    And I click on "Search groups" "field"
    And I wait until "Default group" "option_role" exists
    And I press the down key
    And the focused element is "All participants" "option_role"
    And I press the end key
    And the focused element is "Tutor group" "option_role"
    And I press the home key
    And the focused element is "All participants" "option_role"
    And I press the up key
    And the focused element is "Tutor group" "option_role"
    And I press the down key
    And the focused element is "All participants" "option_role"
    And I press the escape key
    And the focused element is "Search groups" "field"
    Then I set the field "Search groups" to "Goodmeme"
    And I wait until "Tutor group" "option_role" does not exist
    And I press the down key
    And the focused element is "Search groups" "field"

    And I navigate to "View > User report" in the course gradebook
    And I click on ".groupsearchwidget" "css_element"
    And I set the field "Search groups" to "Tutor"
    And I wait until "All participants" "option_role" does not exist
    And I press the down key
    And the focused element is "Tutor group" "option_role"

    # Lets check the tabbing order.
    And I set the field "Search groups" to "Marker"
    And I wait until "Marker group" "option_role" exists
    And I press the tab key
    And the focused element is "Clear search input" "button"
    And I press the enter key
    And I wait until the page is ready
    And ".groupsearchwidget" "css_element" should exist
