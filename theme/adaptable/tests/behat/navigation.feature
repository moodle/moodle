@theme @theme_adaptable
Feature: Basic navigation scenarios through Moodle.

  Background: User "admin" logs into Moodle and then logs out
    And I log in as "admin"
    Then I log out

  Scenario: Basic navigation without JavaScript

  @javascript
  Scenario: Basic navigation with JavaScript