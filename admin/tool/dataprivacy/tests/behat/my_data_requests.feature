@tool @tool_dataprivacy
Feature: Manage my own data requests
  In order to manage my own data requests
  As a user
  I need to be able to view and cancel all my data requests

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | student1 | Student   | 1        | s1@example.com |
    And the following config values are set as admin:
      | contactdataprotectionofficer | 1 | tool_dataprivacy |

  @javascript
  Scenario: Cancel my own data request
    Given I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Contact the privacy officer" "link"
    And I set the field "Message" to "Hello DPO!"
    And I click on "Send" "button" in the "Contact the privacy officer" "dialogue"
    And I should see "Your request has been submitted to the privacy officer"
    When I click on "Data requests" "link"
    And I open the action menu in "Hello DPO!" "table_row"
    And I choose "Cancel" in the open action menu
    And I click on "Cancel request" "button" in the "Cancel request" "dialogue"
    Then I should see "Cancelled" in the "Hello DPO!" "table_row"
