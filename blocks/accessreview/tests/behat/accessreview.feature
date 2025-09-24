@block @block_accessreview @javascript
Feature: Block accessreview
  In order to overview accessibility information on my course
  As a manager
  I can add the accessreview block in a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following config values are set as admin:
      | analysistype | 1 | tool_brickfield |

  Scenario: View accessreview block on a course
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility review" block
    Then I should see "Accessibility review"
    And I should see "Your accessibility toolkit needs to be registered."

  Scenario: Hide/show accessreview view
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility review" block
    Then I should see "Accessibility review"
    And I should see "Your accessibility toolkit needs to be registered."
    And I click on "Actions menu" "menuitem" in the "Accessibility review" "block"
    And I follow "Hide Accessibility review block"
    And I should not see "Your accessibility toolkit needs to be registered."
    And I click on "Actions menu" "menuitem" in the "Accessibility review" "block"
    And I follow "Show Accessibility review block"
    And I should see "Your accessibility toolkit needs to be registered."
