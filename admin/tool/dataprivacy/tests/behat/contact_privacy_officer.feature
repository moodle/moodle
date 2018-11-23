@tool @tool_dataprivacy
Feature: Contact the privacy officer
  As a user
  In order to reach out to the site's privacy officer
  I need to be able to contact the site's privacy officer in Moodle

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | student1 | Student   | 1        | s1@example.com |
    And I log in as "admin"
    And I set the following administration settings values:
      | contactdataprotectionofficer | 1 |
    And I log out

  @javascript
  Scenario: Contacting the privacy officer
    Given I log in as "student1"
    And I follow "Profile" in the user menu
    And I should see "Contact the privacy officer"
    And I click on "Contact the privacy officer" "link"
    And I set the field "Message" to "Hello DPO!"
    And I click on "Send" "button" in the "Contact the privacy officer" "dialogue"
    And I should see "Your request has been submitted to the privacy officer"
    And I click on "Data requests" "link"
    And I should see "Hello DPO!" in the "General inquiry" "table_row"
