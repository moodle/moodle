@tool @tool_admin_presets @javascript
Feature: I can add a new preset with current settings

  Background:
    Given I log in as "admin"
    And I navigate to "Site admin presets" in site administration

  Scenario: Export settings with an existing name
    Given I should see "Starter"
    And I click on "Create preset" "button"
    And I set the field "Name" to "Starter"
    And I set the field "Description" to "Non-core starter preset"
    When I click on "Create preset" "button"
    Then the following should exist in the "Site admin presets table" table:
      | Name    | Description                                                                                                                        |
      | Starter | Moodle with all of the most popular features, including Assignment, Feedback, Forum, H5P, Quiz and Completion tracking.            |
      | Full    | All the Starter features plus External (LTI) tool, SCORM, Workshop, Analytics, Badges, Competencies, Learning plans and lots more. |
      | Starter | Non-core starter preset                                                                                                            |

  Scenario: Export current settings
    Given I click on "Create preset" "button"
    And I set the field "Name" to "Current"
    And I click on "Create preset" "button"
    And I should see "Current"
    And I open the action menu in "Current" "table_row"
    When I choose "Review settings and apply" in the open action menu
    And I should not see "Setting changes"
    And I click on "Continue" "button"
    And the following config values are set as admin:
      | enableportfolios | 1 |
    And I open the action menu in "Current" "table_row"
    And I choose "Review settings and apply" in the open action menu
    Then I should see "Setting changes"
