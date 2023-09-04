@auth @auth_iomadsaml2 @javascript
Feature: IOMAD SAML2 settings
  In order to configure the plugin
  As an administrator
  I need to change the settings in Moodle

  Scenario: I can navigate to the settings page
    Given the authentication plugin iomadsaml2 is enabled                                  # auth_iomadsaml2
    And I am an administrator                                                         # auth_iomadsaml2
    When I navigate to "Plugins > Authentication > IOMAD SAML2" in site administration
    Then I should see "SAML2"
    And I should see "Authenticate with a IOMAD SAML2 Identity Provider (IdP)"

  Scenario Outline: I can change the Dual Login options
    Given the authentication plugin iomadsaml2 is enabled        # auth_iomadsaml2
    And I am an administrator                               # auth_iomadsaml2
    And I am on the iomadsaml2 settings page                     # auth_iomadsaml2
    When I change the setting "Dual login" to "<Option>"    # auth_iomadsaml2
    And I press "Save changes"
    Then I go to the iomadsaml2 settings page again              # auth_iomadsaml2
    And the setting "Dual login" should be "<Option>"       # auth_iomadsaml2
    Examples:
      | Option       |
      | No           |
      | Yes          |
      | Passive mode |

  Scenario: I can use special characters for the IdP mapping
    Given the authentication plugin iomadsaml2 is enabled        # auth_iomadsaml2
    And I am an administrator                               # auth_iomadsaml2
    And I am on the iomadsaml2 settings page                     # auth_iomadsaml2
    When I change the setting "Mapping IdP" to "my:idpid"   # auth_iomadsaml2
    And I press "Save changes"
    Then I go to the iomadsaml2 settings page again              # auth_iomadsaml2
    And the setting "Mapping IdP" should be "my:idpid"      # auth_iomadsaml2
