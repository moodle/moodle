@core @core_grades
Feature: We can view the logs for any changes to grade letters.
  In order to view changes the letter boundary of a course
  As an administrator
  I need to add make changes and then view the logs.

  Scenario: I override the letter boundaries and check the logs.
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I navigate to "Setup > Course grade settings" in the course gradebook
    And I set the following fields to these values:
      | Grade display type | Letter |
    And I press "Save changes"
    And I navigate to "Letters" in the course gradebook
    And I follow "Edit grade letters"
    And I set the following fields to these values:
      | id_override | 1 |
      | id_gradeboundary10 | 57 |
    And I press "Save changes"
    And I follow "Edit grade letters"
    And I set the following fields to these values:
      | id_override | 1 |
      | id_gradeboundary10 | 50 |
    And I press "Save changes"
    And I follow "Edit grade letters"
    And I set the following fields to these values:
      | id_override | 1 |
      | id_gradeletter11 | |
      | id_gradeboundary11 | |
    And I press "Save changes"
    When I navigate to "Live logs" node in "Site administration > Reports"
    Then I should see "Grade letter created"
    And I should see "Grade letter updated"
    And I should see "Grade letter deleted"
