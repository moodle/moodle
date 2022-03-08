@tool @tool_xmldb
Feature: The Reconcile XMLDB files report works and all the files are ok
  In order to ensure that all the XMLDB Editor xml files are generated properly
  As a developer
  I need to be able to run the Reconcile XMLDB files report and verify that everything is ok

  Scenario: The Reconcile XMLDB files reports that all files are ok
    Given I log in as "admin"
    And I navigate to "Development > XMLDB editor" in site administration
    When I follow "[Reconcile XMLDB files]"
    Then I should see "Look for XMLDB files needing reconciling"
    And I should see "All files are OK. No reconciling is needed."
