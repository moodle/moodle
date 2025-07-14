@tool @tool_dataprivacy
Feature: Contact the privacy officer
  As a user
  In order to reach out to the site's privacy officer
  I need to be able to contact the site's privacy officer in Moodle

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | student1 | Student   | 1        | s1@example.com |

  @javascript
  Scenario: Contacting the privacy officer
    Given the following config values are set as admin:
      | contactdataprotectionofficer | 1 | tool_dataprivacy |
    When I log in as "student1"
    And I follow "Profile" in the user menu
    And I click on "Contact the privacy officer" "link"
    And I set the field "Message" to "Hello DPO!"
    And I click on "Send" "button" in the "Contact the privacy officer" "dialogue"
    Then I should see "Your request has been submitted to the privacy officer"
    And I click on "Data requests" "link"
    And I should see "Hello DPO!" in the "General enquiry" "table_row"

  Scenario: Contacting the privacy officer when not enabled
    When I log in as "student1"
    And I follow "Profile" in the user menu
    Then "Contact the privacy officer" "link" should not exist
