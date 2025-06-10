@report @javascript @report_lpmonitoring
Feature: Manage configuration for monitoring of learning plans report
  As an appreciator of learning plan
  In order to display color configuration for monitoring of learning plan report
  I need to create, update configuration

  Background:
    Given the lpmonitoring fixtures exist
    And I log in as "appreciator"
    And I am on the "Medicine" "Category" page
    And I select "More" from secondary navigation
    Then I should see "Competencies scale colors"
    And I follow "Competencies scale colors"

  Scenario: Create colors configuration with picker
    Given I set the field "templateselector" to "Framework Medicine (Medicine)"
    And the "scaleselector" select box should contain "Scale default"
    And the "scaleselector" select box should contain "Scale specific"
    And I set the field "scaleselector" to "Scale default"
    And I should see "Colors for the scale: Scale default"
    And I should see "not good"
    And I should see "good"
    When I set the field with xpath "//input[@name='not good']" to "#ea1022"
    And I set the field with xpath "//input[@name='good']" to "#38ea3a"
    And I press "Save"
    Then I should see "scale were saved successfully"
    And I set the field "templateselector" to "Choose a competency framework"
    And the "scaleselector" select box should contain "No scale available"
    And the "scaleselector" "select" should be disabled
    And I set the field "templateselector" to "Framework Medicine (Medicine)"
    And I set the field "scaleselector" to "Scale default"
    And "//input[@name='not good' and @value='#ea1022']" "xpath_element" should exist
    And "//input[@name='good' and @value='#38ea3a']" "xpath_element" should exist

  Scenario: Update colors configuration
    Given I set the field "templateselector" to "Framework Medicine (Medicine)"
    And the "scaleselector" select box should contain "Scale default"
    And the "scaleselector" select box should contain "Scale specific"
    And I set the field "scaleselector" to "Scale specific"
    And I should see "Colors for the scale: Scale specific"
    And I should see "not qualified"
    And I should see "qualified"
    And "//input[@name='not qualified' and @value='#f30c0c']" "xpath_element" should exist
    And "//input[@name='qualified' and @value='#14e610']" "xpath_element" should exist
    When I set the field with xpath "//input[@name='not qualified']" to "#2ca9d3"
    And I set the field with xpath "//input[@name='qualified']" to "#e6e00d"
    And I press "Save"
    Then I should see "scale were saved successfully"
# Reactiver ce code dans tache MDLUM-6027
#    And I set the field "templateselector" to "Choose a competency framework"
#    And the "scaleselector" select box should contain "No scale available"
#    And the "scaleselector" "select" should be disabled
#    And I set the field "templateselector" to "Framework Medicine (Medicine)"
#    And I set the field "scaleselector" to "Scale specific"
#    And I should see "Colors for the scale: Scale specific"
#    And "//input[@name='not qualified' and @value='#2ca9d3']" "xpath_element" should exist
#    And "//input[@name='qualified' and @value='#e6e00d']" "xpath_element" should exist

