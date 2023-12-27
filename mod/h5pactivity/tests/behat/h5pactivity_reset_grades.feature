@mod @mod_h5pactivity @core_h5p
Feature: Teacher can reset H5P activity grades
  As a teacher,
  I should be able to reset H5P activity grades

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | student3 | Third     | Student  | student3@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
    And the following "activities" exist:
      | activity    | course | name         | grade[modgrade_type] | grade[modgrade_point] |
      | h5pactivity | C1     | H5P Activity | point                | 10                    |

  @javascript
  Scenario:Teacher can reset H5P activity grades
    Given I am on the "Course 1" course page logged in as teacher1
    And I navigate to "View > Grader report" in the course gradebook
    And I turn editing mode on
    And I give the grade "7" to the user "First Student" for the grade item "H5P Activity"
    And I give the grade "5" to the user "Second Student" for the grade item "H5P Activity"
    And I give the grade "0" to the user "Third Student" for the grade item "H5P Activity"
    And I press "Save changes"
    # Confirm that grade was sucessfully saved
    And I turn editing mode off
    And I should see "7.00" in the "First Student" "table_row"
    And I should see "5.00" in the "Second Student" "table_row"
    And I should see "0.00" in the "Third Student" "table_row"
    When I am on the "Course 1" "reset" page
    And I expand all fieldsets
    # Check `Delete all grades` in course reset page to reset grades
    And I click on "Delete all grades" "checkbox"
    And I press "Reset"
    Then I should see "OK" in the "Gradebook" "table_row"
    And I press "Continue"
    # Confirm that previously saved grades are gone
    And I navigate to "View > Grader report" in the course gradebook
    And I should not see "7.00" in the "First Student" "table_row"
    And I should not see "5.00" in the "Second Student" "table_row"
    And I should not see "0.00" in the "Third Student" "table_row"
