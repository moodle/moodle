@mod @mod_assign
Feature: In an assignment, teachers can include or exclude submissions from suspended participants
  In order to manage submissions more easily
  As a teacher
  I need to be able to include or exclude submissions from suspended participants.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
      | student2 | Student   | 2        | student2@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           | status |
      | teacher1 | C1     | editingteacher | 0      |
      | student1 | C1     | student        | 0      |
      | student2 | C1     | student        | 1      |
    And the following "activity" exists:
      | activity | assign               |
      | course   | C1                   |
      | name     | Test assignment name |

  @javascript
  Scenario: The suspended participants filter is available only when the teacher has the capability to view suspended participants
    Given the following "permission overrides" exist:
      | capability                       | permission | role           | contextlevel | reference |
      | moodle/course:viewsuspendedusers | Prevent    | editingteacher | Course       | C1        |
    And I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    When I change window size to "large"
    # Ensure the Advanced filters component is not available. This validates that the Suspended participants filer is
    # not available as well since it's part of this component.
    Then "Advanced" "button" should not exist in the ".tertiary-navigation" "css_element"
    And the following "permission overrides" exist:
      | capability                       | permission | role           | contextlevel | reference |
      | moodle/course:viewsuspendedusers | Allow      | editingteacher | Course       | C1        |
    And I reload the page
    And "Advanced" "button" should exist in the ".tertiary-navigation" "css_element"
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And "Include suspended participants" "checkbox" should exist in the ".extrafilters .dropdown-menu" "css_element"
    And the field "Include suspended participants" matches value ""

  @javascript
  Scenario: Teacher can include or exclude submissions from suspended participants
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    # Set to include submissions from suspended participants.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    When I click on "Include suspended participants" "checkbox" in the ".extrafilters .dropdown-menu" "css_element"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure that the suspended Student 2 is now also displayed in the submissions table.
    Then the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
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
      | Student 2 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Include suspended participants" matches value "1"
    # Ensure the filter is not applied unless the 'Apply' button is pressed.
    And I click on "Include suspended participants" "checkbox" in the ".extrafilters .dropdown-menu" "css_element"
    And the field "Include suspended participants" matches value ""
    And I click on "Close" "link" in the ".extrafilters .dropdown-menu" "css_element"
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Include suspended participants" matches value "1"
    # Set to exclude submissions from suspended participants.
    And I click on "Include suspended participants" "checkbox" in the ".extrafilters .dropdown-menu" "css_element"
    And the field "Include suspended participants" matches value ""
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure only Student 1 is now displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |

  @javascript
  Scenario: The applied suspended participants filter can be reset using the 'Clear all' option
    Given I am on the "Test assignment name" Activity page logged in as teacher1
    And I navigate to "Submissions" in current page administration
    And I change window size to "large"
    # Ensure the 'Clear all' option is not available until the suspended participants filter has been applied.
    And "Clear all" "link" should not exist in the ".tertiary-navigation" "css_element"
    # Set to include submissions from suspended participants.
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And I click on "Include suspended participants" "checkbox" in the ".extrafilters .dropdown-menu" "css_element"
    And the field "Include suspended participants" matches value "1"
    And I click on "Apply" "button" in the ".extrafilters .dropdown-menu" "css_element"
    # Ensure that the suspended Student 2 is now also displayed in the submissions table.
    And the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
      | Student 2 |
    # Ensure the 'Clear all' option is now available.
    And "Clear all" "link" should exist in the ".tertiary-navigation" "css_element"
    # Ensure the marker filter is reset when the 'Clear All' option is triggered.
    When I click on "Clear all" "link" in the ".tertiary-navigation" "css_element"
    Then the following should exist in the "submissions" table:
      | -2-       |
      | Student 1 |
    And the following should not exist in the "submissions" table:
      | -2-       |
      | Student 2 |
    And I click on "Advanced" "button" in the ".tertiary-navigation" "css_element"
    And the field "Include suspended participants" matches value ""
