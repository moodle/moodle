@mod @mod_assign
Feature: In an assignment, teachers can filter displayed submissions by marking workflow status.
  In order to manage submissions more easily
  As a teacher
  I need to filter submissions by their marking workflow status.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
      | student3 | Student   | 3        | student3@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activity" exists:
      | activity | assign          |
      | course   | C1              |
      | name     | Test assignment |

  @javascript
  Scenario: The marking workflow filter is available only when marking workflow is enabled in the assignment.
    Given I am on the "Test assignment" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    When I am on the "Test assignment" "assign activity editing" page
    And I expand all fieldsets
    And I set the field "Use marking workflow" to "Yes"
    And I press "Save and display"
    And I navigate to "Submissions" in current page administration
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    Then "Marking state" "select" should exist in the ".extrafilters .dropdown-menu" "css_element"
    And the field "Marking state" matches value "No filter"
    And the "Marking state" select box should contain "No filter"
    And the "Marking state" select box should contain "Not marked"
    And the "Marking state" select box should contain "Marking completed"
    And the "Marking state" select box should contain "In review"
    And the "Marking state" select box should contain "Ready for release"
    And the "Marking state" select box should contain "Released"

  @javascript
  Scenario: Filter submissions by marking workflow status
    Given I am on the "Test assignment" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Use marking workflow" to "Yes"
    And I press "Save and display"
    And I change window size to "large"
    # Change the marking workflow state for Student 2 and Student 3.
    And I go to "Student 2" "Test assignment" activity advanced grading page
    And I set the field "Marking workflow state" to "In marking"
    And I press "Save changes"
    And I go to "Student 3" "Test assignment" activity advanced grading page
    And I set the field "Marking workflow state" to "Marking completed"
    And I press "Save changes"
    And I follow "View all submissions"
    # Ensure all students are displayed in the submissions table before applying the marking workflow filter.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |
      | Student 3 |
    # Ensure the badge indicating the number of applied filters is absent before applying any marking workflow filter.
    And ".badge" "css_element" should not exist in the ".extrafilters .dropdown-toggle" "css_element"
    # Filter by 'In marking' marking workflow status.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marking state" in the ".extrafilters .dropdown-menu" "css_element" to "In marking"
    When I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 2 is now displayed in the submissions table.
    Then the following should exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 3 |
    # Ensure the badge indicating the number of applied filters is present.
    And ".badge" "css_element" should exist in the ".extrafilters .dropdown-toggle" "css_element"
    And I should see "+1" in the ".extrafilters .badge" "css_element"
    # Ensure the filter remains applied when navigating away from and returning to the assignment submissions page.
    And I am on the "Test assignment" Activity page
    And I navigate to "Submissions" in current page administration
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 3 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Marking state" matches value "In marking"
    # Ensure the filter is not applied unless the 'Apply' button is pressed.
    And I set the field "Marking state" in the ".extrafilters .dropdown-menu" "css_element" to "Marking completed"
    And I click on "Close" "link" in the ".extrafilters .dropdown-menu" "css_element"
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 3 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Marking state" matches value "In marking"
    # Filter by 'Marking completed' marking workflow status.
    And I set the field "Marking state" in the ".extrafilters .dropdown-menu" "css_element" to "Marking completed"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 3 is now displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 3 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |

  @javascript
  Scenario: The applied marking workflow filter can be reset using the 'Clear all' option.
    Given I am on the "Test assignment" "assign activity editing" page logged in as teacher1
    And I expand all fieldsets
    And I set the field "Use marking workflow" to "Yes"
    And I press "Save and display"
    And I change window size to "large"
    # Change the marking workflow state for Student 2.
    And I go to "Student 2" "Test assignment" activity advanced grading page
    And I set the field "Marking workflow state" to "In marking"
    And I press "Save changes"
    And I follow "View all submissions"
    # Ensure the 'Clear all' option is not available until the marking workflow filter has been applied.
    And "Clear all" "link" should not exist in the ".tertiary-navigation" "css_element"
    # Filter by 'In marking' marking workflow status.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I set the field "Marking state" in the ".extrafilters .dropdown-menu" "css_element" to "In marking"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 2 is now displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 3 |
    # Ensure the 'Clear all' option is now available.
    And "Clear all" "link" should exist in the ".tertiary-navigation" "css_element"
    # Ensure the marking workflow filter is reset when the 'Clear All' option is triggered.
    When I click on "Clear all" "link" in the ".tertiary-navigation" "css_element"
    Then the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |
      | Student 3 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Marking state" matches value "No filter"
