@filter @filter_wiris @wiris_mathtype @3.x @3.x_filter @4.0 @4.0_filter @4.x @4.x_filter @5.x @5.x_filter @services @mtmoodle-15
Feature: Integration Services availability - Logged users
  In order to check access provider
  As an admin
  I must access the services if service provider is enabled

  Background:
    Given the following config values are set as admin:
      | config                  | value | plugin       |
      | access_provider_enabled | 1     | filter_wiris |
    And the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: MTMOODLE-15 - configurationjs service
    And I go to link "/filter/wiris/integration/configurationjs.php"
    Then I should see "\"editorEnabled\":true"
    And I should see "\"saveMode\":\"safeXml\""
    And "Forgotten your username" "text" should not exist

  @javascript
  Scenario: MTMOODLE-15 - createimage service
    And I go to link "/filter/wiris/integration/createimage.php?mml=<math><mo>x</mo></math>"
    Then "formula=5eacffd8c463696c5742b05a925cef04" "text" should exist
    And "Forgotten your username" "text" should not exist

  @javascript
  Scenario: MTMOODLE-15 - showimage service
    And I go to link "/filter/wiris/integration/showimage.php?mml=<math><mo>x</mo></math>"
    Then "\"status\":\"ok\"" "text" should exist
    And "!--MathML:" "text" should exist
    And "\"format\":\"svg\"" "text" should exist
    And "Forgotten your username" "text" should not exist

  @javascript
  Scenario: MTMOODLE-15 - services service
    And I go to link "/filter/wiris/integration/service.php?service=mathml2accessible&mml=<math xmlns='http://www.w3.org/1998/Math/MathML'><mn>1</mn><mo>+</mo><mn>2</mn></math>"
    Then "{\"result\":{\"text\":\"1 2\"},\"status\":\"ok\"}" "text" should exist
    And "Forgotten your username" "text" should not exist

  @javascript
  Scenario: MTMOODLE-15 - test service
    And I go to link "/filter/wiris/integration/test.php"
    Then "MathType integration test page" "text" should exist
    And "Forgotten your username" "text" should not exist
    And "OK" "text" should exist
    And "ERROR" "text" should not exist
    And "KO" "text" should not exist
    And "DISABLED" "text" should not exist
