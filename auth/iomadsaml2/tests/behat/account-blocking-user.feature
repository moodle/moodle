@auth @auth_iomadsaml2 @javascript
Feature: IOMAD SAML2 flagged accounts login
  In order to have correct Moodle access
  As a user
  I need to have my account checked for group restrictions when logging in through IOMAD SAML2 service provider

  Scenario: If my account is blocked and I log in via IOMAD SAML2 I should not have Moodle access
    Given the authentication plugin iomadsaml2 is enabled                                              # auth_iomadsaml2
    And the mock SAML IdP is configured                                                           # auth_iomadsaml2
    And the iomadsaml2 setting "Group rules" is set to "deny groups=block"                             # auth_iomadsaml2
    And the iomadsaml2 setting "Account blocking response type" is set to "Display custom message"     # auth_iomadsaml2
    When I go to the login page                                                                   # auth_iomadsaml2
    And I follow "Login via IOMAD SAML2"
    And the mock SAML IdP allows login with the following attributes:                             # auth_iomadsaml2
      | uid    | studentflagged |
      | groups | block          |
    Then I should see "You are not logged in."
    And I should see "You are logged in to your identity provider however, this account has limited access to Moodle, please contact your administrator for more details."

  Scenario: If my account is blocked and redirect page is set, on IOMAD SAML2 login I should see the redirect page
    Given the authentication plugin iomadsaml2 is enabled                                              # auth_iomadsaml2
    And the mock SAML IdP is configured                                                           # auth_iomadsaml2
    And the iomadsaml2 setting "Group rules" is set to "deny groups=block"                             # auth_iomadsaml2
    And the iomadsaml2 setting "Account blocking response type" is set to "Redirect to external URL"   # auth_iomadsaml2
    And the iomadsaml2 setting "Redirect URL" is set to "https://en.wikipedia.org"                     # auth_iomadsaml2
    When I go to the login page                                                                   # auth_iomadsaml2
    And I follow "Login via IOMAD SAML2"
    And the mock SAML IdP allows login with the following attributes:                             # auth_iomadsaml2
      | uid    | studentflagged |
      | groups | block          |
    Then I should see "Wikipedia"
    And I should not see "Moodle"

  Scenario: If my account is blocked and response message is set, on IOMAD SAML2 login I should see the response message
    Given the authentication plugin iomadsaml2 is enabled                                              # auth_iomadsaml2
    And the mock SAML IdP is configured                                                           # auth_iomadsaml2
    And the iomadsaml2 setting "Group rules" is set to "deny groups=block"                             # auth_iomadsaml2
    And the iomadsaml2 setting "Account blocking response type" is set to "Display custom message"     # auth_iomadsaml2
    And the iomadsaml2 setting "Response message" is set to "No access"                                # auth_iomadsaml2
    When I go to the login page                                                                   # auth_iomadsaml2
    And I follow "Login via IOMAD SAML2"
    And the mock SAML IdP allows login with the following attributes:                             # auth_iomadsaml2
      | uid    | studentflagged |
      | groups | block          |
    Then I should see "No access"

  Scenario: If my account is blocked, but group restrictions is turned off, I should always be able to log in to Moodle
    Given the authentication plugin iomadsaml2 is enabled                                              # auth_iomadsaml2
    And the mock SAML IdP is configured                                                           # auth_iomadsaml2
    And the iomadsaml2 setting "Group rules" is set to ""                                              # auth_iomadsaml2
    And the iomadsaml2 setting "Account blocking response type" is set to "Display custom message"     # auth_iomadsaml2
    When I go to the login page                                                                   # auth_iomadsaml2
    And I follow "Login via IOMAD SAML2"
    And the mock SAML IdP allows login with the following attributes:                             # auth_iomadsaml2
      | uid       | studentflagged |
      | firstname | Student        |
      | surname   | Bravo          |
      | groups    | block          |
    Then I should see "Student Bravo"

  Scenario: If my account is not blocked, I should be able to log into Moodle
    Given the authentication plugin iomadsaml2 is enabled                                              # auth_iomadsaml2
    And the mock SAML IdP is configured                                                           # auth_iomadsaml2
    And the iomadsaml2 setting "Group rules" is set to "allow groups=allow"                            # auth_iomadsaml2
    And the iomadsaml2 setting "Account blocking response type" is set to "Display custom message"     # auth_iomadsaml2
    And the iomadsaml2 setting "Response message" is set to "No access"                                # auth_iomadsaml2
    When I go to the login page                                                                   # auth_iomadsaml2
    And I follow "Login via IOMAD SAML2"
    And the mock SAML IdP allows login with the following attributes:                             # auth_iomadsaml2
      | uid       | student |
      | firstname | Student |
      | surname   | Alpha   |
      | groups    | allow   |
    Then I should see "Student Alpha"
