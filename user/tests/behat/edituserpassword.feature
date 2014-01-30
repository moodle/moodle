@core @core_user
Feature: Enable/disable password field based on authentication selected.
  In order edit a user password properly
  As an admin
  I need to be able to notice if the change in password is allowed by athuentication plugin or not

  @javascript
  Scenario: Verify the password field is enabled/disabled based on authentication selected, in user edit advanced page.
    Given I log in as "admin"
    And I follow "My home"
    And I expand "Site administration" node
    And I expand "Users" node
    And I expand "Accounts" node
    When I follow "Add a new user"
    Then the "newpassword" "field" should be enabled
    And I set the field "auth" to "Web services authentication"
    And the "newpassword" "field" should be disabled
    And I set the field "auth" to "Email-based self-registration"
    And the "newpassword" "field" should be enabled
    # We need to cancel/submit a form that has been modified.
    And I press "Create user"
