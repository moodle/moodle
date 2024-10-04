@tool @tool_generator @_file_upload
Feature: Create testing scenarios using generators
  In order to execute manual tests
  As a developer
  I need to use a feature file to execute generators into the current instance

  @javascript
  Scenario: Create a testing scenario with a course enrolled users and activities
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario.feature" file to "Feature file" filemanager
    And I press "Import"
    And I should see "Scenario: Create course content"
    Then I am on the "C1" "Course" page
    And I should see "Activity sample 1" in the "Section 1" "section"
    And I should see "Activity sample 2" in the "Section 1" "section"
    And I navigate to course participants
    And I should see "Teacher Test1"
    And I should see "Student Test1"
    And I should see "Student Test2"
    And I should see "Student Test3"
    And I should see "Student Test4"
    And I should see "Student Test5"
    And I set the field "Participants tertiary navigation" to "Enrolment methods"
    And I click on "Edit" "link" in the "Manual enrolments" "table_row"
    And the field "Send course welcome message" matches value "No"
    And I navigate to "Plugins > Enrolments > Manual enrolments" in site administration
    And the field "Send course welcome message" matches value "No"

  @javascript
  Scenario: Prevent creating a testing scenario with no steps to execute
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_wrongempty.feature" file to "Feature file" filemanager
    And I press "Import"
    Then I should see "There are no steps to execute in the file."

  @javascript
  Scenario: Prevent creating a testing scenario with only background steps to execute
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_wrongonlybackground.feature" file to "Feature file" filemanager
    And I press "Import"
    Then I should see "There are no steps to execute in the file."

  @javascript
  Scenario: Prevent creating a testing scenario with a wrong file format
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_wrongformat.feature" file to "Feature file" filemanager
    And I press "Import"
    Then I should see "Error parsing feature file"

  @javascript
  Scenario: Prevent creating a testing scenario with non generator steps
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_wrongstep.feature" file to "Feature file" filemanager
    And I press "Import"
    Then I should see "The file format is not valid or contains invalid steps"

  @javascript
  Scenario: Create a testing scenario from a scenario outline
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_outline.feature" file to "Feature file" filemanager
    And I press "Import"
    And I should see "Example: creating test scenarios using an outline (1)"
    And I should see "Example: creating test scenarios using an outline (2)"
    And I should see "Example: creating test scenarios using an outline (3)"
    Then I am on the "C1" "Course" page
    And I should see "Course 1" in the "page-header" "region"
    And I am on the "C2" "Course" page
    And I should see "Course 2" in the "page-header" "region"
    And I am on the "C3" "Course" page
    And I should see "Course 3" in the "page-header" "region"

  @javascript
  Scenario: Run cleanup steps after creating a testing scenario
    Given I log in as "admin"
    And I navigate to "Development > Create testing scenarios" in site administration
    And I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_cleanup.feature" file to "Feature file" filemanager
    And I press "Import"
    And I should see "Scenario: Create course content to cleanup later"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I should see "Course cleanup" in the "course-listing" "region"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should see "Teacher Test1"
    And I navigate to "Development > Create testing scenarios" in site administration
    When I upload "admin/tool/generator/tests/fixtures/testscenario/scenario_cleanup.feature" file to "Feature file" filemanager
    And I set the field "Execute" to "Cleanup scenarios"
    And I press "Import"
    And I should see "the course \"Course cleanup\" is deleted"
    And I should see "the user \"cleanteacher\" is deleted"
    Then I navigate to "Courses > Manage courses and categories" in site administration
    And I should not see "Course cleanup" in the "course-listing" "region"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I should not see "Teacher Test1"

  Scenario: All available steps are listed in the tool to create testing scenarios
    Given I log in as "admin"
    When I navigate to "Development > Create testing scenarios" in site administration
    Then I should see "This is the list of steps that can be used in the test scenario feature file"
    And I should see "And the following \"activities\" exist"
    And I should see "And \"5\" \"course enrolments\" exist with the following data"
    And I should see "And the following \"course\" exists"
    And I should see "And the following config values are set as admin"
    And I should see "I enable \"subsection\" \"mod\" plugin"
    And I should see "I disable \"page\" \"mod\" plugin"
    And I should see "And the course \"Course test\" is deleted"
    And I should see "And the user student1 is deleted"
