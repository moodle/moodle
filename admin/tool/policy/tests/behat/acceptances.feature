@tool @tool_policy
Feature: Viewing acceptances reports and accepting on behalf of other users
  In order to manage user acceptances
  As a manager
  I need to be able to view acceptances and accept on behalf of other users

  Background:
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    # This is required for now to prevent the overflow region affecting the action menus.
    And I change window size to "large"
    And the following policies exist:
      | Name                | Revision | Content    | Summary     | Status   |
      | This site policy    |          | full text2 | short text2 | active   |
      | This privacy policy |          | full text3 | short text3 | draft    |
    And the following "users" exist:
      | username | firstname | lastname | email           |
      | user1    | User      | One      | one@example.com |
      | user2    | User      | Two      | two@example.com |
      | manager  | Max       | Manager  | man@example.com |
    And the following "role assigns" exist:
      | user    | role           | contextlevel | reference |
      | manager | manager        | System       |           |
    And the following "courses" exist:
      | fullname | shortname |
      | Course1  | C1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | user1    | C1     | student |
      | user2    | C1     | student |

  Scenario: View acceptances made by users on their own, single policy
    When I log in as "user1"
    Then I should see "This site policy"
    And I should not see "Course overview"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I should see "Calendar"
    And I log out
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > User agreements" in site administration
    And "Accepted" "text" should exist in the "User One" "table_row"
    And "Accepted" "text" should exist in the "Max Manager" "table_row"
    And "Pending" "text" should exist in the "User Two" "table_row"

  Scenario: Agree on behalf of another user as a manager, single policy, javascript off
    Given I log in as "admin"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    When I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "1 of 4 (25%)" "link" in the "This site policy" "table_row"
    And I click on "Accept This site policy" "link" in the "User One" "table_row"
    Then I should see "Accepting policy"
    And I should see "User One"
    And I should see "This site policy"
    And I should see "I acknowledge that I have received a request to give consent on behalf of the above user(s)."
    And I set the field "Remarks" to "Consent received from a parent"
    And I press "Give consent"
    And "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Max Manager" "link" should exist in the "User One" "table_row"
    And "Consent received from a parent" "text" should exist in the "User One" "table_row"
    And "Pending" "text" should exist in the "User Two" "table_row"

  @javascript
  Scenario: Agree on behalf of another user as a manager, single policy, javascript on
    Given I log in as "admin"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    When I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I should see "Calendar"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "1 of 4 (25%)" "link" in the "This site policy" "table_row"
    And I click on "Accept This site policy" "link" in the "User One" "table_row"
    Then I should see "Give consent"
    And I should see "User One"
    And I should see "This site policy"
    And I should see "I acknowledge that I have received a request to give consent on behalf of the above user(s)."
    And I set the field "Remarks" to "Consent received from a parent"
    And I press "Give consent"
    And "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Max Manager" "link" should exist in the "User One" "table_row"
    And "Consent received from a parent" "text" should exist in the "User One" "table_row"
    And "Pending" "text" should exist in the "User Two" "table_row"

  Scenario: View acceptances made by users on their own, multiple policies
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "Set status to \"Active\"" "link" in the "This privacy policy" "table_row"
    And I press "Continue"
    And I log out
    When I log in as "user1"
    Then I should see "This site policy"
    And I press "Next"
    And I should see "This privacy policy"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I should see "Calendar"
    And I log out
    And I log in as "manager"
    And I press "Next"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > User agreements" in site administration
    And "Accepted" "text" should exist in the "User One" "table_row"
    And "Pending" "text" should not exist in the "User One" "table_row"
    And "Accepted" "text" should exist in the "Max Manager" "table_row"
    And "Pending" "text" should exist in the "User Two" "table_row"
    And "Accepted" "text" should not exist in the "User Two" "table_row"
    And I click on "Details" "link" in the "User One" "table_row"
    And "Accepted" "text" should exist in the "This site policy" "table_row"
    And "Accepted" "text" should exist in the "This privacy policy" "table_row"
    And I am on site homepage
    And I navigate to "Users > Privacy and policies > User agreements" in site administration
    And I click on "Details" "link" in the "User Two" "table_row"
    And "Pending" "text" should exist in the "This site policy" "table_row"
    And "Pending" "text" should exist in the "This privacy policy" "table_row"

  Scenario: Agree on behalf of another user as a manager, multiple policies, javascript off
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "Set status to \"Active\"" "link" in the "This privacy policy" "table_row"
    And I press "Continue"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    When I log in as "manager"
    And I press "Next"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > User agreements" in site administration
    And I click on "Accept This site policy" "link" in the "User One" "table_row"
    Then I should see "Accepting policy"
    And I should see "User One"
    And I should see "This site policy"
    And I should see "I acknowledge that I have received a request to give consent on behalf of the above user(s)."
    And I set the field "Remarks" to "Consent received from a parent"
    And I press "Give consent"
    And "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Pending" "text" should exist in the "User One" "table_row"
    And I click on "Details" "link" in the "User One" "table_row"
    And "Accepted on user's behalf" "text" should exist in the "This site policy" "table_row"
    And "Max Manager" "link" should exist in the "This site policy" "table_row"
    And "Consent received from a parent" "text" should exist in the "This site policy" "table_row"
    And "Pending" "text" should exist in the "This privacy policy" "table_row"

  @javascript
  Scenario: Agree on behalf of another user as a manager, multiple policies, javascript on
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "Actions" "link_or_button" in the "This privacy policy" "table_row"
    And I click on "Set status to \"Active\"" "link" in the "This privacy policy" "table_row"
    And I press "Activate"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    When I log in as "manager"
    And I press "Next"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > User agreements" in site administration
    And I click on "Accept This site policy" "link" in the "User One" "table_row"
    Then I should see "Give consent"
    And I should see "User One"
    And I should see "This site policy"
    And I should see "I acknowledge that I have received a request to give consent on behalf of the above user(s)."
    And I set the field "Remarks" to "Consent received from a parent"
    And I press "Give consent"
    And "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Pending" "text" should exist in the "User One" "table_row"
    And I click on "Details" "link" in the "User One" "table_row"
    And "Accepted on user's behalf" "text" should exist in the "This site policy" "table_row"
    And "Max Manager" "link" should exist in the "This site policy" "table_row"
    And "Consent received from a parent" "text" should exist in the "This site policy" "table_row"
    And "Pending" "text" should exist in the "This privacy policy" "table_row"

  Scenario: Policies and agreements profile link visible for current user
    Given I log in as "user1"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    When I follow "Profile" in the user menu
    # User can see his own agreements link in the profile.
    Then I should see "Policies and agreements"
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "This site policy" "table_row"
    # User can't see agreements link in other user profiles.
    And I am on "Course1" course homepage
    And I navigate to course participants
    And I follow "User Two"
    And I should not see "Policies and agreements"

  Scenario: Policies and agreements profile link visible also for users who can access on behalf of others
    Given I log in as "admin"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    And I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    # User can see agreements link in other user profiles because has the capability for accepting on behalf of them.
    When I am on "Course1" course homepage
    And I navigate to course participants
    And I follow "User Two"
    Then I should see "Policies and agreements"

  Scenario: Agree on behalf of another user as an admin who is logged in as a manager
    Given I log in as "admin"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    When I log in as "manager"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I log out
    And I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "Manager"
    And I follow "Log in as"
    And I press "Continue"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "1 of 4 (25%)" "link" in the "This site policy" "table_row"
    And I click on "Accept This site policy" "link" in the "User One" "table_row"
    Then I should see "Accepting policy"
    And I should see "User One"
    And I should see "This site policy"
    And I should see "I acknowledge that I have received a request to give consent on behalf of the above user(s)."
    And I set the field "Remarks" to "Consent received from a parent"
    And I press "Give consent"
    And "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Max Manager" "link" should not exist in the "User One" "table_row"
    And "Admin User" "link" should exist in the "User One" "table_row"
    And "Consent received from a parent" "text" should exist in the "User One" "table_row"
    And "Pending" "text" should exist in the "User Two" "table_row"

  @javascript
  Scenario: Bulk agree on behalf of another users as a manager, multiple policies, javascript on
    Given I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "Actions" "link_or_button" in the "This privacy policy" "table_row"
    And I click on "Set status to \"Active\"" "link" in the "This privacy policy" "table_row"
    And I press "Activate"
    And I set the following system permissions of "Manager" role:
      | capability | permission |
      | tool/policy:acceptbehalf | Allow |
    And I log out
    When I log in as "manager"
    And I press "Next"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I navigate to "Users > Privacy and policies > User agreements" in site administration
    And I click on "Select" "checkbox" in the "User One" "table_row"
    And I press "Consent"
    And I should see "Accepting policy"
    And I should see "One"
    And I click on "Cancel" "button" in the "Accepting policy" "dialogue"
    And I should not see "Accepting policy"
    And I click on "Select" "checkbox" in the "User Two" "table_row"
    And I press "Consent"
    And I should see "Accepting policy"
    And I should see "User One, User Two"
    When I press "Give consent"
    Then "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Accepted on user's behalf" "text" should exist in the "User Two" "table_row"

  Scenario: View acceptances made by users on their own after inactivating a policy
    Given I log in as "user1"
    And I should see "This site policy"
    And I should not see "Course overview"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I should see "Calendar"
    And I log out
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I click on "Actions" "link_or_button" in the "This privacy policy" "table_row"
    And I click on "Set status to \"Active\"" "link" in the "This privacy policy" "table_row"
    And I press "Continue"
    And I click on "Set status to \"Inactive\"" "link" in the "This privacy policy" "table_row"
    And I press "Continue"
    And I log out
    When I log in as "user1"
    Then I should see "Calendar"
