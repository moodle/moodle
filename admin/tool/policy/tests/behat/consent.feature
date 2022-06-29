@tool @tool_policy
Feature: User must accept policy managed by this plugin when logging in and signing up
  In order to record user agreement to use the site
  As a user
  I need to be able to accept site policy during sign up

  Scenario: Accept policy on sign up, no site policy
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site (edit their profile).
    And I log in as "user1"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"

  Scenario: Accept policy on sign up, only draft policy
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Policy | Name             | Revision | Content    | Summary     | Status   |
      | P1     | This site policy |          | full text1 | short text1 | draft |
      | P1     | This privacy policy |          | full text2 | short text2 | draft |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site (edit their profile).
    And I log in as "user1"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"

  Scenario: Accept policy on sign up, one policy
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    Given the following policies exist:
      | Policy | Name             | Revision | Content    | Summary     | Status   |
      | P1     | This site policy |          | full text1 | short text1 | archived |
      | P1     | This site policy |          | full text2 | short text2 | active   |
      | P1     | This site policy |          | full text3 | short text3 | draft    |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should see "full text2"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should not see "full text2"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site.
    And I log in as "user1"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "This site policy" "table_row"
    And I log out

  Scenario: Accept policy on sign up, multiple policies
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    Given the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
      | This guests policy  | 0    |          | full text4 | short text4 | active   | guest    |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should see "full text2"
    And I press "Next"
    And I should see "This privacy policy" in the "region-main" "region"
    And I should see "short text3"
    And I should see "full text3"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should not see "full text2"
    And I should see "This privacy policy" in the "region-main" "region"
    And I should see "short text3"
    And I should not see "full text3"
    And I should not see "This guests policy" in the "region-main" "region"
    And I should not see "short text4"
    And I should not see "full text4"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site.
    And I log in as "user1"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "This site policy" "table_row"
    And "Accepted" "text" should exist in the "This privacy policy" "table_row"
    And I should not see "This guests policy"
    And I log out

  Scenario: Accept policy on sign up and age verification
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
      | agedigitalconsentverification | 1 |
    Given the following policies exist:
      | Name             | Revision | Content    | Summary     | Status   |
      | This site policy |          | full text2 | short text2 | active   |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should see "Age and location verification"
    And I set the field "What is your age?" to "16"
    And I set the field "In which country do you live?" to "DZ"
    And I press "Proceed"
    And I should see "This site policy"
    And I should see "short text2"
    And I should see "full text2"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    And I should see "short text2"
    And I should not see "full text2"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site.
    And I log in as "user1"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "This site policy" "table_row"
    And I log out

  Scenario: Accept policy on sign up, do not accept all policies
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
    And I am on site homepage
    And I follow "Log in"
    And I click on "Create new account" "link"
    And I should see "This site policy"
    And I press "Next"
    And I should see "This privacy policy"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    And I should see "This privacy policy"
    # Confirm that a notification is displayed if none of the policies are accepted.
    When I set the field "I agree to the This site policy" to "0"
    And I set the field "I agree to the This privacy policy" to "0"
    And I press "Next"
    Then I should see "Please agree to the following policies"
    And I should see "Before continuing you need to acknowledge all these policies."
    # Confirm that a notification is displayed if only some policies are accepted.
    When I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "0"
    Then I should see "Please agree to the following policies"
    And I should see "Before continuing you need to acknowledge all these policies."

  Scenario: Accept policy on login, do not accept all policies
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
    And I log in as "user1"
    And I should see "This site policy"
    And I press "Next"
    And I should see "This privacy policy"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    And I should see "This privacy policy"
    # Confirm that a notification is displayed if none of the policies are accepted.
    When I set the field "I agree to the This site policy" to "0"
    And I set the field "I agree to the This privacy policy" to "0"
    And I press "Next"
    Then I should see "Please agree to the following policies"
    And I should see "Before continuing you need to acknowledge all these policies."
    # Confirm that a notification is displayed if only some policies are accepted.
    When I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "0"
    Then I should see "Please agree to the following policies"
    And I should see "Before continuing you need to acknowledge all these policies."
    # Confirm that user can not browse the site (edit their profile).
    When I follow "Profile" in the user menu
    Then I should see "Please agree to the following policies"

  Scenario: Accept policy on login, accept all policies
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
    And I log in as "user1"
    And I should see "This site policy"
    And I press "Next"
    And I should see "This privacy policy"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    And I should see "This privacy policy"
    # User accepts all policies.
    When I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    Then I should not see "Please agree to the following policies"
    And I should not see "Before continuing you need to acknowledge all these policies."
    # Confirm that user can login and browse the site (edit their profile).
    When I open my profile in edit mode
    Then the field "First name" matches value "User"
    And I log out
    # Confirm when logging again as user, the policies are not displayed.
    When I log in as "user1"
    Then I should not see "This site policy"
    And I should not see "This privacy policy"
    And I should not see "Please agree to the following policies"
    # Confirm that user can login and browse the site (edit their profile).
    When I open my profile in edit mode
    Then the field "First name" matches value "User"

  Scenario: Accept policy on login, accept new policy documents
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
    And I log in as "user1"
    And I should see "This site policy"
    And I press "Next"
    And I should see "This privacy policy"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    And I should see "This privacy policy"
    # User accepts all policies.
    When I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    Then I should not see "Please agree to the following policies"
    # Confirm that user can login and browse the site (edit their profile).
    When I open my profile in edit mode
    Then the field "First name" matches value "User"
    And I log out
    # Create new policy document.
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I should see "Policies and agreements"
    And I should see "New policy"
    And I follow "New policy"
    And I set the following fields to these values:
      | Name          | This third parties policy |
      | Type          | Third parties policy      |
      | User consent  | All users                 |
      | Summary       | short text4               |
      | Full policy   | full text4                |
      | Active        | 1                    |
    When I press "Save"
    Then I should see "Policies and agreements"
    And I should see "This third parties policy"
    And I log out
    # Confirm when logging again as user, the new policies are displayed.
    When I log in as "user1"
    And I should not see "This site policy"
    And I should not see "This privacy policy"
    Then I should see "This third parties policy"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This third parties policy"
    And I set the field "This third parties policy" to "1"
    And I press "Next"
    # Confirm that user can login and browse the site (edit their profile).
    When I open my profile in edit mode
    Then the field "First name" matches value "User"

  Scenario: Accept policy on login, accept new policy version
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
    And I log in as "user1"
    And I should see "This site policy"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    # User accepts policy.
    When I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    Then I should not see "Please agree to the following policies"
    # Confirm that user can login and browse the site (edit their profile).
    When I open my profile in edit mode
    Then the field "First name" matches value "User"
    And I log out
    # Create new version of the policy document.
    And I log in as "admin"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    # Menu is already open because javascript is disabled.
    Then I should see "View"
    And I should see "Edit"
    And I should see "Set status to \"Inactive\""
    When I follow "Edit"
    Then I should see "Editing policy"
    And I set the field "Name" to "This site policy new version"
    And I set the field "Summary" to "short text2 new version"
    And I set the field "Full policy" to "full text2 new version"
    And I press "Save"
    And I log out
    # Confirm that the user has to agree to the new version of the policy.
    When I log in as "user1"
    Then I should see "This site policy new version"
    And I should see "short text2 new version"
    And I should see "full text2 new version"
    When I press "Next"
    Then I should see "Please agree to the following policies"
    And I should see "This site policy new version"
    And I should see "short text2 new version"
    # User accepts policy.
    And I set the field "I agree to the This site policy new version" to "1"
    When I press "Next"
    Then I should not see "Please agree to the following policies"
    # Confirm that user can login and browse the site (edit their profile).
    When I open my profile in edit mode
    Then the field "First name" matches value "User"

  @javascript
  Scenario: Accept policy on login as guest
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
      | This guests policy  | 0    |          | full text4 | short text4 | active   | guest    |
    And I am on site homepage
    And I change window size to "large"
    And I follow "Log in"
    When I press "Log in as a guest"
    Then I should see "If you continue browsing this website, you agree to our policies"
    # Confirm when navigating, the pop-up policies are displayed.
    When I am on the "My courses" page
    Then I should see "If you continue browsing this website, you agree to our policies"
    And I should see "This site policy"
    And I should see "This guests policy"
    And I should not see "This privacy policy"
    # Confirm when clicking on the policy links, the policy content is displayed.
    When I click on "This site policy" "link"
    Then I should see "full text2"
    And I click on "Close" "button" in the "This site policy" "dialogue"
    And I should not see "full text2"
    When I click on "This guests policy" "link"
    Then I should see "full text4"
    And I click on "Close" "button" in the "This guests policy" "dialogue"
    And I should not see "full text4"
    # Confirm when agreeing to policies the pop-up is no longer displayed.
    When I follow "Continue"
    Then I should not see "If you continue browsing this website, you agree to our policies"

  Scenario: Accept policy on sign up, after completing sign up attempt to create another account
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    Given the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
      | This guests policy  | 0    |          | full text4 | short text4 | active   | guest    |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should see "full text2"
    When I press "Next"
    Then I should see "This privacy policy" in the "region-main" "region"
    And I should see "short text3"
    And I should see "full text3"
    When I press "Next"
    Then I should see "Please agree to the following policies"
    And I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should see "This privacy policy" in the "region-main" "region"
    And I should see "short text3"
    And I should not see "This guests policy" in the "region-main" "region"
    And I should not see "short text4"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    When I press "Next"
    Then I should not see "I understand and agree"
    And I should see "New account"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    When I press "Create my new account"
    Then I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I follow "Log in"
    When I click on "Create new account" "link"
    # Confirm that the user can view and accept policies when attempting to create another account.
    Then I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should see "full text2"
    When I press "Next"
    Then I should see "This privacy policy" in the "region-main" "region"
    And I should see "short text3"
    And I should see "full text3"
    When I press "Next"
    Then I should see "Please agree to the following policies"
    And I should see "This site policy" in the "region-main" "region"
    And I should see "short text2"
    And I should not see "full text2"
    And I should see "This privacy policy" in the "region-main" "region"
    And I should see "short text3"
    And I should not see "full text3"
    And I should not see "This guests policy" in the "region-main" "region"
    And I should not see "short text4"
    And I should not see "full text4"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    When I press "Next"
    Then I should not see "I understand and agree"
    And I should see "New account"

  Scenario: Accept policy while being logged in as another user
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
    When I log in as "admin"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "User 1"
    And I follow "Log in as"
    Then I should see "You are logged in as User 1"
    And I press "Continue"
    And I should see "Please read our This site policy"
    And I press "Next"
    And I should see "Please read our This privacy policy"
    And I press "Next"
    And I should see "Viewing this page on behalf of User 1"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I log out
    And I log in as "user1"
    And I follow "Profile" in the user menu
    And I follow "Policies and agreements"
    And "Admin User" "link" should exist in the "This site policy" "table_row"
    And "Admin User" "link" should exist in the "This privacy policy" "table_row"

  Scenario: Log in as another user without capability to accept policies on their behalf
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | Name                | Type | Revision | Content    | Summary     | Status   | Audience |
      | This site policy    | 0    |          | full text2 | short text2 | active   | all      |
      | This privacy policy | 1    |          | full text3 | short text3 | active   | loggedin |
    And the following "users" exist:
      | username | firstname | lastname | email            |
      | user1    | User      | 1        | user1@example.com    |
      | manager  | Max       | Manager  | man@example.com |
    And the following "role assigns" exist:
      | user    | role           | contextlevel | reference |
      | manager | manager        | System       |           |
    When I log in as "manager"
    And I press "Next"
    And I press "Next"
    And I set the field "I agree to the This site policy" to "1"
    And I set the field "I agree to the This privacy policy" to "1"
    And I press "Next"
    And I navigate to "Users > Accounts > Browse list of users" in site administration
    And I follow "User 1"
    And I follow "Log in as"
    Then I should see "You are logged in as User 1"
    And I press "Continue"
    And I should see "Policies and agreements"
    And I should see "No permission to agree to the policies on behalf of this user"
    And I should see "Sorry, you do not have the required permission to agree to the following policies on behalf of User 1"

  Scenario: Accept policy on sign up as a guest, one policy
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
      | sitepolicyhandler | tool_policy |
    Given the following policies exist:
      | Policy | Name             | Revision | Content    | Summary     | Status   |
      | P1     | This site policy |          | full text1 | short text1 | archived |
      | P1     | This site policy |          | full text2 | short text2 | active   |
      | P1     | This site policy |          | full text3 | short text3 | draft    |
    And I am on site homepage
    And I follow "Log in"
    # First log in as a guest
    And I press "Log in as a guest"
    # Now sign up
    And I follow "Log in"
    When I click on "Create new account" "link"
    Then I should see "This site policy"
    And I should see "short text2"
    And I should see "full text2"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "This site policy"
    And I should see "short text2"
    And I should not see "full text2"
    And I set the field "I agree to the This site policy" to "1"
    And I press "Next"
    And I should not see "I understand and agree"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site.
    And I log in as "user1"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "This site policy" "table_row"
    And I log out

  Scenario: Accepting policies on sign up, multiple policies with different style of giving ageement.
    Given the following config values are set as admin:
      | registerauth      | email       |
      | passwordpolicy    | 0           |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | name                          | summary                   | content             | agreementstyle  |
      | Privacy policy                | We scan your thoughts     | Here goes content.  | 0               |
      | Digital maturity declaration  | You declare be old enough | Here goes content.  | 1               |
      | Cookies policy                | We eat cookies, srsly     | Here goes content.  | 0               |
      | Terms of Service              | We teach, you learn       | Here goes content.  | 1               |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    # The first policy with the agreement style "on its own page" must be accepted first.
    Then I should see "Digital maturity declaration" in the "region-main" "region"
    And I should see "You declare be old enough"
    And I should see "Here goes content."
    And I press "I agree to the Digital maturity declaration"
    # The second policy with the agreement style "on its own page" must be accepted now.
    And I should see "Terms of Service" in the "region-main" "region"
    And I should see "We teach, you learn"
    And I should see "Here goes content."
    And I press "I agree to the Terms of Service"
    # Only now we see the remaining consent page policies.
    And I should see "Policy 1 out of 2"
    And I should see "Privacy policy" in the "region-main" "region"
    And I should see "We scan your thoughts"
    And I should see "Here goes content."
    And I press "Next"
    And I should see "Policy 2 out of 2"
    And I should see "Cookies policy" in the "region-main" "region"
    And I should see "We eat cookies, srsly"
    And I should see "Here goes content."
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "Privacy policy"
    And I should see "Cookies policy"
    And I should not see "Digital maturity declaration" in the "region-main" "region"
    And I should not see "Terms of Service" in the "region-main" "region"
    And I should not see "Here goes content."
    And I set the field "I agree to the Privacy policy" to "1"
    And I set the field "I agree to the Cookies policy" to "1"
    And I press "Next"
    And I should see "New account"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site.
    And I log in as "user1"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "Privacy policy" "table_row"
    And "Accepted" "text" should exist in the "Cookies policy" "table_row"
    And "Accepted" "text" should exist in the "Terms of Service" "table_row"
    And "Accepted" "text" should exist in the "Digital maturity declaration" "table_row"
    And I log out

  Scenario: Accepting policies on login, multiple policies with different style of giving ageement.
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | name                          | summary                   | content             | agreementstyle  |
      | Digital maturity declaration  | You declare be old enough | Here goes content.  | 1               |
      | Privacy policy                | We scan your thoughts     | Here goes content.  | 0               |
      | Terms of Service              | We teach, you learn       | Here goes content.  | 1               |
      | Cookies policy                | We eat cookies, srsly     | Here goes content.  | 0               |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
    And I log in as "user1"
    # The first policy with the agreement style "on its own page" must be accepted first.
    Then I should see "Digital maturity declaration" in the "region-main" "region"
    And I should see "You declare be old enough"
    And I should see "Here goes content."
    And I press "I agree to the Digital maturity declaration"
    # The second policy with the agreement style "on its own page" must be accepted now.
    And I should see "Terms of Service" in the "region-main" "region"
    And I should see "We teach, you learn"
    And I should see "Here goes content."
    # If the user logs out now, only the first policy is accepted and we return to the same page.
    And I log out
    And I log in as "user1"
    And I should see "Terms of Service" in the "region-main" "region"
    And I should see "We teach, you learn"
    And I should see "Here goes content."
    And I press "I agree to the Terms of Service"
    # Only now we see the remaining consent page policies.
    And I should see "Policy 1 out of 2"
    And I should see "Privacy policy" in the "region-main" "region"
    And I should see "We scan your thoughts"
    And I should see "Here goes content."
    And I press "Next"
    And I should see "Policy 2 out of 2"
    And I should see "Cookies policy" in the "region-main" "region"
    And I should see "We eat cookies, srsly"
    And I should see "Here goes content."
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I should see "Privacy policy"
    And I should see "Cookies policy"
    And I should not see "Digital maturity declaration" in the "region-main" "region"
    And I should not see "Terms of Service" in the "region-main" "region"
    And I should not see "Here goes content."
    And I set the field "I agree to the Privacy policy" to "1"
    And I set the field "I agree to the Cookies policy" to "1"
    And I press "Next"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "Privacy policy" "table_row"
    And "Accepted" "text" should exist in the "Cookies policy" "table_row"
    And "Accepted" "text" should exist in the "Terms of Service" "table_row"
    And "Accepted" "text" should exist in the "Digital maturity declaration" "table_row"
    And I log out

  Scenario: Accepting policies on login, all and loggedin policies to be accepted on their own page.
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | name                          | summary                   | content             | agreementstyle  | audience  |
      | Privacy policy                | We scan your thoughts     | Here goes content.  | 1               | all       |
      | Digital maturity declaration  | You declare be old enough | Here goes content.  | 1               | loggedin  |
      | Cookies policy                | We eat cookies, srsly     | Here goes content.  | 1               | guest     |
      | Terms of Service              | We teach, you learn       | Here goes content.  | 1               | all       |
    And the following "users" exist:
      | username | firstname | lastname | email             |
      | user1    | User      | One      | user1@example.com |
    And I log in as "user1"
    # All the policies to be displayed one by one with a button to accept each of them prior seeing the next.
    Then I should see "Privacy policy" in the "region-main" "region"
    And I should see "We scan your thoughts"
    And I should see "Here goes content."
    And I press "I agree to the Privacy policy"
    And I should see "Digital maturity declaration" in the "region-main" "region"
    And I should see "You declare be old enough"
    And I should see "Here goes content."
    And I press "I agree to the Digital maturity declaration"
    And I should see "Terms of Service" in the "region-main" "region"
    And I should see "We teach, you learn"
    And I should see "Here goes content."
    And I press "I agree to the Terms of Service"
    And I follow "Profile" in the user menu
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "Privacy policy" "table_row"
    And "Accepted" "text" should exist in the "Terms of Service" "table_row"
    And "Accepted" "text" should exist in the "Digital maturity declaration" "table_row"
    And "Cookies policy" "table_row" should not exist
    And I log out

  Scenario: Accepting policies on sign up, policies to be accepted on their own page.
    Given the following config values are set as admin:
      | registerauth      | email       |
      | passwordpolicy    | 0           |
      | sitepolicyhandler | tool_policy |
    And the following policies exist:
      | name                          | summary                   | content             | agreementstyle  | audience  |
      | Privacy policy                | We scan your thoughts     | Here goes content.  | 1               | guest     |
      | Digital maturity declaration  | You declare be old enough | Here goes content.  | 1               | all       |
      | Cookies policy                | We eat cookies, srsly     | Here goes content.  | 1               | loggedin  |
      | Terms of Service              | We teach, you learn       | Here goes content.  | 1               | guest     |
    And I am on site homepage
    And I follow "Log in"
    When I click on "Create new account" "link"
    # All the policies to be displayed one by one with a button to accept each of them prior seeing the next.
    Then I should see "Digital maturity declaration" in the "region-main" "region"
    And I should see "You declare be old enough"
    And I should see "Here goes content."
    And I press "I agree to the Digital maturity declaration"
    And I should see "Cookies policy" in the "region-main" "region"
    And I should see "We eat cookies, srsly"
    And I press "I agree to the Cookies policy"
    And I should see "New account"
    And I set the following fields to these values:
      | Username      | user1                 |
      | Password      | user1                 |
      | Email address | user1@address.invalid |
      | Email (again) | user1@address.invalid |
      | First name    | User1                 |
      | Surname       | L1                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user1@address.invalid"
    And I confirm email for "user1"
    And I should see "Thanks, User1 L1"
    And I should see "Your registration has been confirmed"
    And I open my profile in edit mode
    And the field "First name" matches value "User1"
    And I log out
    # Confirm that user can login and browse the site.
    And I log in as "user1"
    And I follow "Profile" in the user menu
    # User can see his own agreements in the profile.
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "Digital maturity declaration" "table_row"
    And "Accepted" "text" should exist in the "Cookies policy" "table_row"
    And "Privacy policy" "table_row" should not exist
    And "Terms of Service" "table_row" should not exist
    And I log out
