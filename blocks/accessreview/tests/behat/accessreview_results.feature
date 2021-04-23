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
      | activity | name      | intro                     | course | idnumber |
      | label    | Label one | <b>Bold text is bold.</b> | C1     | id001    |
    And I run the scheduled task "\tool_brickfield\task\bulk_process_courses"

  Scenario: View accessreview block results on a course
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility Review" block
    Then I should see "Accessibility Review"
    And I should not see "No accessibility results data was found."
    And I should see "Image"
    And I should see "Layout"
    And I should see "Link"
    And I should see "Media"
    And I should see "Table"
    And I should see "Text"
    And I should see "View"
    And I should see "Toggle highlighting"

  Scenario: Toggle highlighting on/off
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility Review" block
    Then I should see "Toggle highlighting"
    And ".block_accessreview_view" "css_element" should be visible
    And I click on "Toggle highlighting" "text"
    And ".block_accessreview_view" "css_element" should not be visible
