@tool @tool_lp @tool_lp_framework
Feature: Import and export competency framework
  In order to successfully import a competency framework
  As an admin
  I need to be able to export existing competency framework

  Background:
    Given I log in as "admin"

  Scenario: Competency framework can be exported
    Given the following "core_competency > frameworks" exist:
      | shortname | idnumber |
      | CF1       | CF1      |
    And the following "core_competency > competencies" exist:
      | shortname | competencyframework | idnumber |
      | C1        | CF1                 | C1       |
      | C2        | CF1                 | C2       |
      | C3        | CF1                 | C3       |
    And the following "core_competency > related_competencies" exist:
      | competency | relatedcompetency |
      | C1         | C3                |
    When I navigate to "Competencies > Export competency framework" in site administration
    Then following "Export" button should download a file that:
      | Has mimetype | text/csv |

  @javascript @_file_upload
  Scenario: Competency framework can be imported
    Given I navigate to "Competencies > Import competency framework" in site administration
    And I upload "admin/tool/lpimportcsv/tests/fixtures/example.csv" file to "CSV framework description file" filemanager
    When I press "Import"
    And I press "Confirm"
    And I should see "Competency framework created."
    And I press "Continue"
    Then I should see "The Core Competencies summarise the capabilities"
    And I should see "Levels"
