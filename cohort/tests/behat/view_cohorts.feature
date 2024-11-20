@core @core_cohort
Feature: View cohort list
  In order to operate with cohorts
  As an admin or manager
  I need to be able to view the list of cohorts in the system

  Background:
    Given the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
      | Cat 2 | 0        | CAT2     |
      | Cat 3 | CAT1     | CAT3     |
    And the following "cohorts" exist:
      | name          | idnumber |
      | System cohort | CH0      |
    And the following "cohorts" exist:
      | name                 | idnumber | contextlevel | reference |
      | Cohort in category 1 | CH1      | Category     | CAT1      |
      | Cohort in category 2 | CH2      | Category     | CAT2      |
      | Cohort in category 3 | CH3      | Category     | CAT3      |
    Given the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | First     | User     | first@example.com  |
      | user2    | Second    | User     | second@example.com |
    And the following "role assigns" exist:
      | user  | role    | contextlevel | reference |
      | user1 | manager | System       |           |
      | user2 | manager | Category     | CAT1      |

  Scenario: Admin can see system cohorts and all cohorts
    When I log in as "admin"
    And I navigate to "Users > Accounts >Cohorts" in site administration
    Then I should see "System cohort"
    And I should not see "Cohort in category"
    And I follow "All cohorts"
    And I should see "System cohort"
    And I should see "Cohort in category 1"
    And I should see "Cohort in category 2"
    And I should see "Cohort in category 3"
    And I log out

  Scenario: Manager can see system cohorts and all cohorts
    When I log in as "user1"
    And I navigate to "Users > Accounts >Cohorts" in site administration
    Then I should see "System cohort"
    And I should not see "Cohort in category"
    And I follow "All cohorts"
    And I should see "System cohort"
    And I should see "Cohort in category 1"
    And I should see "Cohort in category 2"
    And I should see "Cohort in category 3"
    And I log out

  Scenario: Manager in category can see cohorts in the category
    When I log in as "user2"
    And I am on course index
    And I follow "Cat 1"
    And I follow "Cohorts"
    And I should not see "All cohorts"
    And I should not see "System cohort"
    And I should see "Cohort in category 1"
    And I should not see "Cohort in category 2"
    And I should not see "Cohort in category 3"
    And I log out

  @javascript
  Scenario: Cohorts list can be filtered
    Given the following "custom field categories" exist:
      | name   | component   | area   | itemid |
      | Newcat | core_cohort | cohort | 0      |
    And the following "custom fields" exist:
      | name            | category | type     | shortname | description | configdata |
      | Field checkbox  | Newcat   | checkbox | checkbox  |             |            |
    And the following "cohorts" exist:
      | name           | idnumber | contextlevel | reference | customfield_checkbox |
      | Cohort with CF | CH4      | Category     | CAT1      | 1                    |
    When I log in as "admin"
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I follow "All cohorts"
    And I click on "Filters" "button"
    And I set the following fields in the "Name" "core_reportbuilder > Filter" to these values:
      | Name operator | Contains    |
      | Name value    | category 1  |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then the following should exist in the "Cohorts" table:
      | Category  | Name                  |
      | Cat 1     | Cohort in category 1  |
    And the following should not exist in the "Cohorts" table:
      | Category  | Name                  |
      | Cat 2     | Cohort in category 2  |
      | Cat 3     | Cohort in category 3  |
      | System    | System cohort         |
    And I click on "Reset all" "button" in the "[data-region='report-filters']" "css_element"
    And I set the following fields in the "Field checkbox" "core_reportbuilder > Filter" to these values:
      | Field checkbox operator | Yes    |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And the following should exist in the "Cohorts" table:
      | Category  | Name           |
      | Cat 1     | Cohort with CF |
    And the following should not exist in the "Cohorts" table:
      | Category  | Name                  |
      | Cat 1     | Cohort in category 1  |
      | Cat 2     | Cohort in category 2  |
      | Cat 3     | Cohort in category 3  |
      | System    | System cohort         |
