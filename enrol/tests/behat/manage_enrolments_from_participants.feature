@core_enrol
Feature: Manage enrollments from participants page
  In order to manage course participants
  As a teacher
  In need to get to the enrolment page from the course participants page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
      | teacher1 | teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | student1 | C1 | student |
      | student2 | C1 | student |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I navigate to "Participants" node in "Current course > C1"

  Scenario: Check the participants link when "All partipants" selected
    Given I select "All participants" from the "roleid" singleselect
    When I follow "Edit"
    Then I should see "Enrolled users" in the "h2" "css_element"
    And the field "Role" matches value "All"

  Scenario: Check the participants link when "Student" selected
    Given I select "Student" from the "roleid" singleselect
    When I follow "Edit"
    Then I should see "Enrolled users" in the "h2" "css_element"
    And the field "Role" matches value "Student"
