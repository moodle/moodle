@tool @tool_behat
Feature: Transform steps arguments
  In order to write tests with complex nasty arguments
  As a tests writer
  I need to apply some transformations to the steps arguments

  Background:
    Given I am on homepage
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And I log in as "admin"
    And I follow "Admin User"
    And I follow "Edit profile"

  Scenario: Use nasty strings on steps arguments
    When I fill in "Surname" with "$NASTYSTRING1"
    And I fill in "Description" with "$NASTYSTRING2"
    And I fill in "City/town" with "$NASTYSTRING3"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And the "Surname" field should match "$NASTYSTRING1" value
    And the "City/town" field should match "$NASTYSTRING3" value

  Scenario: Use nasty strings on table nodes
    When I fill the moodle form with:
      | Surname | $NASTYSTRING1 |
      | Description | $NASTYSTRING2 |
      | City/town | $NASTYSTRING3 |
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And the "Surname" field should match "$NASTYSTRING1" value
    And the "City/town" field should match "$NASTYSTRING3" value

  Scenario: Use double quotes
    When I fill the moodle form with:
      | First name | va"lue1 |
      | Description | va\"lue2 |
    And I fill in "City/town" with "va\"lue3"
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And the "First name" field should match "va\"lue1" value
    And the "Description" field should match "va\\"lue2" value
    And the "City/town" field should match "va\"lue3" value

  @javascript
  Scenario: Nasty strings with other contents
    When I fill in "First name" with "My Firstname $NASTYSTRING1"
    And I fill the moodle form with:
      | Surname | My Surname $NASTYSTRING2 |
    And I press "Update profile"
    And I follow "Edit profile"
    Then I should not see "NASTYSTRING"
    And I should see "My Firstname"
    And I should see "My Surname"
    And the "First name" field should match "My Firstname $NASTYSTRING1" value
    And the "Surname" field should match "My Surname $NASTYSTRING2" value
