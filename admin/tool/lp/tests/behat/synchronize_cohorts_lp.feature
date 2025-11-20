@tool @tool_lp @core_cohort
Feature: Cohorts can be synchronized with learning plans
  In order to create learning plans for cohort members
  As an admin
  I need to be able to synchronise cohorts with learning plans

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
      | user2    | User      | Two      | user2@example.com |
    And the following "cohorts" exist:
      | name     | idnumber |
      | Cohort 1 | CH1      |
    And the following "cohort members" exist:
      | user  | cohort |
      | user1 | CH1    |
      | user2 | CH1    |
    And the following "core_competency > frameworks" exist:
      | shortname | idnumber |
      | CF1       | CF1      |
    And the following "core_competency > competencies" exist:
      | shortname | competencyframework | idnumber |
      | C1        | CF1                 | C1       |
    And the following "core_competency > templates" exist:
      | shortname |
      | LPT1      |
    And the following "core_competency > template_competencies" exist:
      | template | competency |
      | LPT1     | C1         |

  @javascript
  Scenario: Cohorts can be synchronised with learning plans
    Given I log in as "admin"
    # Navigate to the list of learning plan templates in order to add cohorts.
    And I navigate to "Competencies > Learning plan templates" in site administration
    When I click on "Add cohorts to sync" of edit menu in the "LPT1" row
    And I set the field "Select cohorts to sync" to "Cohort 1"
    And I press "Add cohorts"
    And I wait until the page is ready
    # Confirm that 2 learning plans were created for members of the cohort.
    Then "2 learning plans were created" "text" should exist
    # Confirm current screen is still "Cohorts synced to this learning plan template screen"
    And "Cohorts synced to this learning plan template" "text" should exist
    # Confirm that the cohort is now added to the learning plan template.
    And the following should exist in the "generaltable" table:
      | Name     | Cohort ID |
      | Cohort 1 | CH1       |
    # Navigate back to the list of learning plan templates to view updated list.
    And I navigate to "Competencies > Learning plan templates" in site administration
    # Confirm that the added cohort and learning plans are now reflected on the list of Learning plan templates.
    And the following should exist in the "generaltable" table:
      | Name | Category | Cohorts | Learning plans |
      | LPT1 | System   | 1       | 2              |
    And I click on ".template-userplans" "css_element" in the "LPT1" "table_row"
    # Confirm that learning plans were created for all cohort members.
    And the following should exist in the "generaltable" table:
      | Name | First name / Last name | Email address     |
      | LPT1 | User One               | user1@example.com |
      | LPT1 | User Two               | user2@example.com |
