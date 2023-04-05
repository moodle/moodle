@core @core_cohort @core_customfield @javascript
Feature: Add and use cohort custom fields
  In order to store an extra information about cohorts
  As an admin
  I need to create cohort customs fields and be able to populate them on cohort creation

  Background:
    Given the following "custom field categories" exist:
      | name              | component   | area   | itemid |
      | Category for test | core_cohort | cohort | 0      |

  Scenario: Create a new cohort custom field and use the field for a new cohort
    When I log in as "admin"
    And I navigate to "Users > Accounts > Cohort custom fields" in site administration
    And I click on "Add a new custom field" "link"
    And I click on "Short text" "link"
    And I set the following fields to these values:
      | Name       | Test field |
      | Short name | testfield  |
    And I click on "Save changes" "button" in the "Adding a new Short text" "dialogue"
    Then the following should exist in the "generaltable" table:
      | Custom field | Short name | Type       |
      | Test field   | testfield  | Short text |
    And I navigate to "Users > Accounts > Cohorts" in site administration
    And I follow "Add new cohort"
    Then I should see "Category for test"
    And I should see "Test field"
    And I set the following fields to these values:
      | Name        | My new cohort             |
      | Context     | System                    |
      | Cohort ID   | mynewcohort               |
      | Description | My new cohort description |
      | Test field  | Custom field text         |
    And I press "Save changes"
    Then the following should exist in the "generaltable" table:
      | Name          | Cohort ID   | Description               |
      | My new cohort | mynewcohort | My new cohort description |
    And I press "Edit" action in the "My new cohort" report row
    And the field "Test field" matches value "Custom field text"
