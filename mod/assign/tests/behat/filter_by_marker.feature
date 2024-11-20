@mod @mod_assign
Feature: In an assignment, teachers can filter displayed submissions by assigned marker
  In order to manage submissions more easily
  As a teacher
  I need to view submissions allocated to markers.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | marker1  | Marker    | 1        | marker1@example.com  |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | marker1  | C1     | teacher        |
    And the following "activity" exists:
      | activity | assign               |
      | course   | C1                   |
      | name     | Test assignment name |

  @javascript
  Scenario: The marker filter is available only when marking workflow and marking allocation is enabled in the assignment
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And "Marker" "select" should not exist in the ".extrafilters .dropdown-menu" "css_element"
    And I am on the "Test assignment name" "assign activity editing" page
    And I expand all fieldsets
    When I set the field "Use marking workflow" to "Yes"
    And I set the field "Use marking allocation" to "Yes"
    And I press "Save and display"
    And I navigate to "Submissions" in current page administration
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    Then "Marker" "select" should exist in the ".extrafilters .dropdown-menu" "css_element"
    And the field "Marker" matches value "No filter"
    And the "Marker" select box should contain "No filter"
    And the "Marker" select box should contain "No marker"
    And the "Marker" select box should contain "Teacher 1"
    And the "Marker" select box should contain "Marker 1"
    And the "Marker" select box should not contain "Student 1"
    And the "Marker" select box should not contain "Student 2"

  @javascript
  Scenario: Allocate markers to submissions and filter by marker
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Use marking workflow" to "Yes"
    And I set the field "Use marking allocation" to "Yes"
    And I press "Save and display"
    And I am on the "Test assignment name" "assign activity" page
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I am on the "Test assignment name" Activity page
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    # Set the Marker filter to 'Marker 1'.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marker" in the ".extrafilters .dropdown-menu" "css_element" to "Marker 1"
    When I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 1 is now displayed in the submissions table.
    Then the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    # Ensure the badge indicating the number of applied filters is present.
    And ".badge" "css_element" should exist in the ".extrafilters .dropdown-toggle" "css_element"
    And I should see "+1" in the ".extrafilters .badge" "css_element"
    # Ensure the filter remains applied when navigating away from and returning to the assignment submissions page.
    And I am on the "Test assignment name" Activity page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Marker" matches value "Marker 1"
    # Ensure the filter is not applied unless the 'Apply' button is pressed.
    And I set the field "Marker" in the ".extrafilters .dropdown-menu" "css_element" to "No marker"
    And I click on "Close" "link" in the ".extrafilters .dropdown-menu" "css_element"
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Marker" matches value "Marker 1"
    # Set the Marker filter to 'No marker'.
    And I set the field "Marker" in the ".extrafilters .dropdown-menu" "css_element" to "No marker"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 2 is now displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    # Set the Marker filter to 'No filter'.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marker" in the ".extrafilters .dropdown-menu" "css_element" to "No filter"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure all student are now displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |

  @javascript
  Scenario: The applied marker filter can be reset using the 'Clear all' option
    Given I am on the "Test assignment name" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Use marking workflow" to "Yes"
    And I set the field "Use marking allocation" to "Yes"
    And I press "Save and display"
    And I am on the "Test assignment name" "assign activity" page
    And I change window size to "large"
    And I go to "Student 1" "Test assignment name" activity advanced grading page
    # Allocate Marker 1 as the marker of Student 1.
    And I set the field "allocatedmarker" to "Marker 1"
    And I set the field "Notify student" to "0"
    And I press "Save changes"
    And I follow "View all submissions"
    # Ensure the 'Clear all' option is not available until the marker filter has been applied.
    And "Clear all" "link" should not exist in the ".tertiary-navigation" "css_element"
    # Set the Marker filter to 'Marker 1'.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marker" in the ".extrafilters .dropdown-menu" "css_element" to "Marker 1"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 1 is now displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    # Ensure the 'Clear all' option is now available.
    And "Clear all" "link" should exist in the ".tertiary-navigation" "css_element"
    # Ensure the marker filter is reset when the 'Clear All' option is triggered.
    When I click on "Clear all" "link" in the ".tertiary-navigation" "css_element"
    Then the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Marker" matches value "No filter"
