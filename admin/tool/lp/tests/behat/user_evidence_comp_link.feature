@tool @javascript @tool_lp @tool_lp_user_evidence_comp_link
Feature: Manage competencies linked to evidence of prior learning
  To link or unlink competency to evidence of prior learning
  As learning plan admin
  I need to link and unlink competencies from evidence of prior learning

  Background:
    Given the following lp "frameworks" exist:
      | shortname | idnumber |
      | Test-Framework | ID-FW1 |
    And the following lp "competencies" exist:
      | shortname | framework |
      | Test-Comp1 | ID-FW1 |
      | Test-Comp2 | ID-FW1 |
    And the following lp "plans" exist:
      | name | user | description |
      | Test-Plan | admin | Plan description |
    And the following lp "plancompetencies" exist:
      | plan | competency |
      | Test-Plan | Test-Comp1 |
      | Test-Plan | Test-Comp2 |
    And the following lp "userevidence" exist:
      | name | description | user |
      | Test-Evidence | Description evidence | admin |
    When I log in as "admin"
    And I follow "Profile" in the user menu
    And I follow "Learning plans"
    Then I should see "Evidence of prior learning"

  Scenario: Link competency to evidence of prior learning from page
    Given I follow "Evidence of prior learning"
    And I should see "List of evidence"
    And I should see "Test-Evidence"
    And I click on "Test-Evidence" "link"
    And I should see "Linked competencies"
    And I press "Link competencies"
    And "Competency picker" "dialogue" should be visible
    And I select "Test-Comp1" of the competency tree
    When I click on "Add" "button" in the "Competency picker" "dialogue"
    Then "Test-Comp1" "table_row" should exist

  Scenario: Link competency to evidence of prior learning from list
    Given I follow "Evidence of prior learning"
    And I change window size to "large"
    And I should see "List of evidence"
    And I should see "Test-Evidence"
    And I click on "Link" of edit menu in the "Test-Evidence" row
    And "Competency picker" "dialogue" should be visible
    And I select "Test-Comp2" of the competency tree
    When I click on "Add" "button" in the "Competency picker" "dialogue"
    Then "Test-Comp2" "table_row" should exist

  Scenario: Unlink competency from evidence of prior learning
    Given the following lp "userevidencecompetencies" exist:
      | userevidence | competency |
      | Test-Evidence | Test-Comp1 |
      | Test-Evidence | Test-Comp2 |
    Given the following lp "usercompetencies" exist:
      | user | competency |
      | admin | Test-Comp1 |
      | admin | Test-Comp2 |
    And I follow "Evidence of prior learning"
    And I should see "List of evidence"
    And I should see "Test-Evidence"
    And I click on "Test-Evidence" "link"
    And I should see "Linked competencies"
    And I should see "Test-Comp1"
    And I should see "Test-Comp2"
    When I click on "Delete" "link" in the "Test-Comp1" "table_row"
    Then I should not see "Test-Comp1"
    And I should see "Test-Comp2"
