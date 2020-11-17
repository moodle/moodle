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
    And I open the action menu in "John Doe" "table_row"
    And I should see "View the request"
    And I should see "Mark as complete"
    And I choose "View the request" in the open action menu
    And I should see "Hi PO! Can others access my information on your site?"
    And I press "Mark as complete"
    And I wait until the page is ready
    And I should see "Complete" in the "John Doe" "table_row"
    And I open the action menu in "John Doe" "table_row"
    And I should see "View the request"
    But I should not see "Mark as complete"
    And I press the escape key
    And I open the action menu in "Jane Doe" "table_row"
    And I choose "Mark as complete" in the open action menu
    And I should see "Do you really want to mark this user enquiry as complete?"
    And I press "Mark as complete"
    And I wait until the page is ready
    And I should see "Complete" in the "Jane Doe" "table_row"
    And I open the action menu in "Jane Doe" "table_row"
    And I should see "View the request"
    But I should not see "Mark as complete"

  @javascript
  Scenario: Bulk accepting requests
    Given I log in as "student1"
    And I follow "Profile" in the user menu
    And I should see "Data requests"
    And I click on "Data requests" "link"
    And I should see "New request"
    And I click on "New request" "link"
    And I should see "Type"
    And I should see "Comments"
    And I set the field "Type" to "Export all of my personal data"
    And I set the field "Comments" to "Comment1"
    And I press "Save changes"
    And I should see "Your request has been submitted to the privacy officer"
    And I log out
    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I should see "Data requests"
    And I click on "Data requests" "link"
    And I should see "New request"
    And I click on "New request" "link"
    And I should see "Type"
    And I should see "Comments"
    And I set the field "Type" to "Export all of my personal data"
    And I set the field "Comments" to "Comment2"
    And I press "Save changes"
    And I should see "Your request has been submitted to the privacy officer"
    And I log out
    And I trigger cron
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I should see "Comment1" in the "John Doe" "table_row"
    And I should see "Awaiting approval" in the "John Doe" "table_row"
    And I should see "Comment2" in the "Jane Doe" "table_row"
    And I should see "Awaiting approval" in the "Jane Doe" "table_row"
    And I click on ".selectrequests" "css_element" in the "John Doe" "table_row"
    And I click on ".selectrequests" "css_element" in the "Jane Doe" "table_row"
    And I set the field with xpath "//select[@id='bulk-action']" to "Approve"
    And I press "Confirm"
    And I should see "Approve requests"
    And I should see "Do you really want to bulk approve the selected data requests?"
    When I press "Approve requests"
    Then I should see "Approved" in the "John Doe" "table_row"
    And I should see "Approved" in the "Jane Doe" "table_row"

  @javascript
  Scenario: Bulk denying requests
    Given I log in as "student1"
    And I follow "Profile" in the user menu
    And I should see "Data requests"
    And I click on "Data requests" "link"
    And I should see "New request"
    And I click on "New request" "link"
    And I should see "Type"
    And I should see "Comments"
    And I set the field "Type" to "Export all of my personal data"
    And I set the field "Comments" to "Comment1"
    And I press "Save changes"
    And I should see "Your request has been submitted to the privacy officer"
    And I log out
    And I log in as "student2"
    And I follow "Profile" in the user menu
    And I should see "Data requests"
    And I click on "Data requests" "link"
    And I should see "New request"
    And I click on "New request" "link"
    And I should see "Type"
    And I should see "Comments"
    And I set the field "Type" to "Export all of my personal data"
    And I set the field "Comments" to "Comment2"
    And I press "Save changes"
    And I should see "Your request has been submitted to the privacy officer"
    And I log out
    And I trigger cron
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Data requests" in site administration
    And I should see "Comment1" in the "John Doe" "table_row"
    And I should see "Awaiting approval" in the "John Doe" "table_row"
    And I should see "Comment2" in the "Jane Doe" "table_row"
    And I should see "Awaiting approval" in the "Jane Doe" "table_row"
    And I click on ".selectrequests" "css_element" in the "John Doe" "table_row"
    And I click on ".selectrequests" "css_element" in the "Jane Doe" "table_row"
    And I set the field with xpath "//select[@id='bulk-action']" to "Deny"
    And I press "Confirm"
    And I should see "Deny requests"
    And I should see "Do you really want to bulk deny the selected data requests?"
    When I press "Deny requests"
    Then I should see "Rejected" in the "John Doe" "table_row"
    And I should see "Rejected" in the "Jane Doe" "table_row"
