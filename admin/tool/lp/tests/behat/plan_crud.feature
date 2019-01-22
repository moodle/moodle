@tool @javascript @tool_lp @tool_lp_plan
Feature: Manage plearning plan
  As a learning plan admin
  In order to perform CRUD operations on learning plan
  I need to create, update and delete learning plan

  Background:
    Given I log in as "admin"
    And I am on site homepage
    When I follow "Profile" in the user menu
    Then I should see "Learning plans"

  Scenario: Create a new learning plan
    Given I follow "Learning plans"
    And I should see "List of learning plan"
    And I click on "Add new learning plan" "button"
    And I should see "Add new learning plan"
    And I set the field "Name" to "Science plan"
    And I set the field "Description" to "Here description of learning plan"
    When I press "Save changes"
    Then I should see "Learning plan created"
    And I should see "Science plan"

  Scenario: Create a learning plan based on template
    Given the following lp "templates" exist:
      | shortname | description |
      | Science template | science template description |
    And I follow "Home"
    And I navigate to "Competencies > Learning plan templates" in site administration
    And I click on ".template-userplans" "css_element" in the "Science template" "table_row"
    And I open the autocomplete suggestions list
    And I click on "Admin User" item in the autocomplete list
    And I press key "27" in the field "Select users to create learning plans for"
    When I click on "Create learning plans" "button"
    Then I should see "A learning plan was created"
    And I should see "Admin User" in the "Science template" "table_row"

  Scenario: Create a learning plan from template cohort
    Given the following lp "templates" exist:
      | shortname | description |
      | Science template cohort | science template description |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | student-plan1 | Student | 1 | studentplan1@example.com |
      | student-plan2 | Student | 2 | studentplan2@example.com |
    And the following "cohorts" exist:
      | name            | idnumber |
      | cohort plan | COHORTPLAN |
    And the following "cohort members" exist:
      | user     | cohort |
      | student-plan1 | COHORTPLAN |
      | student-plan2 | COHORTPLAN |
    And I follow "Home"
    And I navigate to "Competencies > Learning plan templates" in site administration
    And I click on ".template-cohorts" "css_element" in the "Science template cohort" "table_row"
    And I click on ".form-autocomplete-downarrow" "css_element"
    And I click on "cohort plan" item in the autocomplete list
    And I press key "27" in the field "Select cohorts to sync"
    When I click on "Add cohorts" "button"
    Then I should see "2 learning plans were created."
    And I follow "Learning plan templates"
    And I click on ".template-userplans" "css_element" in the "Science template cohort" "table_row"
    And I should see "Student 1"
    And I should see "Student 2"

  Scenario: Read a learning plan
    Given the following lp "plans" exist:
      | name | user | description |
      | Science plan Year-1 | admin | science plan description |
    And I follow "Learning plans"
    And I should see "Science plan Year-1"
    When I click on "Science plan Year-1" "link"
    Then I should see "Science plan Year-1"
    And I should see "Learning plan competencies"

  Scenario: Manage a learning plan competencies
    Given the following lp "plans" exist:
      | name | user | description |
      | Science plan Year-manage | admin | science plan description |
    And the following lp "frameworks" exist:
      | shortname | idnumber |
      | Framework 1 | sc-y-2 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | comp1 | sc-y-2 |
      | comp2 | sc-y-2 |
    And I follow "Learning plans"
    And I should see "Science plan Year-manage"
    And I follow "Science plan Year-manage"
    And I should see "Add competency"
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    When I click on "Add" "button" in the "Competency picker" "dialogue"
    Then "comp1" "table_row" should exist
    And I click on "Delete" of edit menu in the "comp1" row
    And "Confirm" "dialogue" should be visible
    And I click on "Confirm" "button"
    And I wait until the page is ready
    And "comp1" "table_row" should not exist

  Scenario: Edit a learning plan
    Given the following lp "plans" exist:
      | name | user | description |
      | Science plan Year-2 | admin | science plan description |
      | Science plan Year-3 | admin | science plan description |
    And I follow "Learning plans"
    And I should see "Science plan Year-2"
    And I should see "Science plan Year-3"
    And I click on "Edit" of edit menu in the "Science plan Year-3" row
    And the field "Name" matches value "Science plan Year-3"
    And I set the field "Name" to "Science plan Year-3 Edited"
    When I press "Save changes"
    Then I should see "Learning plan updated"
    And I should see "Science plan Year-3 Edited"

  Scenario: Delete a learning plan
    Given the following lp "plans" exist:
      | name | user | description |
      | Science plan Year-4 | admin | science plan description |
    And I follow "Learning plans"
    And I should see "Science plan Year-4"
    And I click on "Delete" of edit menu in the "Science plan Year-4" row
    And "Confirm" "dialogue" should be visible
    And "Delete" "button" should exist in the "Confirm" "dialogue"
    And "Cancel" "button" should exist in the "Confirm" "dialogue"
    And I click on "Cancel" "button" in the "Confirm" "dialogue"
    And I click on "Delete" of edit menu in the "Science plan Year-4" row
    And "Confirm" "dialogue" should be visible
    When I click on "Delete" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    Then I should not see "Science plan Year-4"

  Scenario: See a learning plan from a course
    Given the following lp "plans" exist:
      | name | user | description |
      | Science plan Year-manage | admin | science plan description |
    And the following lp "frameworks" exist:
      | shortname | idnumber |
      | Framework 1 | sc-y-2 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | comp1 | sc-y-2 |
      | comp2 | sc-y-2 |
    And I follow "Learning plans"
    And I should see "Science plan Year-manage"
    And I follow "Science plan Year-manage"
    And I should see "Add competency"
    And I press "Add competency"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    When I click on "Add" "button" in the "Competency picker" "dialogue"
    Then "comp1" "table_row" should exist
    And I create a course with:
      | Course full name | New course fullname |
      | Course short name | New course shortname |
    And I follow "New course fullname"
    And I follow "Competencies"
    And I press "Add competencies to course"
    And "Competency picker" "dialogue" should be visible
    And I select "comp1" of the competency tree
    And I click on "Add" "button" in the "Competency picker" "dialogue"
    And I should see "Learning plans"
    And I should see "Science plan Year-manage"
