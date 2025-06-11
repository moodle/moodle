@filter @filter_wiris @wiris_mathtype @filter_settings @mtmoodle-10
Feature: Filter settings page
  In order to check if MathType filters can be configurated
  As an admin
  I need to access the filters page in site administration

  Background:
    Given the "wiris" filter is "on"
    And I log in as "admin"

  @javascript @4.0 @4.0_filter @4.x @4.x_filter @5.x @5.x_filter
    Scenario: MTMOODLE-10 - Check that different categories of settings exist
    And I navigate to "Plugins > Filters" in site administration
    Then "Common filter settings" "text" should exist
    Then "Convert URLs into links and images" "text" should exist
    And "Display emoticons as images" "text" should exist
    And "Display H5P" "text" should exist
    And "MathJax" "text" should exist
    And "MathType by WIRIS" "text" should exist
    And "Wiris Quizzes settings" "text" should exist
    And "Multimedia plugins" "text" should exist

  @javascript @3.x @3.x_filter 
  Scenario: MTMOODLE-10 - Check that different categories of settings exist
    And I navigate to "Plugins > Filters" in site administration
    Then "Common filter settings" "text" should exist
    And "Display H5P" "text" should exist
    And "MathJax" "text" should exist
    And "MathType by WIRIS" "text" should exist
    And "Wiris Quizzes settings" "text" should exist
    And "Multimedia plugins" "text" should exist