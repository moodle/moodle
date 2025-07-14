@editor @editor_tiny @tiny_aiplacement
Feature: Generate text using AI
  In order to generate text using AI, as a teacher, I need to be able to use the AI text generation feature in the TinyMCE editor

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | teacher1 | Teacher   | 1        | t1@example.com |
      | teacher2 | Teacher   | 2        | t2@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
      | Course 2 | C2        | topics |
    And the following "roles" exist:
      | name                   | shortname | description      | archetype      |
      | Custom editing teacher | custom1   | My custom role 1 | editingteacher |
      | Custom teacher         | custom2   | My custom role 2 | editingteacher |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | custom1        |
      | teacher2 | C1     | custom2        |
      | teacher1 | C2     | editingteacher |
    And the following "activities" exist:
      | activity | name      | intro     | introformat | course | content     | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | PageContent | 1             | 1        |
      | page     | PageName2 | PageDesc2 | 1           | C2     | PageContent | 1             | 2        |
    Given the following "permission overrides" exist:
      | capability                         | permission | role    | contextlevel | reference |
      | aiplacement/editor:generate_image | Prohibit   | user    | System       |           |
      | aiplacement/editor:generate_text  | Prohibit   | custom2 | Course       | C1        |
    And the following "core_ai > ai providers" exist:
      |provider          | name   | enabled | apikey | orgid |
      |aiprovider_openai | openai | 1       | 123    | abc   |
    And I enable "editor" "aiplacement" plugin
    And I log in as "admin"

  @javascript
  Scenario: Text generation using AI is not available if placement is not enabled
    Given I disable "editor" "aiplacement" plugin
    When I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then "AI generate text" button should not exist in the "Description" TinyMCE editor
    And I enable "editor" "aiplacement" plugin
    And I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: Text generation using AI is not available if provider is not enabled
    Given I "disable" the ai provider with name "openai"
    When I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then "AI generate text" button should not exist in the "Description" TinyMCE editor
    And I "enable" the ai provider with name "openai"
    And I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: Text generation using AI is not available if provider action is not enabled
    Given I set the following action configuration for ai provider with name "openai":
      | action          | enabled |
      | generate_text   | 0       |
    When I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then "AI generate text" button should not exist in the "Description" TinyMCE editor
    And I set the following action configuration for ai provider with name "openai":
      | action          | enabled |
      | generate_text   | 1      |
    And I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: Text generation using AI is not available if placement action is not enabled
    Given the following config values are set as admin:
      | generate_text |  | aiplacement_editor |
    When I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then "AI generate text" button should not exist in the "Description" TinyMCE editor
    And the following config values are set as admin:
      | generate_text | 1 | aiplacement_editor |
    And I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: Text generation using AI is not available if provider action is not enabled and placement action is enabled
    Given the following config values are set as admin:
      | generate_text |  | aiplacement_editor |
    And I set the following action configuration for ai provider with name "openai":
      | action          | enabled |
      | generate_text   | 0       |
    When I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    Then "AI generate text" button should not exist in the "Description" TinyMCE editor
    And the following config values are set as admin:
      | generate_text | 1 | aiplacement_editor |
    And I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should not exist in the "Description" TinyMCE editor
    And I set the following action configuration for ai provider with name "openai":
      | action          | enabled |
      | generate_text   | 1       |
    And I am on the "PageName2" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should exist in the "Description" TinyMCE editor

  @javascript
  Scenario: Text generation using AI is not available if the user does not have permission
    When I am on the "PageName1" "page activity" page logged in as teacher2
    And I navigate to "Settings" in current page administration
    Then "AI generate text" button should not exist in the "Description" TinyMCE editor
    When I am on the "PageName1" "page activity" page logged in as teacher1
    And I navigate to "Settings" in current page administration
    And "AI generate text" button should exist in the "Description" TinyMCE editor
    And I click on the "AI generate text" button for the "Description" TinyMCE editor
    And I should see "Welcome to the new AI feature!" in the "AI usage policy" "dialogue"
    And I click on "Accept and continue" "button" in the "AI usage policy" "dialogue"
    And I should see "Describe the text you want AI to create"
