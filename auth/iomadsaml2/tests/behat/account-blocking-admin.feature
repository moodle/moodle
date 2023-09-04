@auth @auth_iomadsaml2 @javascript
Feature: IOMAD SAML2 Account blocking settings
  In order to handle Account blocking logging in through and IdP
  As an administrator
  I need to be able to change Account blocking settings in Moodle

  Scenario: I can navigate to the settings page
    Given the authentication plugin iomadsaml2 is enabled                            # auth_iomadsaml2
    And I am an administrator                                                   # auth_iomadsaml2
    When I navigate to "Plugins > Authentication > IOMAD SAML2" in site administration
    Then I should see "Account blocking actions"
    And I should see "Redirect or display message to IOMAD SAML2 logins based on configured group restrictions"

  Scenario Outline: I can change the Account blocking response type options
    Given the authentication plugin iomadsaml2 is enabled                            # auth_iomadsaml2
    And I am an administrator                                                   # auth_iomadsaml2
    And I am on the iomadsaml2 settings page                                         # auth_iomadsaml2
    When I change the setting "Account blocking response type" to "<Option>"    # auth_iomadsaml2
    And I press "Save changes"
    Then I go to the iomadsaml2 settings page again                                  # auth_iomadsaml2
    And the setting "Account blocking response type" should be "<Option>"       # auth_iomadsaml2
    Examples:
      | Option                             |
      | Display custom message             |
      | Redirect to external URL           |

  Scenario: I can use URLs for the Redirect URL mapping
    Given the authentication plugin iomadsaml2 is enabled                       # auth_iomadsaml2
    And I am an administrator                                              # auth_iomadsaml2
    And I am on the iomadsaml2 settings page                                    # auth_iomadsaml2
    When I change the setting "Redirect URL" to "https://www.google.com"   # auth_iomadsaml2
    And I press "Save changes"
    Then I go to the iomadsaml2 settings page again                             # auth_iomadsaml2
    And the setting "Redirect URL" should be "https://www.google.com"      # auth_iomadsaml2
