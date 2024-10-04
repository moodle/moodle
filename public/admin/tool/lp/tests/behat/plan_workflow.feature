@tool @javascript @tool_lp @tool_lp_plan_workflow
Feature: Manage plan workflow
  As a user
  In order to change the status of plan
  I need to be able to change the status of a plan

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | 1 | user1@example.com |
      | user2 | User | 2 | user2@example.com |
      | manager1 | Manager | 1 | manager@example.com |
    And the following "roles" exist:
      | shortname | name | archetype |
      | usermanageowndraftplan | User manage own draft plan role | user |
      | usermanageownplan | User manage own plan role | user |
      | manageplan | Manager all plans role | manager |
    And the following "role capabilities" exist:
      | role                   | moodle/competency:planmanageowndraft | moodle/competency:planmanageown |
      | usermanageowndraftplan | allow                                |                                 |
      | usermanageownplan      | allow                                | allow                           |
      | manageplan             | allow                                | allow                           |
    And the following "role capability" exists:
      | role                                | manageplan |
      | moodle/competency:planmanage        | allow      |
      | moodle/competency:planview          | allow      |
      | moodle/competency:planreview        | allow      |
      | moodle/competency:planrequestreview | allow      |
    And the following "role assigns" exist:
      | user  | role | contextlevel | reference |
      | user1 | usermanageowndraftplan | System |  |
      | user2 | usermanageownplan | System |  |
      | manager1 | manageplan | System |  |
    And the following "core_competency > frameworks" exist:
      | shortname      | idnumber |
      | Test-Framework | ID-FW1   |
    And the following "core_competency > competencies" exist:
      | shortname  | competencyframework | idnumber   |
      | Test-Comp1 | ID-FW1              | Test-Comp1 |
      | Test-Comp2 | ID-FW1              | Test-Comp2 |
    And the following "core_competency > plans" exist:
      | name       | user  | description                    | reviewer | status |
      | Test-Plan1 | user1 | Description of plan for user 1 | user1    | draft  |
      | Test-Plan2 | user2 | Description of plan for user 2 | user2    | draft  |
    And the following "core_competency > plan_competency" exist:
      | plan       | competency |
      | Test-Plan1 | Test-Comp1 |
      | Test-Plan1 | Test-Comp2 |
      | Test-Plan2 | Test-Comp1 |
      | Test-Plan2 | Test-Comp2 |
    And the following "blocks" exist:
      | blockname               | contextlevel | reference | pagetypepattern | defaultregion |
      | lp                      | System       | 1         | my-index        | content       |

  Scenario: User can manages his own plan draft
    Given I log in as "user1"
    And I follow "Profile" in the user menu
    When I follow "Learning plans"
    Then I should see "List of learning plans"
    And I should see "Test-Plan1"
    And I should not see "Test-Plan2"
    And I click on "Request review" of edit menu in the "Test-Plan1" row
    And I should see "Waiting for review"
    And I click on "Cancel review" of edit menu in the "Test-Plan1" row
    And I should see "Draft"
    And I log out

  Scenario: User can manages his own plan
    Given I log in as "user2"
    And I follow "Profile" in the user menu
    When I follow "Learning plans"
    Then I should see "List of learning plans"
    And I should see "Test-Plan2"
    And I should not see "Test-Plan1"
    And I click on "Request review" of edit menu in the "Test-Plan2" row
    And I should see "Waiting for review"
    And I click on "Start review" of edit menu in the "Test-Plan2" row
    And I should see "In review"
    And I click on "Finish review" of edit menu in the "Test-Plan2" row
    And I should see "Draft"
    And I click on "Make active" of edit menu in the "Test-Plan2" row
    And I should see "Active"
    And I click on "Complete this learning plan" of edit menu in the "Test-Plan2" row
    And I click on "Complete this learning plan" "button" in the "Confirm" "dialogue"
    And I should see "Complete"
    And I click on "Reopen this learning plan" of edit menu in the "Test-Plan2" row
    And I click on "Reopen this learning plan" "button" in the "Confirm" "dialogue"
    And I should see "Active"
    And I log out

  Scenario: Manager can see learning plan with status waiting for review
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status             |
      | Test-Plan3 | user2 | Description of plan 3 for user 1 | waiting for review |
      | Test-Plan4 | user1 | Description of plan 3 for user 1 | draft              |
    When I log in as "manager1"
    Then I should see "Test-Plan3"
    And I should not see "Test-Plan4"
    And I log out

  Scenario: Manager can start review of learning plan with status waiting for review
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status             |
      | Test-Plan3 | user1 | Description of plan 3 for user 1 | waiting for review |
    And I log in as "manager1"
    And I follow "Test-Plan3"
    And I should see "User 1"
    And I should see "Test-Plan3"
    When I follow "Start review"
    Then I should see "In review"
    And I log out

  Scenario: Manager can reject a learning plan with status in review
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status    | reviewer |
      | Test-Plan3 | user1 | Description of plan 3 for user 1 | in review | manager1 |
    And I log in as "manager1"
    And I follow "Test-Plan3"
    And I should see "User 1"
    And I should see "Test-Plan3"
    And I should see "In review"
    When I follow "Finish review"
    Then I should see "Draft"
    And I log out

  Scenario: Manager can accept a learning plan with status in review
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status    | reviewer |
      | Test-Plan3 | user1 | Description of plan 3 for user 1 | in review | manager1 |
    And I log in as "manager1"
    And I follow "Test-Plan3"
    And I should see "User 1"
    And I should see "Test-Plan3"
    And I should see "In review"
    When I follow "Make active"
    Then I should see "Active"
    And I log out

  Scenario: Manager send back to draft an active learning plan
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status | reviewer |
      | Test-Plan3 | user1 | Description of plan 3 for user 1 | active | manager1 |
      | Test-Plan4 | user1 | Description of plan 4 for user 1 | active | manager1 |
    And I am on the "user1" "user > profile" page logged in as "manager1"
    And I follow "Learning plans"
    And I should see "List of learning plans"
    When I click on "Send back to draft" of edit menu in the "Test-Plan3" row
    And I follow "Test-Plan4"
    And I follow "Send back to draft"
    And I follow "Learning plans"
    Then I should see "Draft"
    And I should not see "Active"
    And I log out

  Scenario: Manager change an active learning plan to completed
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status | reviewer |
      | Test-Plan3 | user1 | Description of plan 3 for user 1 | active | manager1 |
      | Test-Plan4 | user1 | Description of plan 4 for user 1 | active | manager1 |
    And I am on the "user1" "user > profile" page logged in as "manager1"
    And I follow "Learning plans"
    And I should see "List of learning plans"
    When I click on "Complete this learning plan" of edit menu in the "Test-Plan3" row
    And I click on "Complete this learning plan" "button" in the "Confirm" "dialogue"
    And I wait until the page is ready
    And I follow "Test-Plan4"
    And I follow "Complete this learning plan"
    And I click on "Complete this learning plan" "button" in the "Confirm" "dialogue"
    And I follow "Learning plans"
    Then I should see "Complete"
    And I should not see "Active"
    And I log out

  Scenario: Manager reopen a complete learning plan
    Given the following "core_competency > plans" exist:
      | name       | user  | description                      | status   | reviewer |
      | Test-Plan3 | user1 | Description of plan 3 for user 1 | complete | manager1 |
      | Test-Plan4 | user1 | Description of plan 4 for user 1 | complete | manager1 |
    And I am on the "user1" "user > profile" page logged in as "manager1"
    And I follow "Learning plans"
    And I should see "List of learning plans"
    When I click on "Reopen this learning plan" of edit menu in the "Test-Plan3" row
    And I click on "Reopen this learning plan" "button" in the "Confirm" "dialogue"
    And I follow "Test-Plan4"
    And I follow "Reopen this learning plan"
    And I click on "Reopen this learning plan" "button" in the "Confirm" "dialogue"
    And I follow "Learning plans"
    Then I should see "Active"
    And I should not see "Complete"
    And I log out
