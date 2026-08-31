@aiprovider_anthropic @aiplacement_courseassist @core_ai
Feature: Use the Anthropic Claude API provider for AI course assist features
  In order to use AI features backed by the Anthropic Claude provider
  As a teacher
  I need to see the AI features available on a course page

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | intro     | introformat | course | content     | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | PageContent | 1             | 1        |
    And the following "core_ai > ai providers" exist:
      | provider             | name             | enabled | apikey           |
      | aiprovider_anthropic | Anthropic test   | 1       | test_api_key_123 |
    And the following config values are set as admin:
      | enabled | 1 | aiplacement_courseassist |

  @javascript
  Scenario: AI features are available on a course page when the Anthropic Claude provider is active
    Given I am on the "PageName1" "page activity" page logged in as teacher1
    Then "AI features" "button" should exist
    And "Summarise" "button" should exist
    And "Explain" "button" should exist

  @javascript
  Scenario: I can open the AI drawer for a feature backed by the Anthropic Claude provider
    Given I am on the "PageName1" "page activity" page logged in as teacher1
    When I click on "AI features" "button"
    And I click on "Summarise" "button"
    Then I should see "Welcome to the new AI feature!" in the ".ai-drawer" "css_element"
