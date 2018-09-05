@core @core_admin
Feature: Manage tokens
  In order to manage webservice usage
  As an admin
  I need to be able to create and delete tokens

  Background:
    Given the following "users" exist:
    | username  | password  | firstname | lastname |
    | testuser  | testuser  | Joe | Bloggs |
    | testuser2 | testuser2 | TestFirstname | TestLastname |
    And I log in as "admin"
    And I am on site homepage

  @javascript
  Scenario: Add & delete a token
    Given I navigate to "Plugins > Web services > Manage tokens" in site administration
    And I follow "Add"
    And I set the field "User" to "Joe Bloggs"
    And I set the field "IP restriction" to "127.0.0.1"
    When I press "Save changes"
    Then I should see "Joe Bloggs"
    And I should see "127.0.0.1"
    And I follow "Delete"
    And I press "Delete"
    And I should not see "Joe Bloggs"
