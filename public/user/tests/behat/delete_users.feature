@core @core_user
Feature: Deleting users
  In order to manage a Moodle site
  As an admin
  I need to be able to delete users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | one@example.com   |
      | user2    | User      | Two      | two@example.com   |
      | user3    | User      | Three    | three@example.com |
      | user4    | User      | Four     | four@example.com  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | user1    | C1     | student        |
      | user2    | C1     | student        |
      | user3    | C1     | student        |
      | user4    | C1     | student        |
    And the following config values are set as admin:
      | messaging | 1 |

  @javascript
  Scenario: Deleting one user at a time
    When I log in as "admin"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    And the "Available" select box should contain "User Four"
    And I set the field "Available" to "User Four"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I should see "Are you absolutely sure you want to completely delete the user User Four, including their enrolments, activity and other user data?"
    And I press "Yes"
    And I should see "Changes saved"
    And I press "Continue"
    Then the "Available" select box should not contain "User Four"
    And the "Available" select box should contain "User One"

  @javascript
  Scenario: Deleting more than one user at a time
    When I log in as "admin"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    And I set the field "Available" to "User Four"
    And I press "Add to selection"
    And I set the field "Available" to "User Three"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I should see "Are you absolutely sure you want to completely delete the user User Four, User Three, including their enrolments, activity and other user data?"
    And I press "Yes"
    And I should see "Changes saved"
    And I press "Continue"
    Then the "Available" select box should not contain "User Four"
    And the "Available" select box should not contain "User Three"
    And the "Available" select box should contain "User One"

  @javascript
  Scenario: Deleting users from bulk actions in the user list
    When I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "User Four" "checkbox"
    And I click on "User Three" "checkbox"
    And I set the field "Bulk user actions" to "Delete"
    And I should see "Are you absolutely sure you want to completely delete the user User Four, User Three, including their enrolments, activity and other user data?"
    And I press "Yes"
    And I should see "Changes saved"
    And I press "Continue"
    And I should see "Browse list of users"
    And I should not see "User Four"
    And I should not see "User Three"
    And I should see "User One"

  @javascript @core_message
  Scenario: Deleting users who have unread messages sent or received
    When I log in as "user1"
    And I send "Message 1 from user1 to user2" message to "User Two" user
    And I log out
    And I log in as "user3"
    And I send "Message 2 from user3 to user4" message to "User Four" user
    And I log out
    And I log in as "admin"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    And I set the field "Available" to "User One"
    And I press "Add to selection"
    And I set the field "Available" to "User Four"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I press "Yes"
    Then I should see "Changes saved"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    And I set the field "Available" to "User Two"
    And I press "Add to selection"
    And I set the field "Available" to "User Three"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I press "Yes"
    And I should see "Changes saved"
    And I press "Continue"
    And the "Available" select box should not contain "User Four"
    And the "Available" select box should not contain "User Three"
    And the "Available" select box should not contain "User One"
    And the "Available" select box should not contain "User Two"

  @javascript
  Scenario: Deleting a bulked user
    When I log in as "admin"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    And I set the field "Available" to "User Two"
    And I press "Add to selection"
    And I set the field "Available" to "User One"
    And I press "Add to selection"
    Then I should see "User One"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I press "Delete" action in the "User One" report row
    And I click on "Delete" "button" in the "Delete user" "dialogue"
    And I navigate to "Users > Accounts > Bulk user actions" in site administration
    Then I should not see "User One"
