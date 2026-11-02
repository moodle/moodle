@tool @tool_lpimportcsv
Feature: Import and export competency framework
  In order to reuse a competency framework
  As an admin
  I need to be able to export and import competency frameworks

  Background:
    Given I log in as "admin"

  Scenario: Competency framework can be exported
    Given the following "scales" exist:
      | name             | scale                          |
      | Framework scale  | Beginner,Intermediate,Expert   |
      | Competency scale | Emerging,Developing,Proficient |
    And the following "core_competency > frameworks" exist:
      | shortname | idnumber | scale           |
      | CF1       | CF1      | Framework scale |
    And the following "core_competency > competencies" exist:
      | shortname          | idnumber | competencyframework | ruletype                             | ruleoutcome |
      | Level 1 competency | CF1-L1   | CF1                 | core_competency\competency_rule_all  | 2           |
    And the following "core_competency > competencies" exist:
      | shortname          | idnumber | competencyframework | parent | scale            |
      | Level 2 competency | CF1-L2   | CF1                 | CF1-L1 | Competency scale |
    And the following "core_competency > competencies" exist:
      | shortname          | idnumber | competencyframework | parent |
      | Level 3 competency | CF1-L3   | CF1                 | CF1-L2 |
      | Related competency | CF1-RC   | CF1                 | CF1-L2 |
    And the following "core_competency > related_competencies" exist:
      | competency | relatedcompetency |
      | CF1-L3     | CF1-RC            |
    When I navigate to "Competencies > Export competency framework" in site administration
    And I set the field "Competency framework" to "CF1"
    Then following "Export" "button" should download a file that:
      | Has mimetype  | text/csv                       |
      | Contains text | Beginner,Intermediate,Expert   |
      | Contains text | CF1-L1,CF1-L2                  |
      | Contains text | CF1-L2,CF1-L3                  |
      | Contains text | Emerging,Developing,Proficient |
      | Contains text | competency_rule_all",2         |
      | Contains text | null,CF1-RC                    |
      | Contains text | null,CF1-L3                    |

  @javascript @_file_upload
  Scenario: Competency framework can be imported
    # The fixture is the file exported in the scenario above with the id numbers updated from CF1 to CF2.
    Given I navigate to "Competencies > Import competency framework" in site administration
    And I upload "admin/tool/lpimportcsv/tests/fixtures/framework_cf1_updated_idnumbers.csv" file to "CSV framework description file" filemanager
    And I press "Import"
    When I press "Confirm"
    Then I should see "Competency framework created."
    And I navigate to "Competencies > Competency frameworks" in site administration
    And I click on "CF1 (CF2)" "link"
    And I should see "CF1 description"
    # Only Level 1 competency has a rule.
    And I select "Level 1 competency" of the competency tree
    And I should see "All children are complete" in the "[data-region='competencyinfo']" "css_element"
    And I should see "Mark as complete" in the "[data-region='competencyinfo']" "css_element"
    And I should see "No other competencies have been cross-referenced to this competency." in the "[data-region='relatedcompetencies']" "css_element"
    And I select "Level 2 competency" of the competency tree
    And I should not see "Competency rule" in the "[data-region='competencyinfo']" "css_element"
    And I should see "No other competencies have been cross-referenced to this competency." in the "[data-region='relatedcompetencies']" "css_element"
    # The cross-reference is kept in both directions.
    And I select "Level 3 competency" of the competency tree
    And I should see "Related competency CF2-RC" in the "[data-region='relatedcompetencies']" "css_element"
    And I should not see "No other competencies have been cross-referenced to this competency." in the "[data-region='relatedcompetencies']" "css_element"
    And I select "Related competency" of the competency tree
    And I should see "Level 3 competency CF2-L3" in the "[data-region='relatedcompetencies']" "css_element"
    # The competency dialogue shows the path and taxonomy, which confirm the three levels were imported.
    And I click on "Level 3 competency CF2-L3" "link" in the "[data-region='relatedcompetencies']" "css_element"
    And I should see "CF2-L1" in the "Level 3 competency" "dialogue"
    And I should see "CF2-L2" in the "Level 3 competency" "dialogue"
    And I should see "CF1 - Indicator" in the "Level 3 competency" "dialogue"
    And I click on "Close" "button" in the "Level 3 competency" "dialogue"
    # The custom scales are recreated with their values.
    And I navigate to "Grades > Scales" in site administration
    And I should see "Beginner, Intermediate, Expert" in the "Competency scale: CF1" "table_row"
    And I should see "Emerging, Developing, Proficient" in the "Competency scale: Level 2 competency" "table_row"
