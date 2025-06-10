@availability @availability_date
Feature: availability_date
  In order to control student access to activities
  As a teacher
  I need to set date conditions which prevent student access

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
    And the following "activities" exist:
      | activity | course | name   |
      | page     | C1     | Page 1 |
      | page     | C1     | Page 2 |

  @javascript
  Scenario: Test condition
    # Add a Page with a date condition that does match (from the past).
    Given I am on the "Page 1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "year" to "2013"
    And I press "Save and return to course"

    # Add a Page with a date condition that doesn't match (until the past).
    And I am on the "Page 2" "page activity editing" page
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I click on ".availability-item .availability-eye img" "css_element"
    And I set the field "Direction" to "until"
    And I set the field "year" to "2013"
    And I press "Save and return to course"

    # Log back in as student.
    When I am on the "Course 1" "course" page logged in as "student1"

    # Page 1 should appear, but page 2 does not.
    Then I should see "Page 1" in the "region-main" "region"
    And I should not see "Page 2" in the "region-main" "region"
