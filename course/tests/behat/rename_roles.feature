@core @core_course
Feature: Rename roles within a course
  In order to set course roles names according to their responsabilities
  As a teacher
  I need to edit the course role names

  @javascript
  Scenario: Rename roles within a course
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | teacher2 | Teacher | 2 | teacher2@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    When I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Your word for 'Non-editing teacher' | Tutor |
      | Your word for 'Student' | Learner |
    And I press "Save and display"
    And I follow "Switch role to..." in the user menu
    Then "Tutor" "button" should exist
    And "Learner" "button" should exist
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I should see "Tutor (Non-editing teacher)" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Learner (Student)" in the ".form-autocomplete-suggestions" "css_element"
    And I click on "Student 1's role assignments" "link"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Student 1" "table_row"
    And "Tutor (Non-editing teacher)" "autocomplete_suggestions" should exist
    And I click on "Cancel" "link"
    And I press "Enrol users"
    And the "Assign role" select box should contain "Learner (Student)"
    And I click on "Cancel" "button" in the "Enrol users" "dialogue"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | Your word for 'Non-editing teacher' | |
      | Your word for 'Student' | |
    And I press "Save and display"
    And I follow "Switch role to..." in the user menu
    And I should see "Teacher"
    And "Student" "button" should exist
    And "Learner" "button" should not exist
    And I navigate to course participants
    And I set the field "type" in the "Filter 1" "fieldset" to "Roles"
    And I click on ".form-autocomplete-downarrow" "css_element" in the "Filter 1" "fieldset"
    And I should see "Non-editing teacher" in the ".form-autocomplete-suggestions" "css_element"
    And I should see "Student" in the ".form-autocomplete-suggestions" "css_element"
