@block @block_accessreview @javascript
Feature: Block accessreview results
  In order to overview accessibility information on my course
  As a manager
  I can add the accessreview block in a course after running the scheduled task

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "activities" exist:
      | activity | name      | intro                              | course | idnumber |
      | label    | Label one | <b>Bold text is bold.</b>          | C1     | id001    |
      | label    | Label two | <a href="modle.org">Click here</a> | C1     | id002    |
    And the following config values are set as admin:
      | analysistype | 1 | tool_brickfield |

  Scenario: View accessreview block results on a course
    Given I log in as "admin"
    And I navigate to "Plugins > Admin tools > Accessibility > Brickfield registration" in site administration
    And I set the field "id_key" to "123456789012345678901234567890ab"
    And I set the field "id_hash" to "ab123456789012345678901234567890"
    And I press "Activate"
    Then I should see "Your accessibility toolkit is functional while being validated."
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility review" block
    Then I should see "Accessibility review"
    And I press "Submit for analysis"
    Then I should see "This course has been scheduled for analysis."
    And I run the scheduled task "\tool_brickfield\task\process_analysis_requests"
    And I run the scheduled task "\tool_brickfield\task\bulk_process_courses"
    And I run the scheduled task "\tool_brickfield\task\bulk_process_caches"
    And I reload the page
    And I should see "Image" in the "Accessibility review" "block"
    And I should see "Layout" in the "Accessibility review" "block"
    And I should see "Link" in the "Accessibility review" "block"
    And I should see "Media" in the "Accessibility review" "block"
    And I should see "Table" in the "Accessibility review" "block"
    And I should see "Text" in the "Accessibility review" "block"
    # We created one link error above.
    And I should see "1" in the "Link" "table_row"
    # We created one text issue, and the standard Behat course generator creates another (too much content).
    And I should see "2" in the "Text" "table_row"
    And "View accessibility toolkit" "icon" should exist in the "Accessibility review" "block"
    And "Toggle accessibility heatmap" "icon" should exist in the "Accessibility review" "block"
    And "Download accessibility summary report" "icon" should exist in the "Accessibility review" "block"

  Scenario: Toggle highlighting on/off
    Given I log in as "admin"
    Given the following "user preferences" exist:
      | user  | preference                    | value |
      | admin | block_accessreviewtogglestate | 0     |
    And I navigate to "Plugins > Admin tools > Accessibility > Brickfield registration" in site administration
    And I set the field "id_key" to "123456789012345678901234567890ab"
    And I set the field "id_hash" to "ab123456789012345678901234567890"
    And I press "Activate"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility review" block
    And I press "Submit for analysis"
    And I run the scheduled task "\tool_brickfield\task\process_analysis_requests"
    And I run the scheduled task "\tool_brickfield\task\bulk_process_courses"
    And I run the scheduled task "\tool_brickfield\task\bulk_process_caches"
    And I reload the page
    And I click on "Toggle accessibility heatmap" "icon"
    And ".block_accessreview_view" "css_element" should be visible
    And I click on "Toggle accessibility heatmap" "icon"
    And ".block_accessreview_view" "css_element" should not be visible
