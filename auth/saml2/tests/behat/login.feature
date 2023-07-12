@auth @auth_saml2 @javascript
Feature: Login
  In order to allow single sign on
  As a SAML2 user
  I need to be able to login into Moodle

  Scenario: Use Moodle Login if SAML2 is disabled
    Given the authentication plugin saml2 is disabled                          # auth_saml2
    When I go to the login page                                                # auth_saml2
    Then I should see "Acceptance test site"
    And I should see "Log in"
    But I should not see "Mock IdP login"

  Scenario: SAML2 login to existing account with field mapping and updates
    Given the following "users" exist:
      | username | auth  | firstname | lastname |
      | student1 | saml2 | Eigh      | Person   |
    And the authentication plugin saml2 is enabled                             # auth_saml2
    And the mock SAML IdP is configured                                        # auth_saml2
    And the following config values are set as admin:
      | field_map_firstname         | sillyname1 | auth_saml2 |
      | field_updatelocal_firstname | onlogin    | auth_saml2 |
      | field_map_lastname          | sillyname2 | auth_saml2 |
      | field_updatelocal_lastname  | onlogin    | auth_saml2 |
    And the saml2 setting "Dual Login" is set to "no"                          # auth_saml2
    And I am on site homepage
    And I follow "Log in"
    And the mock SAML IdP allows login with the following attributes:          # auth_saml2
      | uid        | student1 |
      | sillyname1 | Anne     |
      | sillyname2 | Other    |
    Then I should see "You are logged in as Anne Other"

  Scenario: SAML2 logout
    Given the following "users" exist:
      | username | auth  | firstname | lastname |
      | student1 | saml2 | Eigh      | Person   |
    And the authentication plugin saml2 is enabled                             # auth_saml2
    And the mock SAML IdP is configured                                        # auth_saml2
    And the saml2 setting "Dual Login" is set to "no"                          # auth_saml2
    And I am on site homepage
    And I follow "Log in"
    And the mock SAML IdP allows login with the following attributes:          # auth_saml2
      | uid | student1 |
    Then I should see "You are logged in as Eigh Person"
    And I click on "Log out" "link" in the "#page-footer" "css_element"
    And the mock SAML IdP confirms logout                                      # auth_saml2
    And I should see "You are not logged in."
