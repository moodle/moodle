@core @core_search
Feature: Show system information in the search interface
  In order to let users know if there are current problems with search
  As an admin
  I need to be able to show information on search pages

  Background:
    Given the following config values are set as admin:
      | enableglobalsearch | 1        |
      | searchengine       | simpledb |
    And I log in as "admin"

  @javascript
  Scenario: Information displays when enabled
    When the following config values are set as admin:
      | searchbannerenable | 1                                                                             |
      | searchbanner       | The search currently only finds frog-related content; we hope to fix it soon. |
    And I search for "toads" using the header global search box
    Then I should see "The search currently only finds frog-related content" in the ".notifywarning" "css_element"

  @javascript
  Scenario: Information does not display when not enabled
    When the following config values are set as admin:
      | searchbannerenable | 0                                                                             |
      | searchbanner       | The search currently only finds frog-related content; we hope to fix it soon. |
    And I search for "toads" using the header global search box
    Then I should not see "The search currently only finds frog-related content"
    And ".notifywarning" "css_element" should not exist

  @javascript
  Scenario: Information does not display when left blank
    When the following config values are set as admin:
      | searchbannerenable | 1 |
      | searchbanner       |   |
    And I search for "toads" using the header global search box
    Then ".notifywarning" "css_element" should not exist
