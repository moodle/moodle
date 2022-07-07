@availability @availability_group
Feature: availability_group
  In order to control student access to activities
  As a teacher
  I need to set group conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Start to add a Page. If there aren't any groups, there's no Group option.
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Group" "button" should not exist in the "Add restriction..." "dialogue"
    And I click on "Cancel" "button" in the "Add restriction..." "dialogue"

    # Back to course page but add groups.
    Given the following "groups" exist:
      | name     | course | idnumber |
      | G1       | C1     | GI1      |
      | G2       | C1     | GI2      |
    # This step used to be 'And I follow "C1"', but Chrome thinks the breadcrumb
    # is not clickable, so we'll go via the home page instead.
    And I am on "Course 1" course homepage
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Group" "button" should exist in the "Add restriction..." "dialogue"

    # Page P1 any group.
    Given I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "(Any group)"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"

    # Page P2 with group G1.
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G1"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Page P3 with group G2
    And I add a "Page" to section "3"
    And I set the following fields to these values:
      | Name         | P3 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Group" "button" in the "Add restriction..." "dialogue"
    And I set the field "Group" to "G2"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"

    # Add to groups and log out/in again.
    Given the following "group members" exist:
      | user     | group |
      | student1 | GI1   |
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage

    # P1 (any groups) and P2 should show but not P3.
    Then I should see "P1" in the "region-main" "region"
    And I should see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"
