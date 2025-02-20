@tool @tool_lp @tool_lp_plan
Feature: Admin can create learning plans from template
  In order to link and unlink a learning plan from a student
  As an admin
  I need to be able to create a learning plan from template

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | student1 | Student   | One      | student1@email.com |
    And the following "core_competency > frameworks" exist:
      | shortname | idnumber |
      | CF1       | CF1      |
      | CF2       | CF2      |
    And the following "core_competency > competencies" exist:
      | shortname        | competencyframework | idnumber |
      | CF1 Competency 1 | CF1                 | CF1C1    |
      | CF2 Competency 1 | CF2                 | CF2C1    |
    And the following "core_competency > templates" exist:
      | shortname | description       |
      | LPT1      | LPT 1 description |
    And the following "core_competency > template_competencies" exist:
      | template | competency |
      | LPT1     | CF1C1      |

  @javascript
  Scenario: Admin can assign competencies from different frameworks to a learning plan
    Given I log in as "admin"
    And I navigate to "Competencies > Learning plan templates" in site administration
    And I follow "LPT1"
    When I press "Add competencies to learning plan template"
    # Add another competency from a different framework in the learning plan template.
    And I set the field with xpath "//select[@data-action='chooseframework']" to "CF2 CF2"
    And I select "CF2 Competency 1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    # Confirm that both competencies from different frameworks are successfully added to learning plan template.
    Then "LPT1" "text" should exist
    And "Learning plan template competencies" "text" should exist
    And "CF1 Competency 1" "text" should exist
    And "CF2 Competency 1" "text" should exist

  @javascript
  Scenario: Admin can link and unlink learning plans created from template
    Given I log in as "admin"
    And I navigate to "Competencies > Learning plan templates" in site administration
    # Initially, the number of learning plans is 0.
    And the following should exist in the "generaltable" table:
      | Name | Learning plans |
      | LPT1 | 0              |
    And I click on ".template-userplans" "css_element" in the "LPT1" "table_row"
    # Create a learning plan for selected student using template.
    When I set the field "Select users" to "student1"
    And I press "Create learning plans"
    # Confirm that selected student is now in the list of learning plans.
    Then "A learning plan was created" "text" should exist
    And the following should exist in the "generaltable" table:
      | Name | Email address      |
      | LPT1 | student1@email.com |
    And I click on "LPT1" "link" in the "LPT1" "table_row"
    # Template title and Unlink from learning plan template link exists.
    And "Learning plan template" "text" should exist
    And "Unlink from learning plan template" "link" should exist
    # Unlink the template.
    And I click on "Unlink from learning plan template" "link"
    And I press "Unlink from learning plan template"
    # Learning plan still exists but Template name and Unlink from learning plan template link no longer exist.
    And "LPT1" "text" should exist
    And "Learning plan template" "text" should not exist
    And "Unlink from learning plan template" "link" should not exist
