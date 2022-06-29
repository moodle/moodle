@tool @tool_xmldb
Feature: Adding mandatory persistent fields to tables
  In order for me to be able to create database tables for a persistent class
  As a developer
  I need to be able to add fields that are mandatory for the persistent class that I am developing

  Background:
    Given I log in as "admin"
    And I navigate to "Development > XMLDB editor" in site administration
    And I click on "Load" "link" in the "admin/tool/cohortroles/db" "table_row"
    And I follow "admin/tool/cohortroles/db"
    And I follow "New table"

  Scenario: Cancel adding of mandatory persistent fields
    Given I follow "Add mandatory persistent fields"
    And I should see "usermodified"
    And I should see "timecreated"
    And I should see "timemodified"
    When I press "Cancel"
    Then I should see "Edit table"
    And I should not see "usermodified"
    And I should not see "timecreated"
    And I should not see "timemodified"

  Scenario: Creating mandatory persistent fields
    Given I follow "Add mandatory persistent fields"
    And I should see "usermodified"
    And I should see "timecreated"
    And I should see "timemodified"
    When I press "Continue"
    Then I should see "The following fields have been added:"
    And I should see "usermodified" in the ".alert ul" "css_element"
    And I should see "timecreated" in the ".alert ul" "css_element"
    And I should see "timemodified" in the ".alert ul" "css_element"
    And I follow "Back"
    And I should see "usermodified" in the "listfields" "table"
    And I should see "timecreated" in the "listfields" "table"
    And I should see "timemodified" in the "listfields" "table"
    And I should see "usermodified" in the "listkeys" "table"

  Scenario: Partial creation of mandatory persistent fields
    Given I follow "Add mandatory persistent fields"
    And I press "Continue"
    And I follow "Back"
    And I click on "Delete" "link" in the "timecreated" "table_row"
    And I press "Yes"
    When I follow "Add mandatory persistent fields"
    Then I should see "The following fields already exist:"
    And I should see "usermodified" in the ".alert ul" "css_element"
    And I should see "timemodified" in the ".alert ul" "css_element"
    But I should not see "timecreated" in the ".alert ul" "css_element"
    And I should see "Do you want to add the following fields:"
    And I should see "timecreated" in the ".modal ul" "css_element"
    And I press "Continue"
    And I should see "The following fields have been added:"
    And I should see "timecreated" in the ".alert ul" "css_element"
    And I should not see "timemodified" in the ".alert ul" "css_element"
    And I should not see "usermodified" in the ".alert ul" "css_element"
    And I follow "Back"
    And I should see "timecreated" in the "listfields" "table"

  Scenario: Trying to create mandatory persistent fields that have all been added
    Given I follow "Add mandatory persistent fields"
    And I press "Continue"
    And I follow "Back"
    When I follow "Add mandatory persistent fields"
    Then I should see "The following fields already exist:"
    And I should see "usermodified" in the ".alert ul" "css_element"
    And I should see "timecreated" in the ".alert ul" "css_element"
    And I should see "timemodified" in the ".alert ul" "css_element"
    And I should not see "Do you want to add the following fields:"
