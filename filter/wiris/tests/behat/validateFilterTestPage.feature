@filter @filter_wiris @wiris_mathtype @3.x @3.x_filter @4.0 @4.0_filter @4.x @4.x_filter @test_page @mtmoodle-11
Feature: Test page
  In order to check the test page
  As a user
  I must see not error messages on test page

  Background:
    Given the following config values are set as admin:
      | config  | value        | plugin      |
      | toolbar | math = wiris | editor_atto |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "course enrolments" exist:
      | user  | course | role           |
      | admin | C1     | editingteacher |
    And the "wiris" filter is "on"

  Scenario: MTMOODLE-11 - Validate filter test page
    And I go to link "/filter/wiris/info.php"
    And I wait "1" seconds
    Then "Error" "text" should not exist
    And "Error" "text" should not exist
    And "KO" "text" should not exist
    And "DISABLED" "text" should not exist
    And "SUCCESS" "text" should exist
