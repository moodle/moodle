@tool @tool_brickfield
Feature: Brickfield activityresults

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | description               |
      | Course 1 | C1        | 0        | <b> Description text </b> |
    And the following "activities" exist:
      | activity | name      | intro                              | course | idnumber |
      | label    | Label one | <b>Bold text is bold.</b>          | C1     | id001    |
      | label    | Label two | <a href="modle.org">Click here</a> | C1     | id002    |
    And the following config values are set as admin:
      | analysistype | 1 | tool_brickfield |

  @javascript
  Scenario: Test the Brickfield accessibility tool plugin
    Given I log in as "admin"
    And I navigate to "Plugins > Admin tools > Accessibility > Brickfield registration" in site administration
    And I set the field "id_key" to "123456789012345678901234567890ab"
    And I set the field "id_hash" to "ab123456789012345678901234567890"
    And I press "Activate"
    Then I should see "Your accessibility toolkit is functional while being validated."
    And I navigate to "Plugins > Admin tools > Accessibility > Reports" in site administration
    And I press "Submit for analysis"
    Then I should see "The global (course independent) content has been scheduled for analysis."
    And I am on "Course 1" course homepage
    And I navigate to "Accessibility toolkit" in current page administration
    And I press "Submit for analysis"
    Then I should see "This course has been scheduled for analysis."
    And I run the scheduled task "\tool_brickfield\task\process_analysis_requests"
    And I run the scheduled task "\tool_brickfield\task\bulk_process_courses"
    And I run the scheduled task "\tool_brickfield\task\bulk_process_caches"
    And I navigate to "Accessibility toolkit" in current page administration
    And I should see "Error details: course Course 1"
    And I should see "The overall page content length"
    And I should see "Bold (b) elements should not be used"
    And I should see "Link text should be descriptive"
    And I follow "Activity breakdown"
    Then I should see "Results per activity: course Course 1"
    And I navigate to "Plugins > Admin tools > Accessibility > Brickfield registration" in site administration
    And I set the field "id_key" to "123456789012345678901234567890ab"
    And I set the field "id_hash" to "ab123456789012345678901234567890"
    And I press "Activate"
    And I navigate to "Plugins > Admin tools > Accessibility > Reports" in site administration
    And I should see "Error details: all reviewed courses (2 courses)"
    And I follow "Activity breakdown"
    Then I should see "Results per activity: all reviewed courses (2 courses)"
    And I follow "Content types"
    Then I should see "Results per content type: all reviewed courses (2 courses)"
