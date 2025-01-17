@report @core_ai @core_ai_reports
Feature: AI reports
  In order to view an AI report
  As an admin or system role manager
  I need to populate relevant data and navigate to the report page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |
    And the following config values are set as admin:
      | enabled        | 1 | aiplacement_editor |
      | generate_text  | 1 | aiplacement_editor |
      | generate_image | 0 | aiplacement_editor |
    And the following "core_ai > ai providers" exist:
      |provider          | name             | enabled | apikey | orgid |
      |aiprovider_openai | OpenAI API test  | 1       | 123    | abc   |

  @javascript @editor_tiny
  Scenario: Mangers with a system role can see who has accepted the AI policy
    Given I am logged in as "admin"
    # Accept the AI policy as admin.
    And I open my profile in edit mode
    And I select the "p" element in position "0" of the "Description" TinyMCE editor
    And I expand all toolbars for the "Description" TinyMCE editor
    And I click on the "AI generate text" button for the "Description" TinyMCE editor
    And I click on "Accept and continue" "button" in the "AI usage policy" "dialogue"
    And I press the escape key
    # Accept the AI policy as manager1.
    And I am logged in as "manager1"
    And I open my profile in edit mode
    And I select the "p" element in position "0" of the "Description" TinyMCE editor
    And I expand all toolbars for the "Description" TinyMCE editor
    And I click on the "AI generate text" button for the "Description" TinyMCE editor
    And I click on "Accept and continue" "button" in the "AI usage policy" "dialogue"
    And I press the escape key
    # View the report.
    When I navigate to "Reports > AI reports > AI policy acceptance" in site administration
    Then I should see "Admin User" in the "reportbuilder-table" "table"
    And I should see "Manager One" in the "reportbuilder-table" "table"
    # Test date filter (check last 1 day).
    And I click on "Filters" "button"
    And I set the following fields in the "Date accepted" "core_reportbuilder > Filter" to these values:
      | Date accepted operator | Last |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should see "Admin User" in the "reportbuilder-table" "table"
    And I should see "Manager One" in the "reportbuilder-table" "table"
    # Test name filter.
    And I click on "Filters" "button"
    And I set the following fields in the "Full name" "core_reportbuilder > Filter" to these values:
      | Full name operator | Is equal to |
      | Full name value    | Admin User  |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And I click on "Filters" "button"
    And I should see "Admin User" in the "reportbuilder-table" "table"
    And I should not see "Manager One" in the "reportbuilder-table" "table"
