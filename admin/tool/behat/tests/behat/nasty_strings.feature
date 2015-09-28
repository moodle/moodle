@tool @tool_behat
Feature: Transform steps arguments
  In order to write tests with complex nasty arguments
  As a tests writer
  I need to apply some transformations to the steps arguments

  Background:
    Given I am on site homepage
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Preferences" in the user menu
    And I follow "Edit profile"

  Scenario: Use nasty strings on steps arguments
    When I set the field "Surname" to "$NASTYSTRING1"
    And I set the field "Description" to "$NASTYSTRING2"
    And I set the field "City/town" to "$NASTYSTRING3"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And the field "Surname" matches value "$NASTYSTRING1"
    And the field "City/town" matches value "$NASTYSTRING3"

  Scenario: Use nasty strings on table nodes
    When I set the following fields to these values:
      | Surname | $NASTYSTRING1 |
      | Description | $NASTYSTRING2 |
      | City/town | $NASTYSTRING3 |
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And the field "Surname" matches value "$NASTYSTRING1"
    And the field "City/town" matches value "$NASTYSTRING3"

  Scenario: Use double quotes
    When I set the following fields to these values:
      | First name | va"lue1 |
      | Description | va\"lue2 |
    And I set the field "City/town" to "va\"lue3"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And the field "First name" matches value "va\"lue1"
    And the field "Description" matches value "va\\"lue2"
    And the field "City/town" matches value "va\"lue3"

  Scenario: Nasty strings with other contents
    When I set the field "First name" to "My Firstname $NASTYSTRING1"
    And I set the following fields to these values:
      | Surname | My Surname $NASTYSTRING2 |
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And I should see "My Firstname"
    And I should see "My Surname"
    And the field "First name" matches value "My Firstname $NASTYSTRING1"
    And the field "Surname" matches value "My Surname $NASTYSTRING2"
