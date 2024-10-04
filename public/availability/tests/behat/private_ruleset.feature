@core @core_availability @javascript
Feature: Private rule sets
  In order to prevent private data being leaked in restriction sets
  As a teacher
  I want to have restrictions hidden when a private condition is selected

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | numsections |
      | Course 1 | C1        | topics | 1                | 3           |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "groups" exist:
      | name    | course | idnumber | visibility |
      | Group A | C1     | GA       | 0          |
      | Group B | C1     | GB       | 1          |
    And I log in as "teacher1"
    And I add a page activity to course "Course 1" section "1"
    And I expand all fieldsets

  Scenario: Add restriction with visible condition (must match), display option should be active
    When I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then ".availability-children .availability-eye" "css_element" should be visible
    And ".availability-children .availability-eye-disabled" "css_element" should not be visible
    And the "title" attribute of ".availability-eye" "css_element" should contain "Click to hide"

  Scenario: Add restriction with private condition (must match), display option should be disabled
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    Then ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should be visible
    And the "title" attribute of ".availability-eye-disabled" "css_element" should contain "Cannot be changed as ruleset includes a rule containing private data."

  Scenario: Add restrictions with a visible and a private condition (must match all), display option should be disabled
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    When I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should be visible

  Scenario: Remove private condition (must match), display option should be active
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should be visible
    # Should pick the first one (Group B)
    When I click on ".availability-item .availability-delete img" "css_element"
    Then ".availability-children .availability-eye" "css_element" should be visible
    And ".availability-children .availability-eye-disabled" "css_element" should not be visible

  Scenario: Set a private condition to a visible value (must match), display option should be active
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    Then ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should be visible
    # Should pick the first one (Group B)
    When I set the field "Group" to "Group A"
    Then ".availability-children .availability-eye" "css_element" should be visible
    And ".availability-children .availability-eye-disabled" "css_element" should not be visible

  Scenario: Add restrictions with a visible and a private condition (must match any), display option should be disabled
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "Required restrictions" to "any"
    # "Hidden" icon should be shown in header.
    And ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should not be visible
    And ".availability-header .availability-eye" "css_element" should not be visible
    And ".availability-header .availability-eye-disabled" "css_element" should be visible

  Scenario: Add restriction with private condition (must not match), display option should be disabled
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I set the field "Restriction type" to "must not"
    # "Hidden" icon should be shown in header.
    And ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should not be visible
    And ".availability-header .availability-eye" "css_element" should not be visible
    And ".availability-header .availability-eye-disabled" "css_element" should be visible

  Scenario: Add restrictions with a visible and a private condition (must not match all), display option should be disabled
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "Restriction type" to "must not"
    # "Hidden" icon should be shown in header.
    And ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should not be visible
    And ".availability-header .availability-eye" "css_element" should not be visible
    And ".availability-header .availability-eye-disabled" "css_element" should be visible

  Scenario: Add restrictions with a visible and a private condition (must not match any), display option should be disabled
    When I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I set the field "Restriction type" to "must not"
    And I set the field "Required restrictions" to "any"
    # "Hidden" icon should be shown in conditions, but not in the header.
    And ".availability-header .availability-eye" "css_element" should not be visible
    And ".availability-header .availability-eye-disabled" "css_element" should not be visible
    And ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should be visible

  Scenario: Private conditions should not show to unprivileged users
    Given I set the field "Name" to "Test page"
    And I set the field "Page content" to "test"
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I press "Save and return to course"
    And I log out
    And I log in as "student1"
    When I am on "Course 1" course homepage
    Then I should not see "Test page"
    And I should not see "Not available unless: You belong to Group B"

  Scenario: Loading a rule set containing private conditions should disable display option
    Given I set the field "Name" to "Test page"
    And I set the field "Page content" to "test"
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "Group B"
    And I press "Save and display"
    When I follow "Settings"
    And I expand all fieldsets
    Then ".availability-children .availability-eye" "css_element" should not be visible
    And ".availability-children .availability-eye-disabled" "css_element" should be visible
