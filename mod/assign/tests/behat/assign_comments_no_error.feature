@mod @mod_assign
Feature: Switch role does not cause an error message in assignsubmission_comments

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
      | assign   | C1     | a1       | Test assignment one | This is the description text | 1              |
    And the following "activity" exists:
      | activity         | assign                       |
      | idnumber         | ass1                         |
      | course           | C1                           |
      | name             | Test assignment              |
      | intro            | This is the description text |
      | teamsubmission   | 1                            |
      | submissiondrafts | 0                            |
    And I am on the "C1" Course page logged in as teacher1
    When I follow "Switch role to..." in the user menu
    And I press "Student"
    And I follow "Test assignment"
    Then I should see "This is the description text"
