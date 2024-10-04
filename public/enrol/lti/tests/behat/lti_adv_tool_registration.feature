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
    And I press "Continue"
    And I should see "Dynamic registration"
    And I should see "Manual registration"
    And I should see "Platform details"
    And I follow "Back"
    And "Manage deployments" "link" should exist in the "My test platform" "table_row"
    And "View platform details" "link" should exist in the "My test platform" "table_row"
    And "Delete" "link" should exist in the "My test platform" "table_row"
    And I should see "Pending" in the "My test platform" "table_row"
    And I click on "View platform details" "link" in the "My test platform" "table_row"
    And I follow "Edit platform details"
    And I set the following fields to these values:
      | Platform ID (issuer) | https://lms.example.com |
      | Client ID            | abcd1234                |
      | Authentication request URL | https://lms.example.com/auth |
      | Public keyset URL          | https://lms.example.com/jwks |
      | Access token URL           | https://lms.example.com/token |
    And I press "Save changes"
    Then I should see "Platform registration updated"
    And I should see "My test platform" in the "Platform name" "table_row"
    And I should see "https://lms.example.com" in the "Platform ID (issuer)" "table_row"
    And I should see "abcd1234" in the "Client ID" "table_row"
    And I should see "https://lms.example.com/auth" in the "Authentication request URL" "table_row"
    And I should see "https://lms.example.com/jwks" in the "Public keyset URL" "table_row"
    And I should see "https://lms.example.com/token" in the "Access token URL" "table_row"
    And I follow "Back"
    And I should see "https://lms.example.com" in the "My test platform" "table_row"
    And I should see "Active" in the "My test platform" "table_row"
    And "View platform details" "link" should exist in the "My test platform" "table_row"
    And "Manage deployments" "link" should exist in the "My test platform" "table_row"
    And "Delete" "link" should exist in the "My test platform" "table_row"

  Scenario: An admin can edit a platform's registration details
    Given the following "enrol_lti > application registrations" exist:
      | name | platformid | clientid | authrequesturl | jwksurl | accesstokenurl |
      | My test platform | https://lms.example.com | abcd1234 | https://lms.example.com/auth | https://lms.example.com/jwks | https://lms.example.com/token |
    And I log in as "admin"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I click on "View platform details" "link" in the "My test platform" "table_row"
    And I follow "Edit platform details"
    And I set the following fields to these values:
      | Platform name | Changed test platform |
      | Platform ID (issuer) | https://lms2.example.com |
      | Client ID            | wxyz9876                |
      | Authentication request URL | https://lms2.example.com/auth |
      | Public keyset URL          | https://lms2.example.com/jwks |
      | Access token URL           | https://lms2.example.com/token |
    And I press "Cancel"
    Then I should see "https://lms.example.com" in the "Platform ID (issuer)" "table_row"
    And I follow "Edit platform details"
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
    And I should see "https://lms2.example.com" in the "Platform ID (issuer)" "table_row"
    And I follow "Edit platform details"
    And the following fields match these values:
      | Platform name | Changed test platform |
      | Platform ID (issuer) | https://lms2.example.com |
      | Client ID            | wxyz9876                 |
      | Authentication request URL | https://lms2.example.com/auth |
      | Public keyset URL          | https://lms2.example.com/jwks |
      | Access token URL           | https://lms2.example.com/token |
    And I press "Cancel"
    And I follow "Back"
    And I should see "https://lms2.example.com" in the "Changed test platform" "table_row"

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
    And I press "Continue"
    And I follow "Edit platform details"
    And I set the following fields to these values:
      | Platform name | My test platform |
      | Platform ID (issuer) | https://lms.example.com |
      | Client ID            | abcd1234                |
      | Authentication request URL | https://lms.example.com/auth |
      | Public keyset URL          | https://lms.example.com/jwks |
      | Access token URL           | https://lms.example.com/token |
    And I press "Save changes"
    Then I should see "Invalid client ID. This client ID is already registered for the platform ID provided."

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
  Scenario: An admin can copy the manual and dynamic registration endpoints to register the tool with the platform
    Given the following "enrol_lti > application registrations" exist:
      | name |
      | My test platform |
    And I log in as "admin"
    And I change window size to "large"
    And I navigate to "Plugins > Enrolments > Publish as LTI tool > Tool registration" in site administration
    When I click on "View platform details" "link" in the "My test platform" "table_row"
    And I follow "Tool details"
    And the "value" attribute of "Registration URL" "field" should contain "enrol/lti/register.php"
    And the "value" attribute of "Tool URL" "field" should contain "enrol/lti/launch.php"
    And the "value" attribute of "Initiate login URL" "field" should contain "enrol/lti/login.php"
    And the "value" attribute of "JWKS URL" "field" should contain "enrol/lti/jwks.php"
    And the "value" attribute of "Deep linking URL" "field" should contain "enrol/lti/launch_deeplink.php"
    And "Copy to clipboard" "button" should exist in the "Registration URL" "table_row"
    And "Copy to clipboard" "button" should exist in the "Tool URL" "table_row"
    And "Copy to clipboard" "button" should exist in the "Initiate login URL" "table_row"
    And "Copy to clipboard" "button" should exist in the "JWKS URL" "table_row"
    And "Copy to clipboard" "button" should exist in the "Deep linking URL" "table_row"
    When I click on "Copy to clipboard" "button" in the "Registration URL" "table_row"
    Then I should see "Registration URL copied to clipboard"
    And I click on "Copy to clipboard" "button" in the "Tool URL" "table_row"
    And I should see "Tool URL copied to clipboard"
    And I click on "Copy to clipboard" "button" in the "Initiate login URL" "table_row"
    And I should see "Initiate login URL copied to clipboard"
    And I click on "Copy to clipboard" "button" in the "JWKS URL" "table_row"
    And I should see "JWKS URL copied to clipboard"
    And I click on "Copy to clipboard" "button" in the "Deep linking URL" "table_row"
    And I should see "Deep linking URL copied to clipboard"
