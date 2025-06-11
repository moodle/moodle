@filter @filter_wiris @wiris_mathtype @3.x @3.x_filter @4.0 @4.0_filter @4.x @4.x_filter @5.x @5.x_filter @filter_settings @mtmoodle-29
Feature: Check filters settings save correctly
  In order to check if MathType settings are being saved correctly
  As an admin
  I need to access the filters page in site administration

  @javascript
  Scenario: MTMOODLE-29 - Validate settings are saved correctly
    Given the "wiris" filter is "on"
    And I log in as "admin"
    And I navigate to "Plugins > MathType by WIRIS" in site administration
    And I set the following fields to these values:
      | Service host | www.wipis.net |
    Then "Service host" input value is equal to "wiris.net"
    And I press "Save changes"
    Then "Service host" input value is equal to "wipis.net"
