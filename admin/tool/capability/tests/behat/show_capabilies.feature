@tool @tool_capability
Feature: show capabilities for selected roles
  In order to check roles capabilities
  As an admin
  I need to be able to customize capabilities report viewing only specific roles and capabilities

  Background:
    Given the following "roles" exist:
      | shortname     | name      | archetype |
      | studenteq     | Studenteq | student   |
      | studentdf     | Studentdf | student   |
    And the following "permission overrides" exist:
      | capability                    | permission | role        | contextlevel | reference |
      | moodle/course:changefullname  | Allow      | studentdf   | System       |           |
      | moodle/course:changeshortname | Prohibit   | studentdf   | System       |           |
      | moodle/course:changeidnumber  | Prevent    | studentdf   | System       |           |
    And I log in as "admin"
    And I navigate to "Users > Permissions > Capability overview" in site administration

  Scenario: visualize capabilities table with a limited number of capabilities
    When I set the following fields to these values:
      | Capability: | moodle/course:changefullname, moodle/course:changeshortname |
      | Roles:      | Studentdf                                                                                 |
    And I click on "Get the overview" "button"
    Then I should see "moodle/course:changefullname" in the "comparisontable" "table"
    And I should see "moodle/course:changeshortname" in the "comparisontable" "table"
    And I should not see "moodle/course:changecategory" in the "comparisontable" "table"

  Scenario: visualize an allow capability
    When I set the following fields to these values:
      | Capability: | moodle/course:changefullname |
      | Roles:      | Studentdf                                                                                                     |
    And I click on "Get the overview" "button"
    Then I should see "Allow" in the "comparisontable" "table"
    And I should not see "Prevent" in the "comparisontable" "table"
    And I should not see "Prohibit" in the "comparisontable" "table"
    And I should not see "Not set" in the "comparisontable" "table"

  Scenario: visualize a prohibit capability
    When I set the following fields to these values:
      | Capability: | moodle/course:changeshortname |
      | Roles:      | Studentdf                                                                                                     |
    And I click on "Get the overview" "button"
    Then I should not see "Allow" in the "comparisontable" "table"
    And I should not see "Prevent" in the "comparisontable" "table"
    And I should see "Prohibit" in the "comparisontable" "table"
    And I should not see "Not set" in the "comparisontable" "table"

  Scenario: visualize a not set capability
    When I set the following fields to these values:
      | Capability: | moodle/course:changecategory |
      | Roles:      | Studentdf                    |
    And I click on "Get the overview" "button"
    Then I should not see "Allow" in the "comparisontable" "table"
    And I should not see "Prevent" in the "comparisontable" "table"
    And I should not see "Prohibit" in the "comparisontable" "table"
    And I should see "Not set" in the "comparisontable" "table"

  Scenario: visualize more than one role
    When I set the following fields to these values:
      | Capability: | moodle/course:changecategory |
      | Roles:      | Student, Studentdf           |
    And I click on "Get the overview" "button"
    Then I should see "Student" in the "comparisontable" "table"
    And I should see "Studentdf" in the "comparisontable" "table"
    And I should not see "Teacher" in the "comparisontable" "table"

  Scenario: visualize all roles without selecting any role
    When I set the following fields to these values:
      | Capability: | moodle/course:changecategory |
    And I click on "Get the overview" "button"
    Then I should see "Student" in the "comparisontable" "table"
    And I should see "Studentdf" in the "comparisontable" "table"
    And I should see "Teacher" in the "comparisontable" "table"

  Scenario: visualize all roles by selecting All option
    When I set the following fields to these values:
      | Capability: | moodle/course:changecategory |
      | Roles:      | All                          |
    And I click on "Get the overview" "button"
    Then I should see "Student" in the "comparisontable" "table"
    And I should see "Studentdf" in the "comparisontable" "table"
    And I should see "Teacher" in the "comparisontable" "table"

  @javascript
  Scenario: filter capability list using javascript
    Given I should see "moodle/site:config" in the "Capability" "field"
    And I should see "moodle/course:change" in the "Capability" "field"
    And I set the field "Search" in the "#capability-overview-form" "css_element" to "moodle/course:change"
    Then I should see "moodle/course:change" in the "Capability" "field"
    And I should not see "moodle/site:config" in the "Capability" "field"

  @javascript
  Scenario: selecting capabilities using filters
    Given I should see "moodle/course:change" in the "Capability" "field"
    And I set the field "Search" in the "#capability-overview-form" "css_element" to "moodle/course:change"
    And I wait "1" seconds
    When I set the following fields to these values:
      | Capability: | moodle/course:changecategory |
      | Roles:      | Student                      |
    And I click on "Get the overview" "button"
    Then I should see "moodle/course:changecategory" in the "comparisontable" "table"
    And the field "Capability:" matches value "moodle/course:changecategory"
