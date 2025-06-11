@filter @filter_wiris @wiris_mathtype @3.x @3.x_filter @4.0 @4.0_filter @4.x @4.x_filter @5.x @5.x_filter @filter_settings @connection_settings @mtmoodle-27
Feature: Filter Settings - Connection Settings - Service host
  In order to check the service host setting
  As an admin
  I should be able to access the test service depending if the service host exists

  @javascript
  Scenario: MTMOODLE-27 - Set an incorrect service host
    Given the "wiris" filter is "on"
    And I log in as "admin"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service host | www.wipis.net |
    And I press "Save changes"
    And I go to link "/filter/wiris/integration/test.php"
    Then "exception" "text" should exist

  @javascript
  Scenario: MTMOODLE-27 - Set a correct service host
    Given the "wiris" filter is "on"
    And I log in as "admin"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service host | www.wiris.net |
    And I press "Save changes"
    And I go to link "/filter/wiris/integration/test.php"
    Then "exception" "text" should not exist
