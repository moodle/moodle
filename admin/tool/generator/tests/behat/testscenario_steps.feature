@tool @tool_generator @_file_upload
Feature: Make test scenario can execute specific steps
  In order to create all sort of testing scenarios
  As a developer
  I need to execute some generic steps in the current instance

  @javascript
  Scenario: Make test scenario can enable and disable plugins
    Given I disable "page" "mod" plugin
    And I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    And I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_plugins.feature" file to "Feature file" filemanager
    And I press "Import"
    And I should see "Scenario: Course with some disabled plugins"
    When I am on "C1" course homepage with editing mode on
    And I click on "Add an activity or resource" "button" in the "Section 1" "section"
    Then I should see "Page" in the "Add an activity or resource" "dialogue"
    And I should not see "Book" in the "Add an activity or resource" "dialogue"
