@tool @tool_dataprivacy
Feature: Manage data requests
  As the privacy officer
  In order to address the privacy-related requests
  I need to be able to manage the data requests of the site's users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email          |
      | student1 | John      | Doe      | s1@example.com |
      | student2 | Jane      | Doe      | s2@example.com |
    And I log in as "admin"
    And I set the following administration settings values:
      | contactdataprotectionofficer | 1 |
    And I log out

  @javascript
  Scenario: Marking general enquiries as complete
    Given I log in as "student1"
    And I follow "Profile" in the user menu
    And I should see "Contact the privacy officer"
    And I click on "Contact the privacy officer" "link"
    And I set the field "Message" to "Hi PO! Can others access my information on your site?"
    And I press "Send"
    And I should see "Your request has been submitted to the privacy officer"
    And I log out
    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I click on "Contact the privacy officer" "link"
    And I set the field "Message" to "Dear Mr. Privacy Officer, I'd like to know more about GDPR. Thanks!"
    And I press "Send"
    And I should see "Your request has been submitted to the privacy officer"
    And I log out
    When I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    Then I should see "Hi PO!" in the "John Doe" "table_row"
    And I should see "Dear Mr. Privacy Officer" in the "Jane Doe" "table_row"
    And I click on "Actions" "link" in the "John Doe" "table_row"
    And I should see "View the request"
    And I should see "Mark as complete"
    And I choose "View the request" in the open action menu
    And I should see "Hi PO! Can others access my information on your site?"
    And I press "Mark as complete"
    And I wait until the page is ready
    And I should see "Complete" in the "John Doe" "table_row"
    And I click on "Actions" "link" in the "John Doe" "table_row"
    And I should see "View the request"
    But I should not see "Mark as complete"
    And I press key "27" in ".moodle-actionmenu" "css_element"
    And I click on "Actions" "link" in the "Jane Doe" "table_row"
    And I choose "Mark as complete" in the open action menu
    And I should see "Do you really want to mark this user enquiry as complete?"
    And I press "Mark as complete"
    And I wait until the page is ready
    And I should see "Complete" in the "Jane Doe" "table_row"
    And I click on "Actions" "link" in the "Jane Doe" "table_row"
    And I should see "View the request"
    But I should not see "Mark as complete"
