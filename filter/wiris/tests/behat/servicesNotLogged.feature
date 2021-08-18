@filter @filter_wiris
Feature: Check access provider for non logged users
In order to check access provider
As an logged user
I can't access the services if service provider is enablabed

  Background:
    Given the following config values are set as admin:
      | config | value | plugin |
      | access_provider_enabled | 1 | filter_wiris |

  @javascript
  Scenario: configurationjs.php
    And I go to link "/filter/wiris/integration/configurationjs.php?lang=en"
    Then "editorEnabled" "text" should not exist
    And "Forgotten your username" "text" should exist

  @javascript
  Scenario: createimage.php
    And I go to link "/filter/wiris/integration/createimage.php?mml=<math><mo>x</mo></math>&lang=en"
    Then "formula" "text" should not exist
    And "Forgotten your username" "text" should exist

  @javascript
  Scenario: showimage.php
    And I go to link "/filter/wiris/integration/showimage.php?mml=<math><mo>x</mo></math>&lang=en"
    Then "status" "text" should not exist
    And "Forgotten your username" "text" should exist

  @javascript
  Scenario: service.php
    And I go to link "/filter/wiris/integration/service.php?service=mathml2accessible&mml=<math xmlns='http://www.w3.org/1998/Math/MathML'><mn>1</mn><mo>+</mo><mn>2</mn></math>&lang=en"
    Then "status" "text" should not exist
    And "Forgotten your username" "text" should exist

  @javascript
  Scenario: test.php
    And I go to link "/filter/wiris/integration/test.php?lang=en"
    Then "MathType integration test page" "text" should not exist
    And "Forgotten your username" "text" should exist
