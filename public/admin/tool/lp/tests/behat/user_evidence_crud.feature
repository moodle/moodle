@tool @javascript @tool_lp @tool_lp_user_evidence
Feature: Manage evidence of prior learning
  In order to perform CRUD operations on evidence of prior learning
  As a user
  I need to create, update and delete evidence of prior learning

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      |user1 | User | 1 | user1@example.com |
    When I log in as "user1"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    Then I should see "Evidence of prior learning"

  Scenario: Create a new evidence of prior learning
    Given I follow "Evidence of prior learning"
    And I should see "List of evidence"
    When I click on "Add new evidence" "button"
    And I set the field "Name" to "Evidence-1"
    And I set the field "Description" to "Evidence-1 description"
    And I press "Save changes"
    Then I should see "Evidence of prior learning created"
    And I should see "Evidence-1"

  Scenario: Read an evidence of prior learning
    Given the following "core_competency > user_evidence" exist:
      | name       | description            | user  |
      | Evidence-2 | Evidence-2 description | user1 |
    And I follow "Evidence of prior learning"
    And I should see "List of evidence"
    And I should see "Evidence-2"
    When I click on "Evidence-2" "link"
    Then I should see "Evidence-2"

  Scenario: Edit an evidence of prior learning
    Given the following "core_competency > user_evidence" exist:
      | name       | description            | user  |
      | Evidence-3 | Evidence-3 description | user1 |
    And I follow "Evidence of prior learning"
    And I should see "List of evidence"
    And I click on "Edit" of edit menu in the "Evidence-3" row
    And the field "Name" matches value "Evidence-3"
    When I set the field "Name" to "Evidence-3 Edited"
    And I press "Save changes"
    Then I should see "Evidence of prior learning updated"
    And I should see "Evidence-3 Edited"

  Scenario: Delete an evidence of prior learning
    Given the following "core_competency > user_evidence" exist:
      | name       | description            | user  |
      | Evidence-4 | Evidence-4 description | user1 |
    And I follow "Evidence of prior learning"
    And I should see "List of evidence"
    And I click on "Delete" of edit menu in the "Evidence-4" row
    And I click on "Cancel" "button" in the "Confirm" "dialogue"
    And I click on "Delete" of edit menu in the "Evidence-4" row
    And "Confirm" "dialogue" should be visible
    When I click on "Delete" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    Then I should not see "Evidence-4"

  Scenario: List evidences of prior learning
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user2    | User      | 2        | user2@example.com |
    And the following "core_competency > user_evidence" exist:
      | name       | description            | user  |
      | Evidence-5 | Evidence-5 description | user1 |
      | Evidence-6 | Evidence-6 description | user2 |
    And I follow "Evidence of prior learning"
    And I should see "List of evidence"
    And I should see "Evidence-5"
    When I log out
    And I log in as "user2"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    And I follow "Evidence of prior learning"
    Then I should see "List of evidence"
    And I should see "Evidence-6"
    And I should not see "Evidence-5"
