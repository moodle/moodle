@availability @availability_grouping
Feature: availability_grouping
  In order to control student access to activities
  As a teacher
  I need to set grouping conditions which prevent student access

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
    And the following "groups" exist:
      | name | course | idnumber |
      | G1   | C1     | GI1      |
    And the following "group members" exist:
      | user     | group |
      | student1 | GI1   |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Start to add a Page. If there aren't any groupings, there's no Grouping option.
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Grouping" "button" should not exist in the "Add restriction..." "dialogue"
    And I click on "Cancel" "button" in the "Add restriction..." "dialogue"

    # Back to course page but add groups.
    # This step used to be 'And I follow "C1"', but Chrome thinks the breadcrumb
    # is not clickable, so we'll go via the home page instead.
    And I am on site homepage
    And I follow "Course 1"
    And the following "groupings" exist:
      | name | course | idnumber |
      | GX1  | C1     | GXI1     |
      | GX2  | C1     | GXI2     |
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    Then "Grouping" "button" should exist in the "Add restriction..." "dialogue"

    # Page P1 grouping GX1.
    Given I click on "Grouping" "button"
    And I set the field "Grouping" to "GX1"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"

    # Page P2 with grouping GX2.
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grouping" "button"
    And I set the field "Grouping" to "GX2"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I follow "Course 1"

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"

    # Add group to grouping and log out/in again.
    And I log out
    And the following "grouping groups" exist:
      | grouping | group  |
      | GXI1     | GI1    |
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    # P1 should show but not B2.
    Then I should see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
