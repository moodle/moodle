@mod @mod_glossary
Feature: A teacher can choose whether to provide a printer-friendly glossary entries list
  In order to print glossaries easily
  As a user
  I need to provide users a different view to print the glossary contents

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |

  @javascript
  Scenario: Printer-friendly glossary view enabled
    Given the following "activity" exists:
      | course         | C1                        |
      | activity       | glossary                  |
      | name           | Test glossary name        |
      | intro          | Test glossary description |
      | allowprintview | 1                         |
    And I am on the "Test glossary name" "glossary activity" page logged in as student1
    When I add a glossary entry with the following data:
      | Concept | Just a test concept |
      | Definition | Concept definition |
    And I click on "Export entries" "button"
    And I click on "Print" "link"
    Then I should see "Just a test concept"

  @javascript
  Scenario: Printer-friendly glossary view disabled
    Given the following "activity" exists:
      | course         | C1                        |
      | activity       | glossary                  |
      | name           | Test glossary name        |
      | intro          | Test glossary description |
      | allowprintview | 0                         |
    And I am on the "Test glossary name" "glossary activity" page logged in as student1
    When I add a glossary entry with the following data:
      | Concept | Just a test concept |
      | Definition | Concept definition |
    And "//select[contains(concat(' ', normalize-space(@class), ' '), ' urlselect ')]" "xpath_element" should not exist
