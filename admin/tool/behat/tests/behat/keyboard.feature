@tool_behat
Feature: Verify that keyboard steps work as expected
  In order to use behat step definitions
  As a test writer
  I need to verify that the keyboard steps work as expected

  @javascript
  Scenario: Typing keys into a field causes them to be input
    Given the following "users" exist:
      | username | email                        | firstname | lastname | password    |
      | saffronr | saffron.rutledge@example.com | Saffron   | Rutledge | flowerpower |
    Given I click on "Log in" "link"
    And I click on "Username" "field"
    When I type "saffronr"
    And I press the tab key
    And I type "flowerpower"
    And I press enter
    Then I should see "You are logged in as Saffron Rutledge"

  @javascript
  Scenario: Using tab changes focus to the next or previous field
    Given I click on "Log in" "link"
    And I click on "Username" "field"
    And the focused element is "Username" "field"
    When I press the tab key
    Then the focused element is "Password" "field"

    And I press the shift tab key
    And the focused element is "Username" "field"

#  TODO: Uncomment the following when MDL-66979 is integrated.
#  @javascript
#  Scenario: Using the arrow keys allows me to navigate through menus
#    Given the following "users" exist:
#      | username | email                        | firstname | lastname |
#      | saffronr | saffron.rutledge@example.com | Saffron   | Rutledge |
#    And I log in as "saffronr"
#    And I click on "Saffron Rutledge" "link" in the ".usermenu" "css_element"
#    When I press the up key
#    Then the focused element is "Log out" "link"

  @javascript
  Scenario: The escape key can be used to close a dialogue
    Given the following "course" exists:
     | fullname  | C1|
     | shortname | C1 |
    And I log in as "admin"
    And I am on "C1" course homepage
    And I navigate to course participants
    And I press "Enrol users"
    And "Enrol users" "dialogue" should be visible
    When I press the escape key
    Then "Enrol users" "dialogue" should not be visible
