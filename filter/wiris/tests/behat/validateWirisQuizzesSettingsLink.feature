@filter @filter_wiris @wiris_mathtype @3.x @3.x_filter @4.0 @4.0_filter @4.x @4.x_filter @filter_settings @mtmoodle-26
Feature: Check WirisQuizzes settings link
  In order to check the WirisQuizzes settings link redirects correctly
  As an admin
  I need to access the filters page in site administration

  Background:
    Given the "wiris" filter is "on"
    And I log in as "admin"

  @javascript
  Scenario: MTMOODLE-26 - Check the WirisQuizzes settings link redirects correctly
    And I navigate to "Plugins > Filters" in site administration
    And I follow "Wiris Quizzes settings"
    Then "Connection settings" "text" should exist
    And "Compatibility settings" "text" should exist
    And "Troubleshooting" "text" should exist
