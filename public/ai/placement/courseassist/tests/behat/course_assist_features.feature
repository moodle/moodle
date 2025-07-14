@core_ai @aiplacement_courseassist
Feature: AI course assist features
  In order to use AI course assist features
  As a user
  I need to access a course and choose the desired AI feature

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
      | teacher2 | Teacher   | 2        | t2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "roles" exist:
      | name                   | shortname | description      | archetype      |
      | Custom editing teacher | custom1   | My custom role 1 | editingteacher |
      | Custom teacher         | custom2   | My custom role 2 | editingteacher |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | teacher1 | C1     | custom1 |
      | teacher2 | C1     | custom2 |
    And the following "activities" exist:
      | activity | name      | intro     | introformat | course | content     | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | PageContent | 1             | 1        |
    And the following "permission overrides" exist:
      | capability                              | permission | role    | contextlevel | reference |
      | aiplacement/courseassist:summarise_text | Prohibit   | custom2 | Course       | C1        |
    And the following "core_ai > ai providers" exist:
      | provider          | name            | enabled | apikey | orgid |
      | aiprovider_openai | OpenAI API test | 1       | 123    | abc   |
    And the following config values are set as admin:
      | enabled | 1 | aiplacement_courseassist |

  Scenario: AI features dropdown is visible when more than one feature is enabled
    When I am on the "PageName1" "page activity" page logged in as teacher1
    Then "AI features" "button" should exist
    # Check nested buttons exist too.
    And "Summarise" "button" should exist
    And "Explain" "button" should exist

  Scenario: AI features dropdown is not visible when only one feature is enabled
    Given I set the following action configuration for ai provider with name "OpenAI API test":
      | action         | enabled |
      | explain_text   | 0       |
      | summarise_text | 1       |
    When I am on the "PageName1" "page activity" page logged in as teacher1
    Then "AI features" "button" should not exist
    And "Explain" "button" should not exist
    # Only the summarise button should exist.
    And "Summarise" "button" should exist

  Scenario: AI features are not available if placement is not enabled
    Given the following config values are set as admin:
      | enabled | | aiplacement_courseassist |
    When I am on the "PageName1" "page activity" page logged in as teacher1
    Then "AI features" "button" should not exist

  Scenario: AI features are not available if provider action is not enabled
    Given I set the following action configuration for ai provider with name "OpenAI API test":
      | action         | enabled |
      | explain_text   | 0       |
      | summarise_text | 0       |
    When I am on the "PageName1" "page activity" page logged in as teacher1
    Then "AI features" "button" should not exist

  Scenario: AI features are not available if placement action is not enabled
    Given I set the following action configuration for ai provider with name "OpenAI API test":
      | action         | enabled |
      | explain_text   | 0       |
      | summarise_text | 0       |
    When I am on the "PageName1" "page activity" page logged in as teacher1
    Then "AI features" "button" should not exist

  Scenario: AI features are not available if the user does not have permission
    When I am on the "PageName1" "page activity" page logged in as teacher2
    Then "AI features" "button" should not exist

  @javascript
  Scenario: I can view the AI drawer contents using the AI features dropdown
    Given I am on the "PageName1" "page activity" page logged in as teacher1
    When I click on "AI features" "button"
    And I click on "Summarise" "button"
    Then I should see "Welcome to the new AI feature!" in the ".ai-drawer" "css_element"
