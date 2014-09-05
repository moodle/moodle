@core @core_user
Feature: Deleting users
  In order to manage a Moodle site
  As an admin
  I need to be able to delete users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | user1 | User | One   | one@asd.com |
      | user2 | User | Two   | two@asd.com |
      | user3 | User | Three | three@asd.com |
      | user4 | User | Four  | four@asd.com |

  @javascript
  Scenario: Deleting one user at a time
    When I log in as "admin"
    And I navigate to "Bulk user actions" node in "Site administration > Users > Accounts"
    And the "Available" select box should contain "User Four"
    And I set the field "Available" to "User Four"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I should see "Are you absolutely sure you want to completely delete User Four ?"
    And I press "Yes"
    And I should see "Changes saved"
    And I press "Continue"
    Then the "Available" select box should not contain "User Four"
    And the "Available" select box should contain "User One"

  @javascript
  Scenario: Deleting more than one user at a time
    When I log in as "admin"
    And I navigate to "Bulk user actions" node in "Site administration > Users > Accounts"
    And I set the field "Available" to "User Four"
    And I press "Add to selection"
    And I set the field "Available" to "User Three"
    And I press "Add to selection"
    And I set the field "id_action" to "Delete"
    And I press "Go"
    And I should see "Are you absolutely sure you want to completely delete User Four, User Three ?"
    And I press "Yes"
    And I should see "Changes saved"
    And I press "Continue"
    Then the "Available" select box should not contain "User Four"
    And the "Available" select box should not contain "User Three"
    And the "Available" select box should contain "User One"
