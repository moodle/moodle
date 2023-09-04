@auth @auth_iomadsaml2 @javascript
Feature: Self tests
  In order to test for known configuration issues
  As any user (not login required)
  I should be able to run self tests

  Scenario: Access the self test page
    Given the authentication plugin iomadsaml2 is enabled  # auth_iomadsaml2
    And the mock SAML IdP is configured               # auth_iomadsaml2
    When I go to the self-test page                   # auth_iomadsaml2
    Then I should not see "Error"
