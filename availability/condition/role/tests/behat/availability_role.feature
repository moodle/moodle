@availability @availability_role
Feature: availability_role
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
    And the following config values are set as admin:
      | enableavailability  | 1 |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Start to add a Page that can be accessed by teachers only.
    And I add a "Page" to section "1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Teacher"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P1 |
      | Description  | x  |
      | Page content | x  |
    And I click on "Save and return to course" "button"
    Then I should see "P1" in the "region-main" "region"

    And I add a "Page" to section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Role" "button"
    And I set the field "Role" to "Manager"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x2  |
      | Page content | x2  |
    And I click on "Save and return to course" "button"
    Then I should see "P2" in the "region-main" "region"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I follow "Course 1"

    # No pages should appear yet.
    Then I should not see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"

    # Log back in as teacher.
    When I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on
    And I open "P1" actions menu
    And I click on "Edit settings" "link" in the "P1" activity
    Then I expand all fieldsets
    And I click on ".availability-item .availability-delete" "css_element"
    And I click on "Save and return to course" "button"

    # Log back in as student.
    When I log out
    And I log in as "student1"
    And I follow "Course 1"
    Then I should see "P1" in the "region-main" "region"
    And I should not see "P2" in the "region-main" "region"
