@tool @tool_capability
Feature: Show capabilities for multiple contexts
  In order to check roles capabilities
  As an admin
  I need to be able to see capability overrides on several contexts

  Background:
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
      | Course 2 | C2        | 0        |
    And the following "permission overrides" exist:
      | capability                    | permission | role       | contextlevel | reference |
      | enrol/category:config         | Allow      | student    | Course       | C1        |
      | enrol/cohort:unenrol          | Allow      | student    | Course       | C2        |
    And I log in as "admin"
    And I navigate to "Users > Permissions > Capability overview" in site administration

  Scenario: Show capabilities table with one capability with overrides
    When I set the following fields to these values:
      | Capability: | enrol/category:config |
      | Roles:      | Student               |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with one capability without overrides
    When I set the following fields to these values:
      | Capability: | enrol/cohort:config |
      | Roles:      | Student               |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should not see "Permissions in Category: Category 1"
    And I should not see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with two capabilities, 1st without overrides and 2nd with
    When I set the following fields to these values:
      | Capability: | enrol/category:synchronised, enrol/category:config |
      | Roles:      | Student                                            |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with two capabilities, 1st with overrides and 2nd without
    When I set the following fields to these values:
      | Capability: | enrol/category:config, enrol/cohort:config |
      | Roles:      | Student                                    |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with two capabilities, none with overrides
    When I set the following fields to these values:
      | Capability: | enrol/category:synchronised, enrol/cohort:config |
      | Roles:      | Student                                          |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should not see "Permissions in Category: Category 1"
    And I should not see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with capability with override and no role selected
    When I set the following fields to these values:
      | Capability: | enrol/category:config |
      | Roles:      |                       |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with capability without override and no role selected
    When I set the following fields to these values:
      | Capability: | enrol/cohort:config |
      | Roles:      |                     |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should not see "Permissions in Category: Category 1"
    And I should not see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with capability with two overrides on different contexts
    When I set the following fields to these values:
      | Capability: | enrol/category:config, enrol/cohort:unenrol |
      | Roles:      |                                           |
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "Permissions in Course: Course 1"
    And I should see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with capability with override and only diff
    When I set the following fields to these values:
      | Capability: | enrol/category:config |
      | Roles:      | Student, Teacher      |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "There are no differences to show between selected roles in this context"
    And I should see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with capability without override and only diff and same capability
    When I set the following fields to these values:
      | Capability: | enrol/category:synchronised |
      | Roles:      | Student, Teacher            |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "There are no differences to show between selected roles in this context"
    And I should not see "Permissions in Category: Category 1"
    And I should not see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"

  Scenario: Show capabilities table with two capabilities only one override and only diff checked
    When I set the following fields to these values:
      | Capability: | enrol/category:config, enrol/cohort:config |
      | Roles:      | Student, Teacher                           |
    And I set the field "Show differences only" to "1"
    And I click on "Get the overview" "button"
    Then I should see "Permissions in System"
    And I should see "Permissions in Category: Category 1"
    And I should see "There are no differences to show between selected roles in this context"
    And I should see "Permissions in Course: Course 1"
    And I should not see "Permissions in Course: Course 2"
