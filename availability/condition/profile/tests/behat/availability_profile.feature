@availability @availability_profile
Feature: availability_profile
  In order to control student access to activities
  As a teacher
  I need to set profile conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username | email         |
      | teacher1 | t@example.org |
      | student1 | s@example.org |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And I log in as "admin"
    And I set the following administration settings values:
      | Enable conditional access  | 1 |
    And I log out

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on

    # Add
    And I add a "Page" to section "1"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "s@example.org"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Add
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "User profile" "button"
    And I set the field "User profile field" to "Email address"
    And I set the field "Value to compare against" to "q@example.org"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I follow "Course 1"

    # I see P1 but not P2.
    Then I should see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
