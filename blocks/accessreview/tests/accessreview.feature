@block @block_accessreview @javascript
Feature: Block accessreview
  In order to overview accessibility information on my course
  As a manager
  I can add the accessreview block in a course

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |

  Scenario: View accessreview block on a course
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility Review" block
    Then I should see "Accessibility Review"
    And I should see "Your accessibility toolkit needs to be registered."

  Scenario: Hide/show accessreview view
    Given I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    When I add the "Accessibility Review" block
    Then I should see "Accessibility Review"
    And I should see "Your accessibility toolkit needs to be registered."
    And I click on "Actions menu" "menuitem" in the "Accessibility Review" "block"
    And I follow "Hide Accessibility Review block"
    And I should not see "Your accessibility toolkit needs to be registered."
    And I click on "Actions menu" "menuitem" in the "Accessibility Review" "block"
    And I follow "Show Accessibility Review block"
    And I should see "Your accessibility toolkit needs to be registered."
