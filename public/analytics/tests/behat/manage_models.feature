@core @core_analytics @javascript
Feature: Manage analytics models
  In order to manage analytics models
  As a manager
  I need to create and use a model

  Background:
    Given a Python Machine Learning backend server is configured
    And I change the Python Machine Learning backend to use external server
    # Turn off the course welcome message, so we can easily test other messages.
    And the following config values are set as admin:
      | onlycli                  | 0 | analytics    |
      | sendcoursewelcomemessage | 0 | enrol_manual |
      | enableanalytics          | 1 |              |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | 1        | manager1@example.com |
    And the following "system role assigns" exist:
      | user     | course               | role      |
      | manager1 | Acceptance test site | manager   |
    And the following "analytics model" exist:
      | target                                   | indicators                                 | timesplitting                               | enabled |
      | \core_course\analytics\target\course_completion | \core\analytics\indicator\any_write_action,\core\analytics\indicator\read_actions |  \core\analytics\time_splitting\single_range | true    |
    And I log in as "manager1"
    And I navigate to "Analytics > Analytics models" in site administration

  Scenario: Create a model
    When I open the action menu in ".top-nav" "css_element"
    And I choose "Create model" in the open action menu
    And I set the field "Enabled" to "Enable"
    And I select "__core_course__analytics__target__course_completion" from the "target" singleselect
    And I set the field "Indicators" to "Read actions amount, Any write action in the course"
    And I select "__core__analytics__time_splitting__single_range" from the "timesplitting" singleselect
    And I press "Save changes"
    Then I should see "No predictions available yet" in the "Students at risk of not meeting the course completion conditions" "table_row"

  Scenario: Edit a model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Edit" in the open action menu
    And I click on "Read actions amount" "text" in the ".form-autocomplete-selection" "css_element"
    And I press "Save changes"
    And I should not see "Read actions amount"

  Scenario: Disable a model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Disable" in the open action menu
    Then I should see "Disabled model" in the "Students at risk of not meeting the course completion conditions" "table_row"

  Scenario: Export model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Export" in the open action menu
    And I click on "Actions" "link" in the "Students at risk of not meeting the course completion conditions" "table_row"
    And following "Export" should download a file that:
       | Contains file in zip | model-config.json |

  Scenario: Check invalid site elements
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Invalid site elements" in the open action menu
    Then I should see "Invalid analysable elements"

  Scenario: Delete model
    When I open the action menu in "Students at risk of not meeting the course completion conditions" "table_row"
    And I choose "Delete" in the open action menu
    And I click on "Delete" "button" in the "Delete" "dialogue"
    Then I should not see "Students at risk of not meeting the course completion conditions"
