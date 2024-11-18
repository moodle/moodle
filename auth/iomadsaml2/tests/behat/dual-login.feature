@auth @auth_iomadsaml2 @javascript
Feature: IOMAD SAML2 Dual Login
  In order use Moodle login or IOMAD SAML2 login
  As a user
  I need to login into IOMAD SAML2 or Moodle depending on the Dual Login setting

  Background:
    Given the authentication plugin iomadsaml2 is enabled                           # auth_iomadsaml2
    And the mock SAML IdP is configured                                        # auth_iomadsaml2

  Scenario: If dual login is "no", redirect to IDP
    Given the iomadsaml2 setting "Dual Login" is set to "no"                        # auth_iomadsaml2
    When I go to the login page                                                # auth_iomadsaml2
    Then I should see "Mock IdP login"

  Scenario: If dual login is "no", I can bypass the iomadsaml2 redirect
    Given the iomadsaml2 setting "Dual Login" is set to "no"                        # auth_iomadsaml2
    When I go to the login page with "saml=0"                                  # auth_iomadsaml2
    Then I should see "Log in"
    And I should not see "Mock IdP login"

  Scenario: If dual login is "yes" then I need to select IOMAD SAML2
    Given the iomadsaml2 setting "Dual Login" is set to "yes"                       # auth_iomadsaml2
    When I go to the login page                                                # auth_iomadsaml2
    And I follow "Login via IOMAD SAML2"
    Then I should see "Mock IdP login"

  Scenario: If dual login is "passive" and I am not logged in IOMAD SAML2, use Moodle Login
    Given the iomadsaml2 setting "Dual Login" is set to "passive"                   # auth_iomadsaml2
    When I go to the login page                                                # auth_iomadsaml2
    And the mock SAML IdP does not allow passive login                         # auth_iomadsaml2
    Then I should see "Log in"
    And I should see "Login via IOMAD SAML2"
    And I should not see "Mock IdP login"
    When I set the field "Username" to "admin"
    And I set the field "Password" to "admin"
    And I press "Log in"
    Then I should see "Admin User"

  Scenario: If dual login is "passive" and I am not logged in IOMAD SAML2, I can still use IOMAD SAML2
    Given the iomadsaml2 setting "Dual Login" is set to "passive"                   # auth_iomadsaml2
    When I go to the login page                                                # auth_iomadsaml2
    And the mock SAML IdP does not allow passive login                         # auth_iomadsaml2
    Then I should see "Log in"
    And I should see "Login via IOMAD SAML2"
    And I follow "Login via IOMAD SAML2"
    And the mock SAML IdP allows login with the following attributes:          # auth_iomadsaml2
      | uid       | student |
      | firstname | Student |
      | surname   | Alpha   |
    And I should see "Student Alpha"

  Scenario: If dual login is "passive" and I am logged in IOMAD SAML2, auto-login
    Given the authentication plugin iomadsaml2 is enabled                           # auth_iomadsaml2
    And the mock SAML IdP is configured                                        # auth_iomadsaml2
    Given the iomadsaml2 setting "Dual Login" is set to "passive"                   # auth_iomadsaml2
    When I go to the login page                                                # auth_iomadsaml2
    And the mock SAML IdP allows passive login with the following attributes:  # auth_iomadsaml2
      | uid       | student |
      | firstname | Student |
      | surname   | Alpha   |
    Then I should see "Student Alpha"
