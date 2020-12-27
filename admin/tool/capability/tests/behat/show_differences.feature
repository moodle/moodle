@tool @tool_capability
Feature: show only differences between roles for selected capabilities
  In order to check roles capabilities
  As an admin
  I need to be able to filter capabilities report viewing only role differences

  Background:
    Given the following "roles" exist:
      | shortname     | name      | archetype |
      | studenteq     | Studenteq | student   |
      | studentdf     | Studentdf | student   |
    And the following "permission overrides" exist:
      | capability                    | permission | role        | contextlevel | reference |
      | moodle/course:changefullname  | Allow      | studentdf   | System       |           |
      | moodle/course:changeshortname | Prohibit   | studentdf   | System       |           |
    And I log in as "admin"
    And I navigate to "Users > Permissions > Capability overview" in site administration

  Scenario: Compare identical roles
    When I set the following fields to these values:
      | Capability: | moodle/course:changefullname, moodle/course:changeshortname, moodle/course:changeidnumber, moodle/course:changesummary |
      | Roles:      | Student, Studenteq                                                                                                     |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "There are no differences to show between selected roles in this context"

  Scenario: Compare different roles
    When I set the following fields to these values:
      | Capability: | moodle/course:changefullname, moodle/course:changeshortname, moodle/course:changeidnumber, moodle/course:changesummary |
      | Roles:      | Student, Studentdf                                                                                                     |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should not see "There are no differences to show between selected roles in this context"
    And I should see "moodle/course:changefullname" in the "comparisontable" "table"
    And I should see "moodle/course:changeshortname" in the "comparisontable" "table"
    And I should not see "moodle/course:changesummary" in the "comparisontable" "table"

  Scenario: Compare different roles but comparing capabilities that are equals on both
    When I set the following fields to these values:
      | Capability: | moodle/course:changeidnumber, moodle/course:changesummary |
      | Roles:      | Student, Studentdf                                        |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "There are no differences to show between selected roles in this context"

  Scenario: Compare all roles without selecting specific role
    When I set the following fields to these values:
      | Capability: | moodle/course:changefullname, moodle/site:config |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should not see "moodle/site:config" in the "comparisontable" "table"
    And I should see "moodle/course:changefullname" in the "comparisontable" "table"

  Scenario: Compare all roles without selecting specific role on not defined capability
    When I set the following fields to these values:
      | Capability: | moodle/site:config |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "There are no differences to show between selected roles in this context"

  Scenario: Comparing only one role
    When I set the following fields to these values:
      | Capability: | moodle/course:changefullname, moodle/course:changeshortname, moodle/course:changeidnumber, moodle/course:changesummary |
      | Roles:      | Student                                                                                                                |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "There are no differences to show between selected roles in this context"
