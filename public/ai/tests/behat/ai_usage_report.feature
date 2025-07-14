@report @core_ai
Feature: AI usage report displays recorded AI data
  In order to view AI usage data
  As a manager or admin
  I can access the AI usage report

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | manager1 | Manager   | One      | manager1@example.com |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
    And the following "role assigns" exist:
      | user     | role    | contextlevel | reference |
      | manager1 | manager | System       |           |
    And the following "core_ai > ai providers" exist:
      | provider          | name             | enabled | apikey | orgid |
      | aiprovider_openai | OpenAI API test  | 1       | 123    | abc   |
    And the following "core_ai > ai actions" exist:
      | actionname     | user     | success | provider           | contextid | prompttokens | completiontokens |
      | generate_text  | student1 | 1       | aiprovider_openai  | 1         | 22           | 33               |
      | summarise_text | student1 | 0       | aiprovider_openai  | 1         |              |                  |
      | explain_text   | student1 | 1       | aiprovider_openai  | 1         | 44           | 55               |
      | generate_image | student2 | 1       | aiprovider_azureai | 1         |              |                  |

  Scenario: Managers can view the AI usage report
    Given I am logged in as "manager1"
    When I navigate to "Reports > AI reports > AI usage" in site administration
    Then the following should exist in the "AI usage" table:
      | Action         | First name  | Provider              | Success | Prompt tokens | Completion tokens |
      | Generate text  | Student One | OpenAI API provider   | Yes     | 22            | 33                |
      | Summarise text | Student One | OpenAI API provider   | No      |               |                   |
      | Explain text   | Student One | OpenAI API provider   | Yes     | 44            | 55                |
      | Generate image | Student Two | Azure AI API provider | Yes     |               |                   |

  @javascript
  Scenario: Managers can filter the AI usage report
    Given I am logged in as "manager1"
    When I navigate to "Reports > AI reports > AI usage" in site administration
    And I click on "Filters" "button"
    And I set the field "Provider value" in the "Provider" "core_reportbuilder > Filter" to "OpenAI API provider"
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    Then the following should exist in the "AI usage" table:
      | Action         | First name  | Provider            | Success | Prompt tokens | Completion tokens |
      | Generate text  | Student One | OpenAI API provider | Yes     | 22            | 33                |
      | Summarise text | Student One | OpenAI API provider | No      |               |                   |
      | Explain text   | Student One | OpenAI API provider | Yes     | 44            | 55                |
    And I should not see "Azure AI API provider" in the "AI usage" "table"
    And I set the following fields in the "Action" "core_reportbuilder > Filter" to these values:
      | Action operator | Is equal to   |
      | Action value    | Generate text |
    And I click on "Apply" "button" in the "[data-region='report-filters']" "css_element"
    And the following should exist in the "AI usage" table:
      | Action         | First name  | Provider            | Success | Prompt tokens | Completion tokens |
      | Generate text  | Student One | OpenAI API provider | Yes     | 22            | 33                |
    And I should not see "Summarise text" in the "AI usage" "table"
    And I should not see "Azure AI API Provider" in the "AI usage" "table"
