@tool @tool_analytics
Feature: Restoring default models
  In order to get prediction models into their initial state
  As a manager
  I need to be able to restore deleted default models

  Background:
    Given the following "users" exist:
      | username 	| firstname	| lastname | email          	|
      | manager  	| Max      	| Manager  | man@example.com	|
    And the following "role assigns" exist:
      | user    	| role    	| contextlevel	| reference		|
      | manager 	| manager		| System      	|							|

  Scenario: Restore a single deleted default model
    Given I log in as "manager"
    And I navigate to "Analytics > Analytics models" in site administration
    # Delete 'No teaching' model.
    And I click on "Delete" "link" in the "No teaching" "table_row"
    And I should see "Analytics models"
    And I should not see "No teaching"
    # Delete 'Students at risk of dropping out' model.
    And I click on "Delete" "link" in the "Students at risk of dropping out" "table_row"
    And I should see "Analytics models"
    And I should not see "Students at risk of dropping out"
    # Go to the page for restoring deleted models.
    When I click on "Restore default models" "link"
    And I should see "No teaching"
    And I should see "Students at risk of dropping out"
    # Select and restore the 'No teaching' model.
    And I set the field with xpath "//tr[contains(normalize-space(.), 'No teaching')]//input[@type='checkbox']" to "1"
    And I click on "Restore selected" "button"
    Then I should see "Succesfully re-created 1 new model(s)."
    And I should see "Analytics models"
    And I should see "No teaching"
    And I should not see "Students at risk of dropping out"

  Scenario: Restore multiple deleted default models at once
    Given I log in as "manager"
    And I navigate to "Analytics > Analytics models" in site administration
    # Delete 'No teaching' model.
    And I click on "Delete" "link" in the "No teaching" "table_row"
    And I should see "Analytics models"
    And I should not see "No teaching"
    # Delete 'Students at risk of dropping out' model.
    And I click on "Delete" "link" in the "Students at risk of dropping out" "table_row"
    And I should see "Analytics models"
    And I should not see "Students at risk of dropping out"
    # Go to the page for restoring deleted models.
    When I click on "Restore default models" "link"
    And I should see "No teaching"
    And I should see "Students at risk of dropping out"
    # Select and restore both models.
    And I set the field with xpath "//tr[contains(normalize-space(.), 'No teaching')]//input[@type='checkbox']" to "1"
    And I set the field with xpath "//tr[contains(normalize-space(.), 'Students at risk of dropping out')]//input[@type='checkbox']" to "1"
    And I click on "Restore selected" "button"
    Then I should see "Succesfully re-created 2 new model(s)."
    And I should see "Analytics models"
    And I should see "No teaching"
    And I should see "Students at risk of dropping out"

  Scenario: Going to the restore page while no models can be restored
    Given I log in as "manager"
    And I navigate to "Analytics > Analytics models" in site administration
    And I should see "Analytics models"
    And I should see "No teaching"
    When I click on "Restore default models" "link"
    Then I should see "All default models provided by core and installed plugins have been created. No new models were found; there is nothing to restore."
    And I click on "Back" "link"
    And I should see "Analytics models"

  @javascript
  Scenario: User can select and restore all missing models
    Given I log in as "manager"
    And I navigate to "Analytics > Analytics models" in site administration
    # Delete 'No teaching' model.
    And I click on "Actions" "link" in the "No teaching" "table_row"
    And I click on "Delete" "link" in the "No teaching" "table_row"
    And I click on "Delete" "button" in the "Delete" "dialogue"
    And I should see "Analytics models"
    And I should not see "No teaching"
    # Delete 'Students at risk of dropping out' model.
    And I click on "Actions" "link" in the "Students at risk of dropping out" "table_row"
    And I click on "Delete" "link" in the "Students at risk of dropping out" "table_row"
    And I click on "Delete" "button" in the "Delete" "dialogue"
    And I should see "Analytics models"
    And I should not see "No teaching"
    And I should not see "Students at risk of dropping out"
    # Go to the page for restoring deleted models.
    And I click on "New model" "link"
    And I click on "Restore default models" "link"
    And I should see "No teaching"
    And I should see "Students at risk of dropping out"
    # Attempt to submit the form without selecting any model.
    And I click on "Restore selected" "button"
    And I should see "Please select models to be restored."
    # Select all models.
    When I click on "Select all" "link"
    And I click on "Restore selected" "button"
    Then I should see "Succesfully re-created 2 new model(s)."
    And I should see "Analytics models"
    And I should see "No teaching"
    And I should see "Students at risk of dropping out"
