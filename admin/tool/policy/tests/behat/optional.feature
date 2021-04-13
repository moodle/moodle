@tool @tool_policy
Feature: Optional policies
  In order to exercise my privacy rights
  As a user
  I should be able to decline policy statements and withdraw my previously given consent to them

  Background:
    Given the following config values are set as admin:
      | sitepolicyhandler | tool_policy |
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

  Scenario: Configuring a policy as optional
    Given I log in as "manager"
    And I navigate to "Users > Privacy and policies > Manage policies" in site administration
    And I follow "New policy"
    # Policies are compulsory by default.
    And the field "Agreement optional" matches value "No"
    # Optional status can be set when creating a new policy.
    And I set the following fields to these values:
      | Name                                      | ConsentPageOptional1  |
      | Version                                   | v1                    |
      | Summary                                   | Policy summary        |
      | Full policy                               | Full text             |
      | Active                                    | 1                     |
      | Show policy before showing other policies | No                    |
      | Agreement optional                        | Yes                   |
    When I press "Save"
    Then the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                                                    | Policy status | Version |
      | ConsentPageOptional1 Site policy, All users, Optional   | Active        | v1      |
    # Optional status can be edited.
    And I open the action menu in "ConsentPageOptional1" "table_row"
    And I click on "Edit" "link" in the "ConsentPageOptional1" "table_row"
    And I set the field "Agreement optional" to "No"
    And I set the field "Minor change" to "1"
    And I press "Save"
    And the following should exist in the "tool-policy-managedocs-wrapper" table:
      | Name                                                    | Policy status | Version |
      | ConsentPageOptional1 Site policy, All users, Compulsory | Active        | v1      |

  Scenario: Compulsory policies must be accepted prior signup, optional policies just after it
    Given the following config values are set as admin:
      | registerauth    | email |
      | passwordpolicy  | 0     |
    And the following policies exist:
      | Name                   | Content    | Summary     | Agreementstyle | Optional  |
      | ConsentPageOptional1   | full text1 | short text1 | 0              | 1         |
      | ConsentPageOptional2   | full text2 | short text2 | 0              | 1         |
      | ConsentPageCompulsory1 | full text3 | short text3 | 0              | 0         |
      | OwnPageCompulsory1     | full text4 | short text4 | 1              | 0         |
      | OwnPageOptional1       | full text5 | short text5 | 1              | 1         |
    And I am on site homepage
    And I follow "Log in"
    And I click on "Create new account" "link"
    # Compulsory policies displayed on own page are shown first and must be agreed.
    And I should see "OwnPageCompulsory1" in the "region-main" "region"
    And I should see "short text4" in the "region-main" "region"
    And I should see "full text4" in the "region-main" "region"
    And I press "I agree to the OwnPageCompulsory1"
    # Compulsory policies displayed on the consent page are shown next and must be agreed.
    And I should see "ConsentPageCompulsory1"
    And I should see "short text3" in the "region-main" "region"
    And I should see "full text3" in the "region-main" "region"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I set the field "I agree to the ConsentPageCompulsory1" to "1"
    And I press "Next"
    # The signup form can be submitted and a new account created.
    And I set the following fields to these values:
      | Username      | user3               |
      | Password      | user3                 |
      | Email address | user3@address.invalid |
      | Email (again) | user3@address.invalid |
      | First name    | User3                 |
      | Surname       | L3                    |
    And I press "Create my new account"
    And I should see "Confirm your account"
    And I should see "An email should have been sent to your address at user3@address.invalid"
    And I confirm email for "user3"
    And I should see "Thanks, User3 L3"
    And I should see "Your registration has been confirmed"
    When I press "Continue"
    # After confirming the new account, the user is logged in and asked to accept or decline the optional policies.
    # First come policies displayed on their own page.
    Then I should see "OwnPageOptional1"
    And I should see "short text5" in the "region-main" "region"
    And I should see "full text5" in the "region-main" "region"
    And I press "No thanks, I decline OwnPageOptional1"
    # Then come policies displayed on the consent page.
    And I should see "ConsentPageOptional1" in the "region-main" "region"
    And I should see "short text1" in the "region-main" "region"
    And I should see "full text1" in the "region-main" "region"
    And I press "Next"
    And I should see "ConsentPageOptional2" in the "region-main" "region"
    And I should see "short text2" in the "region-main" "region"
    And I should see "full text2" in the "region-main" "region"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I set the field "I agree to the ConsentPageOptional1" to "1"
    And I set the field "No thanks, I decline ConsentPageOptional2" to "0"
    And I press "Next"
    # Accepted and declined policies are shown in the profile.
    And I follow "Profile" in the user menu
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "ConsentPageCompulsory1" "table_row"
    And "Accepted" "text" should exist in the "ConsentPageOptional1" "table_row"
    And "Accepted" "text" should exist in the "OwnPageCompulsory1" "table_row"
    And "Declined" "text" should exist in the "OwnPageOptional1" "table_row"
    And "Declined" "text" should exist in the "ConsentPageOptional2" "table_row"

  Scenario: When a new optional policy is added, users are asked to accept/decline it on their next login
    Given the following policies exist:
      | Name                   | Content    | Summary     | Agreementstyle | Optional  |
      | ConsentPageOptional1   | full text1 | short text1 | 0              | 1         |
      | OwnPageOptional1       | full text5 | short text5 | 1              | 1         |
    When I log in as "user1"
    # First come policies displayed on their own page.
    Then I should see "OwnPageOptional1"
    And I should see "short text5" in the "region-main" "region"
    And I should see "full text5" in the "region-main" "region"
    And I press "I agree to the OwnPageOptional1"
    # Then come policies displayed on the consent page.
    And I should see "ConsentPageOptional1" in the "region-main" "region"
    And I should see "short text1" in the "region-main" "region"
    And I should see "full text1" in the "region-main" "region"
    And I press "Next"
    And I should see "Please agree to the following policies"
    And I set the field "No thanks, I decline ConsentPageOptional1" to "0"
    And I press "Next"
    # Accepted and declined policies are shown in the profile.
    And I follow "Profile" in the user menu
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "OwnPageOptional1" "table_row"
    And "Declined" "text" should exist in the "ConsentPageOptional1" "table_row"

  Scenario: Users can withdraw an accepted optional policy and re-accept it again (js off)
    Given the following policies exist:
      | Name                   | Content    | Summary     | Agreementstyle | Optional  |
      | OwnPageOptional1       | full text1 | short text1 | 1              | 1         |
    And I log in as "user1"
    And I press "I agree to the OwnPageOptional1"
    And I follow "Profile" in the user menu
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "OwnPageOptional1" "table_row"
    And "Withdraw" "link" should exist in the "OwnPageOptional1" "table_row"
    When I click on "Withdraw acceptance of OwnPageOptional1" "link" in the "OwnPageOptional1" "table_row"
    Then I should see "Withdrawing policy"
    And I should see "User One"
    And I should see "OwnPageOptional1"
    And I press "Withdraw user consent"
    And "Declined" "text" should exist in the "OwnPageOptional1" "table_row"
    And "Accept" "link" should exist in the "OwnPageOptional1" "table_row"
    And I click on "Accept OwnPageOptional1" "link" in the "OwnPageOptional1" "table_row"
    And I should see "Accepting policy"
    And I should see "User One"
    And I should see "OwnPageOptional1"
    And I press "Give consent"
    And "Accepted" "text" should exist in the "OwnPageOptional1" "table_row"

  @javascript
  Scenario: Users can withdraw an accepted optional policy and re-accept it again (js on)
    Given the following policies exist:
      | Name                   | Content    | Summary     | Agreementstyle | Optional  |
      | OwnPageOptional1       | full text1 | short text1 | 1              | 1         |
    And I log in as "user1"
    And I press "I agree to the OwnPageOptional1"
    And I follow "Profile" in the user menu
    And I follow "Policies and agreements"
    And "Accepted" "text" should exist in the "OwnPageOptional1" "table_row"
    And "Withdraw" "link" should exist in the "OwnPageOptional1" "table_row"
    When I click on "Withdraw acceptance of OwnPageOptional1" "link" in the "OwnPageOptional1" "table_row"
    Then I should see "Withdrawing policy"
    And I should see "User One"
    And I should see "OwnPageOptional1"
    And I press "Withdraw user consent"
    And "Declined" "text" should exist in the "OwnPageOptional1" "table_row"
    And "Accept" "link" should exist in the "OwnPageOptional1" "table_row"
    And I click on "Accept OwnPageOptional1" "link" in the "OwnPageOptional1" "table_row"
    And I should see "Accepting policy"
    And I should see "User One"
    And I should see "OwnPageOptional1"
    And I press "Give consent"
    And "Accepted" "text" should exist in the "OwnPageOptional1" "table_row"

  Scenario: Managers can see accepted, declined and pending acceptances of optional policies
    Given the following policies exist:
      | Name                   | Content    | Summary     | Agreementstyle | Optional  |
      | OwnPageOptional1       | full text1 | short text1 | 1              | 1         |
      | OwnPageOptional2       | full text2 | short text2 | 1              | 1         |
    And I log in as "user1"
    And I press "I agree to the OwnPageOptional1"
    And I press "No thanks, I decline OwnPageOptional2"
    And I log out
    And I log in as "manager"
    And I press "I agree to the OwnPageOptional1"
    And I press "I agree to the OwnPageOptional2"
    When I navigate to "Users > Privacy and policies > User agreements" in site administration
    # User One has accepted just some policies.
    Then "Partially accepted" "text" should exist in the "User One" "table_row"
    And "Details" "link" should exist in the "User One" "table_row"
    # User Two did not have a chance to respond to the new policies yet.
    And "Pending" "text" should exist in the "User Two" "table_row"
    And "Details" "link" should exist in the "User Two" "table_row"
    # Max Manager accepted all and can also change status of own acceptances.
    And "Accepted" "text" should exist in the "Max Manager" "table_row"
    And "Details" "link" should exist in the "Max Manager" "table_row"
    And "Withdraw accepted policies" "link" should exist in the "Max Manager" "table_row"
    And "Withdraw acceptance of OwnPageOptional1" "link" should exist in the "Max Manager" "table_row"
    And "Withdraw acceptance of OwnPageOptional2" "link" should exist in the "Max Manager" "table_row"

  Scenario: Administrators can see accepted, declined and pending acceptances of optional policies and also change them on behalf of other users
    Given the following policies exist:
      | Name                   | Content    | Summary     | Agreementstyle | Optional  |
      | OwnPageOptional1       | full text1 | short text1 | 1              | 1         |
      | OwnPageOptional2       | full text2 | short text2 | 1              | 1         |
    And I log in as "user1"
    And I press "I agree to the OwnPageOptional1"
    And I press "No thanks, I decline OwnPageOptional2"
    And I log out
    And I log in as "admin"
    When I navigate to "Users > Privacy and policies > User agreements" in site administration
    # User One has accepted just some policies.
    Then "Partially accepted" "text" should exist in the "User One" "table_row"
    And "Details" "link" should exist in the "User One" "table_row"
    And "Withdraw acceptance of OwnPageOptional1" "link" should exist in the "User One" "table_row"
    And "Accept OwnPageOptional2" "link" should exist in the "User One" "table_row"
    # User Two did not have a chance to respond to the new policies yet.
    And "Pending" "text" should exist in the "User Two" "table_row"
    And "Accept pending policies" "link" should exist in the "User Two" "table_row"
    And "Decline pending policies" "link" should exist in the "User Two" "table_row"
    And "Accept OwnPageOptional1" "link" should exist in the "User Two" "table_row"
    And "Decline OwnPageOptional1" "link" should exist in the "User Two" "table_row"
    And "Accept OwnPageOptional2" "link" should exist in the "User Two" "table_row"
    And "Decline OwnPageOptional2" "link" should exist in the "User Two" "table_row"
    # Accept all policies on Max Manager's behalf.
    And I click on "Accept pending policies" "link" in the "Max Manager" "table_row"
    And I press "Give consent"
    And "Accepted" "text" should exist in the "Max Manager" "table_row"
    # Decline all policies on User Two's behalf.
    And I click on "Decline pending policies" "link" in the "User Two" "table_row"
    And I press "Decline user consent"
    And "Declined on user's behalf" "text" should exist in the "User Two" "table_row"
    And "Accepted" "text" should not exist in the "User Two" "table_row"
    And "Pending" "text" should not exist in the "User Two" "table_row"
    # Accept policy on User One's behalf.
    And I click on "Accept OwnPageOptional2" "link" in the "User One" "table_row"
    And I press "Give consent"
    And "Accepted on user's behalf" "text" should exist in the "User One" "table_row"
    And "Declined" "text" should not exist in the "User One" "table_row"
    And "Pending" "text" should not exist in the "User One" "table_row"
