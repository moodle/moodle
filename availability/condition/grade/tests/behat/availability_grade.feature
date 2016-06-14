@availability @availability_grade
Feature: availability_grade
  In order to control student access to activities
  As a teacher
  I need to set date conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion |
      | Course 1 | C1        | topics | 1                |
    And the following "users" exist:
      | username | email         |
      | teacher1 | t@example.com |
      | student1 | s@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |

  @javascript
  Scenario: Test condition
    # Basic setup.
    Given I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"
    And I turn editing mode on

    # Add an assignment.
    And I add a "Assignment" to section "1" and I fill the form with:
      | Assignment name     | A1 |
      | Description         | x  |
      | Online text         | 1  |

    # Add a Page with a grade condition for 'any grade'.
    And I add a "Page" to section "2"
    And I set the following fields to these values:
      | Name         | P2 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Grade" to "A1"
    And I press "Save and return to course"

    # Add a Page with a grade condition for 50%.
    And I add a "Page" to section "3"
    And I set the following fields to these values:
      | Name         | P3 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Grade" to "A1"
    And I click on "min" "checkbox" in the ".availability-item" "css_element"
    And I set the field "Minimum grade percentage (inclusive)" to "50"
    And I click on "max" "checkbox" in the ".availability-item" "css_element"
    And I set the field "Maximum grade percentage (exclusive)" to "80"
    And I press "Save and return to course"

    # Check if disabling a part of the restriction is get saved.
    And I open "P3" actions menu
    And I click on "Edit settings" "link" in the "P3" activity
    And I expand all fieldsets
    And I click on "max" "checkbox" in the ".availability-item" "css_element"
    And I press "Save and return to course"
    And I open "P3" actions menu
    And I click on "Edit settings" "link" in the "P3" activity
    And I expand all fieldsets
    And the field "Maximum grade percentage (exclusive)" matches value ""
    And I follow "Course 1"

    # Add a Page with a grade condition for 10%.
    And I add a "Page" to section "4"
    And I set the following fields to these values:
      | Name         | P4 |
      | Description  | x  |
      | Page content | x  |
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Grade" to "A1"
    And I click on "min" "checkbox" in the ".availability-item" "css_element"
    And I set the field "Minimum grade percentage (inclusive)" to "10"
    And I press "Save and return to course"

    # Log in as student without a grade yet.
    When I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    # Do the assignment.
    And I follow "A1"
    And I click on "Add submission" "button"
    And I set the field "Online text" to "Q"
    And I click on "Save changes" "button"
    And I follow "C1"

    # None of the pages should appear (check assignment though).
    Then I should not see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"
    And I should not see "P4" in the "region-main" "region"
    And I should see "A1" in the "region-main" "region"

    # Log back in as teacher.
    When I log out
    And I log in as "teacher1"
    And I am on site homepage
    And I follow "Course 1"

    # Give the assignment 40%.
    And I follow "A1"
    And I follow "View all submissions"
    # Pick the grade link in the row that has s@example.com in it.
    And I click on "Grade" "link" in the "s@example.com" "table_row"
    And I set the field "Grade out of 100" to "40"
    And I click on "Save changes" "button"
    And I press "Ok"
    And I click on "Edit settings" "link"

    # Log back in as student.
    And I log out
    And I log in as "student1"
    And I am on site homepage
    And I follow "Course 1"

    # Check pages are visible.
    Then I should see "P2" in the "region-main" "region"
    And I should see "P4" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"
