@core @core_user
Feature: Edit a users password
  In order edit a user password properly
  As an admin
  I need to be able to edit their profile and change their password

  @javascript
  Scenario: Verify the password field is enabled/disabled based on authentication selected, in user edit advanced page.
    Given I log in as "admin"
    When I navigate to "Users > Accounts > Add a new user" in site administration
    Then the "New password" "field" should be enabled
    And I set the field "auth" to "Web services authentication"
    And the "New password" "field" should be disabled
    And I set the field "auth" to "Email-based self-registration"
    And the "New password" "field" should be enabled
    # We need to cancel/submit a form that has been modified.
    And I press "Create user"

  Scenario: Sign out everywhere field is not present if user doesn't have active token
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | user01   | User      | One      | user01@example.com |
    And I log in as "admin"
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "User One" "link" in the "users" "table"
    And I click on "Edit profile" "link"
    Then "Sign out everywhere" "field" should not exist

  Scenario Outline: Sign out everywhere field is present based on expiry of active token
    Given the following "users" exist:
      | username | firstname | lastname | email              |
      | user01   | User      | One      | user01@example.com |
    And the following "core_webservice > Service" exist:
      | shortname     | name            |
      | mytestservice | My test service |
    And the following "core_webservice > Tokens" exist:
      | user   | service       | validuntil   |
      | user01 | mytestservice | <validuntil> |
    And I log in as "admin"
    When I navigate to "Users > Accounts > Browse list of users" in site administration
    And I click on "User One" "link" in the "users" "table"
    And I click on "Edit profile" "link"
    Then "Sign out everywhere" "field" <shouldornot> exist
    Examples:
      | validuntil     | shouldornot |
      | ## -1 month ## | should not  |
      | 0              | should      |
      | ## +1 month ## | should      |
