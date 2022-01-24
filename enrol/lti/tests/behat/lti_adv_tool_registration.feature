@enrol @enrol_lti
Feature: Register a platform with the tool
  In order to share and consume a Moodle resource or activity over LTI Advantage
  As an admin
  I need to be able to manage platform registrations in the tool

  Background:
    Given I enable "lti" "enrol" plugin

  Scenario: An admin can register a platform with the tool
    Given I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I follow "Register a platform"
    And I set the following fields to these values:
      | Platform name | My test platform |
      | Platform ID (issuer) | https://lms.example.com |
      | Client ID            | abcd1234                |
      | Authentication request URL | https://lms.example.com/auth |
      | Public keyset URL          | https://lms.example.com/jwks |
      | Access token URL           | https://lms.example.com/token |
    And I press "Save changes"
    Then I should see "Platform registration added"
    And I should see "https://lms.example.com" in the "My test platform" "table_row"
    And "Edit" "link" should exist in the "My test platform" "table_row"
    And "Manage deployments" "link" should exist in the "My test platform" "table_row"
    And "Delete" "link" should exist in the "My test platform" "table_row"

  Scenario: An admin can edit a platform's registration details
    Given the following "enrol_lti > application registrations" exist:
      | name | platformid | clientid | authrequesturl | jwksurl | accesstokenurl |
      | My test platform | https://lms.example.com | abcd1234 | https://lms.example.com/auth | https://lms.example.com/jwks | https://lms.example.com/token |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I click on "Edit" "link" in the "My test platform" "table_row"
    And I set the following fields to these values:
      | Platform name | Changed test platform |
      | Platform ID (issuer) | https://lms2.example.com |
      | Client ID            | wxyz9876                |
      | Authentication request URL | https://lms2.example.com/auth |
      | Public keyset URL          | https://lms2.example.com/jwks |
      | Access token URL           | https://lms2.example.com/token |
    And I press "Cancel"
    Then I should see "https://lms.example.com" in the "My test platform" "table_row"
    And I click on "Edit" "link" in the "My test platform" "table_row"
    And the following fields match these values:
      | Platform name | My test platform |
      | Platform ID (issuer) | https://lms.example.com |
      | Client ID            | abcd1234                |
      | Authentication request URL | https://lms.example.com/auth |
      | Public keyset URL          | https://lms.example.com/jwks |
      | Access token URL           | https://lms.example.com/token |
    And I set the following fields to these values:
      | Platform name | Changed test platform |
      | Platform ID (issuer) | https://lms2.example.com |
      | Client ID            | wxyz9876                |
      | Authentication request URL | https://lms2.example.com/auth |
      | Public keyset URL          | https://lms2.example.com/jwks |
      | Access token URL           | https://lms2.example.com/token |
    And I press "Save changes"
    And I should see "https://lms2.example.com" in the "Changed test platform" "table_row"
    And I click on "Edit" "link" in the "Changed test platform" "table_row"
    And the following fields match these values:
      | Platform name | Changed test platform |
      | Platform ID (issuer) | https://lms2.example.com |
      | Client ID            | wxyz9876                 |
      | Authentication request URL | https://lms2.example.com/auth |
      | Public keyset URL          | https://lms2.example.com/jwks |
      | Access token URL           | https://lms2.example.com/token |

  Scenario: An admin can delete a platform registration
    Given the following "enrol_lti > application registrations" exist:
      | name | platformid | clientid | authrequesturl | jwksurl | accesstokenurl |
      | My test platform | https://lms.example.com | abcd1234 | https://lms.example.com/auth | https://lms.example.com/jwks | https://lms.example.com/token |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I click on "Delete" "link" in the "My test platform" "table_row"
    And I press "Cancel"
    Then I should see "https://lms.example.com" in the "My test platform" "table_row"
    And I click on "Delete" "link" in the "My test platform" "table_row"
    And I press "Continue"
    And I should see "Platform registration deleted"
    And I should not see "My test platform"

  Scenario: A platform registration's unique platformid:clientid tuple must be enforced
    Given the following "enrol_lti > application registrations" exist:
      | name | platformid | clientid | authrequesturl | jwksurl | accesstokenurl |
      | My test platform | https://lms.example.com | abcd1234 | https://lms.example.com/auth | https://lms.example.com/jwks | https://lms.example.com/token |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I follow "Register a platform"
    And I set the following fields to these values:
      | Platform name | My test platform |
      | Platform ID (issuer) | https://lms.example.com |
      | Client ID            | abcd1234                |
      | Authentication request URL | https://lms.example.com/auth |
      | Public keyset URL          | https://lms.example.com/jwks |
      | Access token URL           | https://lms.example.com/token |
    And I press "Save changes"
    Then I should see "Invalid Client ID. This Client ID is already registered for the Platform ID provided."

  Scenario: An admin can add deployment ids for a given platform registration
    Given the following "enrol_lti > application registrations" exist:
      | name | platformid | clientid | authrequesturl | jwksurl | accesstokenurl |
      | My test platform | https://lms.example.com | abcd1234 | https://lms.example.com/auth | https://lms.example.com/jwks | https://lms.example.com/token |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I click on "Manage deployments" "link" in the "My test platform" "table_row"
    And I should see "No tool deployments found"
    And I follow "Add a deployment"
    And I set the following fields to these values:
      | Deployment name | Sitewide deployment of Moodle on platform x |
      | Deployment ID   | 1a2b3c                                      |
    And I press "Cancel"
    And I should not see "Sitewide deployment of Moodle on platform x"
    And I should see "No tool deployments found"
    And I follow "Add a deployment"
    And I set the following fields to these values:
      | Deployment name | Sitewide deployment of Moodle on platform x |
      | Deployment ID | 1a2b3c                                        |
    And I press "Save changes"
    Then I should see "Deployment added"
    And I should see "1a2b3c" in the "Sitewide deployment of Moodle on platform x" "table_row"
    And I should not see "No tool deployments found"
    And "Delete" "link" should exist in the "Sitewide deployment of Moodle on platform x" "table_row"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    And "1" "link" should exist in the "My test platform" "table_row"
    And I click on "1" "link" in the "My test platform" "table_row"
    And I should see "1a2b3c" in the "Sitewide deployment of Moodle on platform x" "table_row"
    And I follow "Add a deployment"
    And I set the following fields to these values:
      | Deployment name | Course context deployment of Moodle on platform x |
      | Deployment ID | 4d5e6f                                              |
    And I press "Save changes"
    And I should see "4d5e6f" in the "Course context deployment of Moodle on platform x" "table_row"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    And "2" "link" should exist in the "My test platform" "table_row"

  Scenario: An admin can remove a deployment id for a platform registration
    Given the following "enrol_lti > application registrations" exist:
      | name | platformid | clientid | authrequesturl | jwksurl | accesstokenurl | deploymentname | deploymentid |
      | My test platform | https://lms.example.com | abcd1234 | https://lms.example.com/auth | https://lms.example.com/jwks | https://lms.example.com/token | Site deployment | 1a2b3c |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    And I should see "1" in the "My test platform" "table_row"
    And I click on "Manage deployments" "link" in the "My test platform" "table_row"
    And I should see "1a2b3c" in the "Site deployment" "table_row"
    When I click on "Delete" "link" in the "Site deployment" "table_row"
    And I press "Cancel"
    Then I should see "1a2b3c" in the "Site deployment" "table_row"
    And I click on "Delete" "link" in the "Site deployment" "table_row"
    And I press "Continue"
    And I should see "Deployment deleted"
    And I should see "No tool deployments found"
    And I should not see "1a2b3c"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    And "1" "link" should not exist in the "My test platform" "table_row"

  @javascript
  Scenario: An admin can copy the manual registration endpoints to register the tool with the platform manually
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    And "Manual registration" "link" should exist
    And I click on "Manual registration" "link"
    And the "value" attribute of "Tool URL" "field" should contain "enrol/lti/launch.php"
    And the "value" attribute of "Initiate login URL" "field" should contain "enrol/lti/login.php"
    And the "value" attribute of "JWKS URL" "field" should contain "enrol/lti/jwks.php"
    And the "value" attribute of "Deep linking URL" "field" should contain "enrol/lti/launch_deeplink.php"
    And "Copy to clipboard" "link" should exist in the "Tool URL" "table_row"
    And "Copy to clipboard" "link" should exist in the "Initiate login URL" "table_row"
    And "Copy to clipboard" "link" should exist in the "JWKS URL" "table_row"
    And "Copy to clipboard" "link" should exist in the "Deep linking URL" "table_row"
    When I click on "Copy to clipboard" "link" in the "Tool URL" "table_row"
    Then I should see "Tool URL copied to clipboard"
    And I click on "Copy to clipboard" "link" in the "Initiate login URL" "table_row"
    And I should see "Initiate login URL copied to clipboard"
    And I click on "Copy to clipboard" "link" in the "JWKS URL" "table_row"
    And I should see "JWKS URL copied to clipboard"
    And I click on "Copy to clipboard" "link" in the "Deep linking URL" "table_row"
    And I should see "Deep linking URL copied to clipboard"

  @javascript
  Scenario: An admin can create a dynamic registration URL for use by platforms
    Given I log in as "admin"
    And I change window size to "large"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    And "Dynamic registration" "text" should exist
    And "Generate registration URL" "button" should exist
    When I press "Generate registration URL"
    # The button will be aria disabled and contain some guiding text, so verify this.
    Then the "class" attribute of "Generate registration URL" "button" should contain "disabled"
    And the "aria-disabled" attribute of "Generate registration URL" "button" should contain "true"
    And the "aria-label" attribute of "Generate registration URL" "button" should contain "You must use or delete the current registration URL before you can create a new one."
    And "Registration URL" "field" should be visible
    And the "Registration URL" "field" should be readonly
    And the "value" attribute of "Registration URL" "field" should contain "enrol/lti/register.php?token="
    And "Copy to clipboard" "link" should exist
    And "Delete" "link" should exist
    And I click on "Copy to clipboard" "link"
    And I should see "Registration URL copied to clipboard"
    And I click on "Delete" "link"
    And I click on "Cancel" "button" in the ".modal-dialog" "css_element"
    And "Registration URL" "field" should exist
    And I click on "Delete" "link"
    And I click on "Delete registration URL" "button" in the ".modal-dialog" "css_element"
    And I should see "Registration URL deleted"
    And "Registration URL" "field" should not be visible
