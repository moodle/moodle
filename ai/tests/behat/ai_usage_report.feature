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
    And the following "core_ai > ai actions" exist:
      | actionname     | user     | success | provider | contextid |
      | generate_text  | student1 | 1       | OpenAI   | 1         |
      | summarise_text | student1 | 0       | OpenAI   | 1         |
      | generate_image | student2 | 1       | Azure    | 1         |

  Scenario: Managers can view the AI usage report
    Given I am logged in as "manager1"
    When I navigate to "Reports > AI reports > AI usage" in site administration
    Then the following should exist in the "reportbuilder-table" table:
      | Action         | First name  | Provider | Success |
      | Generate text  | Student One | OpenAI   | Yes     |
      | Summarise text | Student One | OpenAI   | No      |
      | Generate image | Student Two | Azure    | Yes     |
