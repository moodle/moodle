@mod @mod_qbassign
Feature: Switch role does not cause an error message in qbassignsubmission_comments

  Scenario: I switch role to student and an error doesn't occur
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "course enrolments" exist:
      | course | user     | role           |
      | C1     | teacher1 | editingteacher |
    And the following "activities" exist:
      | activity | course | idnumber | name                | intro                        | teamsubmission |
      | qbassign   | C1     | a1       | Test qbassignment one | This is the description text | 1              |
    And the following "activity" exists:
      | activity         | qbassign                       |
      | idnumber         | ass1                         |
      | course           | C1                           |
      | name             | Test qbassignment              |
      | intro            | This is the description text |
      | teamsubmission   | 1                            |
      | submissiondrafts | 0                            |
    And I am on the "C1" Course page logged in as teacher1
    When I follow "Switch role to..." in the user menu
    And I press "Student"
    And I follow "Test qbassignment"
    Then I should see "This is the description text"
