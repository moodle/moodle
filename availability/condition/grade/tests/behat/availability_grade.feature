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
    # Add an assignment.
    And the following "activities" exist:
      | activity | course | name | assignsubmission_onlinetext_enabled |
      | assign   | C1     | A1   | 1                                   |
      | page     | C1     | P1   |                                     |
      | page     | C1     | P2   |                                     |
      | page     | C1     | P3   |                                     |
      | page     | C1     | P4   |                                     |

  @javascript
  Scenario: Test condition
    Given I am on the "P2" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Grade" to "A1"
    And I press "Save and return to course"

    # Add a Page with a grade condition for 50%.
    And I am on the "P3" "page activity editing" page
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
    And I am on the "P3" "page activity editing" page
    And I expand all fieldsets
    And I click on "max" "checkbox" in the ".availability-item" "css_element"
    And I press "Save and return to course"
    And I am on the "P3" "page activity editing" page
    And the field "Maximum grade percentage (exclusive)" matches value ""
    And I am on "Course 1" course homepage

    # Add a Page with a grade condition for 10%.
    And I am on the "P4" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Grade" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Grade" to "A1"
    And I click on "min" "checkbox" in the ".availability-item" "css_element"
    And I set the field "Minimum grade percentage (inclusive)" to "10"
    And I press "Save and return to course"

    # Log in as student without a grade yet.
    When I am on the "A1" "assign activity" page logged in as student1

    # Do the assignment.
    And I click on "Add submission" "button"
    And I set the field "Online text" to "Q"
    And I click on "Save changes" "button"
    And I am on "Course 1" course homepage

    # None of the pages should appear (check assignment though).
    Then I should not see "P2" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"
    And I should not see "P4" in the "region-main" "region"
    And I should see "A1" in the "region-main" "region"

    # Log back in as teacher.
    When I am on the "A1" "assign activity" page logged in as teacher1

    # Give the assignment 40%.
    And I navigate to "View all submissions" in current page administration
    # Pick the grade link in the row that has s@example.com in it.
    And I click on "Grade" "link" in the "s@example.com" "table_row"
    And I set the field "Grade out of 100" to "40"
    And I click on "Save changes" "button"
    And I press "OK"
    And I click on "Edit settings" "link"
    And I log out

    # Log back in as student.
    And I am on the "Course 1" course page logged in as student1

    # Check pages are visible.
    Then I should see "P2" in the "region-main" "region"
    And I should see "P4" in the "region-main" "region"
    And I should not see "P3" in the "region-main" "region"
