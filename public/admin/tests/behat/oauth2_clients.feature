@core @core_admin @core_reportbuilder @javascript @oauth2server
Feature: Create OAuth2 clients
  In order to integrate external applications with Moodle
  As an administrator
  I need to be able to create and manage OAuth2 clients

  Background:
    Given I log in as "admin"
    And I navigate to "Server > OAuth 2 clients" in site administration

  Scenario: Create a confidential OAuth2 client supporting authorization code and client credentials flows
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "1"
    And I set the field "Client Credentials" to "1"
    And I should see "Callback URIs"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And the "Proof Key for Code Exchange" "checkbox" should be enabled
    When I press "Create client"
    Then "Test Confidential Client" "heading" should exist
    And "Secrets" "heading" should exist
    And I click on "Go back to OAuth 2 clients" "link"
    And "OAuth 2 clients" "heading" should exist
    And the following should exist in the "reportbuilder-table" table:
      | Name                     | Type         | Status |
      | Test Confidential Client | Confidential | Active |
    And "Edit" "link" should exist in the "Test Confidential Client" "table_row"
    And "Manage secrets" "link" should exist in the "Test Confidential Client" "table_row"
    And "Revoke" "link" should exist in the "Test Confidential Client" "table_row"
    And "Delete" "link" should not exist in the "Test Confidential Client" "table_row"

  Scenario: Create a confidential OAuth2 client supporting client credentials flow only
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "0"
    And I set the field "Client Credentials" to "1"
    And I should not see "Callback URIs"
    And "Proof Key for Code Exchange" "field" should not be visible
    When I press "Create client"
    Then "Test Confidential Client" "heading" should exist
    And "Secrets" "heading" should exist
    And I click on "Go back to OAuth 2 clients" "link"
    And "OAuth 2 clients" "heading" should exist
    And the following should exist in the "reportbuilder-table" table:
      | Name                     | Type         | Status |
      | Test Confidential Client | Confidential | Active |
    And "Edit" "link" should exist in the "Test Confidential Client" "table_row"
    And "Manage secrets" "link" should exist in the "Test Confidential Client" "table_row"
    And "Revoke" "link" should exist in the "Test Confidential Client" "table_row"
    And "Delete" "link" should not exist in the "Test Confidential Client" "table_row"

  Scenario: Create a confidential OAuth2 client supporting authorization code flow only
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "1"
    And I set the field "Client Credentials" to "0"
    And I should see "Callback URIs"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And the "Proof Key for Code Exchange" "checkbox" should be enabled
    When I press "Create client"
    Then "Test Confidential Client" "heading" should exist
    And "Secrets" "heading" should exist
    And I click on "Go back to OAuth 2 clients" "link"
    And "OAuth 2 clients" "heading" should exist
    And the following should exist in the "reportbuilder-table" table:
      | Name                     | Type         | Status |
      | Test Confidential Client | Confidential | Active |
    And "Edit" "link" should exist in the "Test Confidential Client" "table_row"
    And "Manage secrets" "link" should exist in the "Test Confidential Client" "table_row"
    And "Revoke" "link" should exist in the "Test Confidential Client" "table_row"
    And "Delete" "link" should not exist in the "Test Confidential Client" "table_row"

  Scenario: Create a public OAuth2 client
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Public Client"
    And I set the field "Description" to "A test public OAuth2 client"
    And I click on "Public" "radio"
    And "Client Credentials" "field" should not be visible
    And the "Authorization Code" "field" should be disabled
    And I should see "Callback URIs"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And the "Proof Key for Code Exchange" "checkbox" should be disabled
    When I press "Create client"
    Then "OAuth 2 clients" "heading" should exist
    And "Secrets" "heading" should not exist
    And the following should exist in the "reportbuilder-table" table:
      | Name               | Type   | Status |
      | Test Public Client | Public | Active |
    And "Edit" "link" should exist in the "Test Public Client" "table_row"
    And "Revoke" "link" should exist in the "Test Public Client" "table_row"
    And "Manage secrets" "link" should not exist in the "Test Public Client" "table_row"
    And "Delete" "link" should not exist in the "Test Public Client" "table_row"

  Scenario: Validation checks prevent creating an invalid client
    Given I click on "Create client" "link"
    # Client name is not set.
    And I set the field "Name" to ""
    And I click on "Confidential" "radio"
    # Primary flow is not selected.
    And I set the field "Authorization Code" to "0"
    And I set the field "Client Credentials" to "0"
    When I press "Create client"
    Then I should see "You must supply a value here." in the "Name" "form_row"
    And I should see "You must select at least one primary flow." in the "Primary flows" "form_row"
    # Authorization code flow is selected but no callback URIs are provided.
    And I set the field "Name" to "Test Client"
    And I set the field "Authorization Code" to "1"
    And I press "Create client"
    And I should see "At least one valid Callback URI is required." in the "Callback URIs" "form_row"
    # Invalid callback URI is provided.
    And I set the field "redirecturi[0]" to "invalid-uri"
    And I press "Create client"
    And I should see "Must be a valid URL (e.g., https://example.com/callback)." in the "Callback URIs" "form_row"
    # Invalid callback URI is provided (no https or localhost).
    And I set the field "redirecturi[0]" to "http://example.com/callback"
    And I press "Create client"
    And I should see "Callback URIs must use HTTPS (http:// is only allowed for localhost)." in the "Callback URIs" "form_row"

  Scenario: Generate secrets for a confidential OAuth2 client
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "0"
    And I set the field "Client Credentials" to "1"
    And I press "Create client"
    And "Test Confidential Client" "heading" should exist
    And "Secrets" "heading" should exist
    And I should see "No active client secrets are currently configured."
    And "Generate secret" "button" should be visible
    And "reportbuilder-table" "table" should not exist
    When I press "Generate secret"
    Then "Secret generated" "dialogue" should exist
    And I should see "This secret is shown only once. Copy it now - it can't be retrieved again after you close this dialog." in the "Secret generated" "dialogue"
    And I should see "Client identifier" in the "Secret generated" "dialogue"
    And "Secret value" "field" should exist in the "Secret generated" "dialogue"
    And "Copy to clipboard" "button" should exist in the "Secret generated" "dialogue"
    And I click on "Copy to clipboard" "button" in the "Secret generated" "dialogue"
    And I should see "Client secret copied to clipboard."
    And I click on "Close" "button" in the "Secret generated" "dialogue"
    And "reportbuilder-table" "table" should exist
    And the following should exist in the "reportbuilder-table" table:
      | Status  | Actions |
      | Active  | Revoke  |

  Scenario: Client secrets can be revoked for a confidential OAuth2 client
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "0"
    And I set the field "Client Credentials" to "1"
    And I press "Create client"
    And I press "Generate secret"
    And I click on "Close" "button" in the "Secret generated" "dialogue"
    And the following should exist in the "reportbuilder-table" table:
      | Status  | Actions |
      | Active  | Revoke  |
    When I click on "Revoke" "link" in the "reportbuilder-table" "table"
    Then "Revoke client secret?" "dialogue" should exist
    And I should see "Any external applications or active integrations using this specific credential will instantly lose API access. This action is permanent and cannot be undone." in the "Revoke client secret?" "dialogue"
    And I click on "Revoke" "button" in the "Revoke client secret?" "dialogue"
    And "reportbuilder-table" "table" should not exist
    And I should see "No active client secrets are currently configured."

  Scenario: More than 2 secrets cannot be generated for a confidential OAuth2 client
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "0"
    And I set the field "Client Credentials" to "1"
    And I press "Create client"
    # Generate the first secret.
    And I press "Generate secret"
    And I click on "Close" "button" in the "Secret generated" "dialogue"
    # Generate the second secret.
    When I press "Generate secret"
    And I click on "Close" "button" in the "Secret generated" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Status | Actions |
      | Active | Revoke  |
      | Active | Revoke  |
    And "Generate secret" "button" should not be visible
    And I should see "Maximum of 2 active secrets reached. Revoke one before generating another."
    And I click on "Revoke" "link" in the "reportbuilder-table" "table"
    And I click on "Revoke" "button" in the "Revoke client secret?" "dialogue"
    And "Generate secret" "button" should be visible
    And I should not see "Maximum of 2 active secrets reached. Revoke one before generating another."

  Scenario: Edit confidential OAuth2 client that supports client credentials flow only
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "0"
    And I set the field "Client Credentials" to "1"
    And I press "Create client"
    And I click on "Go back to OAuth 2 clients" "link"
    And I click on "Edit" "link" in the "Test Confidential Client" "table_row"
    # Validate the layout of the edit client page.
    And "Test Confidential Client" "heading" should exist
    And "Details" "heading" should exist
    And "#client-status" "css_element" should exist
    And "#client-type" "css_element" should exist
    And "#client-flows" "css_element" should exist
    And "#client-id" "css_element" should exist
    And "#client-active-secrets" "css_element" should exist
    And I should see "Active" in the "#client-status" "css_element"
    And I should see "Confidential" in the "#client-type" "css_element"
    And I should see "Client Credentials" in the "#client-flows" "css_element"
    And I should see "0" in the "#client-active-secrets" "css_element"
    And "Manage secrets" "link" should exist in the "#client-active-secrets" "css_element"
    And the following fields match these values:
      | Name        | Test Confidential Client |
      | Description | A test confidential OAuth2 client |
    And "Confidential" "radio" should not exist
    And "Public" "radio" should not exist
    And "Authorization Code" "field" should not exist
    And "Client Credentials" "field" should not exist
    And "Callback URIs" "field" should not exist
    And "Proof Key for Code Exchange" "field" should not exist
    # Edit the client name and description.
    And I set the field "Name" to "Updated Confidential Client"
    And I set the field "Description" to "Updated confidential OAuth2 client"
    When I press "Save changes"
    And I click on "Go back to OAuth 2 clients" "link"
    Then the following should exist in the "reportbuilder-table" table:
      | Name                             |
      | Updated Confidential Client |
    And the following should not exist in the "reportbuilder-table" table:
      | Name                     |
      | Test Confidential Client |
    And I click on "Edit" "link" in the "Updated Confidential Client" "table_row"
    And the following fields match these values:
      | Name        | Updated Confidential Client |
      | Description | Updated confidential OAuth2 client |

  Scenario: Edit confidential OAuth2 client that supports authorization code and client credentials flows
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "1"
    And I set the field "Client Credentials" to "1"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And I press "Create client"
    And I click on "Go back to OAuth 2 clients" "link"
    And I click on "Edit" "link" in the "Test Confidential Client" "table_row"
    # Validate the layout of the edit client page.
    And "Test Confidential Client" "heading" should exist
    And "Details" "heading" should exist
    And "#client-status" "css_element" should exist
    And "#client-type" "css_element" should exist
    And "#client-flows" "css_element" should exist
    And "#client-id" "css_element" should exist
    And "#client-active-secrets" "css_element" should exist
    And I should see "Active" in the "#client-status" "css_element"
    And I should see "Confidential" in the "#client-type" "css_element"
    And I should see "Authorization Code" in the "#client-flows" "css_element"
    And I should see "Client Credentials" in the "#client-flows" "css_element"
    And I should see "0" in the "#client-active-secrets" "css_element"
    And "Manage secrets" "link" should exist in the "#client-active-secrets" "css_element"
    And the following fields match these values:
      | Name           | Test Confidential Client          |
      | Description    | A test confidential OAuth2 client |
      | redirecturi[0] | https://example.com/callback      |
    And "Confidential" "radio" should not exist
    And "Public" "radio" should not exist
    And "Authorization Code" "field" should not exist
    And "Client Credentials" "field" should not exist
    And "redirecturi[0]" "field" should exist
    And "Proof Key for Code Exchange" "field" should not exist
    # Edit the client name, description and redirect URIs.
    And I set the field "Name" to "Updated Confidential Client"
    And I set the field "Description" to "Updated confidential OAuth2 client"
    And I click on "Add another callback URI" "button"
    And I set the field "redirecturi[0]" to "https://example.com/callback-updated"
    And I set the field "redirecturi[1]" to "https://example.com/another-callback"
    When I press "Save changes"
    And I click on "Go back to OAuth 2 clients" "link"
    Then the following should exist in the "reportbuilder-table" table:
      | Name                             |
      | Updated Confidential Client |
    And the following should not exist in the "reportbuilder-table" table:
      | Name                     |
      | Test Confidential Client |
    And I click on "Edit" "link" in the "Updated Confidential Client" "table_row"
    And the following fields match these values:
      | Name           | Updated Confidential Client          |
      | Description    | Updated confidential OAuth2 client   |
      | redirecturi[0] | https://example.com/callback-updated |
      | redirecturi[1] | https://example.com/another-callback |

  Scenario: Edit public OAuth2 client
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Public Client"
    And I set the field "Description" to "A test public OAuth2 client"
    And I click on "Public" "radio"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And I press "Create client"
    And I click on "Edit" "link" in the "Test Public Client" "table_row"
    # Validate the layout of the edit client page.
    And "Test Public Client" "heading" should exist
    And "Details" "heading" should exist
    And "#client-status" "css_element" should exist
    And "#client-type" "css_element" should exist
    And "#client-flows" "css_element" should exist
    And "#client-id" "css_element" should exist
    And "#client-active-secrets" "css_element" should not exist
    And I should see "Active" in the "#client-status" "css_element"
    And I should see "Public" in the "#client-type" "css_element"
    And I should see "Authorization Code" in the "#client-flows" "css_element"
    And the following fields match these values:
      | Name           | Test Public Client           |
      | Description    | A test public OAuth2 client  |
      | redirecturi[0] | https://example.com/callback |
    And "Confidential" "radio" should not exist
    And "Public" "radio" should not exist
    And "Authorization Code" "field" should not exist
    And "Client Credentials" "field" should not exist
    And "redirecturi[0]" "field" should exist
    And "Proof Key for Code Exchange" "field" should not exist
    # Edit the client name, description and redirect URIs.
    And I set the field "Name" to "Updated Public Client"
    And I set the field "Description" to "Updated public OAuth2 client"
    And I click on "Add another callback URI" "button"
    And I set the field "redirecturi[0]" to "https://example.com/callback-updated"
    And I set the field "redirecturi[1]" to "https://example.com/another-callback"
    When I press "Save changes"
    And I click on "Go back to OAuth 2 clients" "link"
    Then the following should exist in the "reportbuilder-table" table:
      | Name                  |
      | Updated Public Client |
    And the following should not exist in the "reportbuilder-table" table:
      | Name               |
      | Test Public Client |
    And I click on "Edit" "link" in the "Updated Public Client" "table_row"
    And the following fields match these values:
      | Name           | Updated Public Client          |
      | Description    | Updated public OAuth2 client   |
      | redirecturi[0] | https://example.com/callback-updated |
      | redirecturi[1] | https://example.com/another-callback |

  Scenario: Revoke OAuth2 client
    # Create a confidential OAuth2 client supporting authorization code and client credentials flows.
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "1"
    And I set the field "Client Credentials" to "1"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And I press "Create client"
    # Generate a secret for the client.
    And I press "Generate secret"
    And I click on "Close" "button" in the "Secret generated" "dialogue"
    And the following should exist in the "reportbuilder-table" table:
      | Status  | Actions |
      | Active  | Revoke  |
    And I click on "Go back to OAuth 2 clients" "link"
    # Revoke the client.
    And I click on "Revoke" "link" in the "Test Confidential Client" "table_row"
    And "Revoke \"Test Confidential Client\"?" "dialogue" should exist
    And I should see "This immediately revokes all active secrets and stops the client from authenticating. The client and its configuration are kept, and it can be re-enabled later. To permanently remove it instead, revoke it first, then delete." in the "Revoke \"Test Confidential Client\"?" "dialogue"
    When I click on "Revoke" "button" in the "Revoke \"Test Confidential Client\"?" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name                     | Status  |
      | Test Confidential Client | Revoked |
    And "Revoke" "link" should not exist in the "Test Confidential Client" "table_row"
    # Client secrets should be revoked and not longer managed when the client is revoked and cannot be .
    And "Manage secrets" "link" should not exist in the "Test Confidential Client" "table_row"
    And I click on "Edit" "link" in the "Test Confidential Client" "table_row"
    And "#client-active-secrets" "css_element" should not exist

  Scenario: Enable OAuth2 client
    # Create a confidential OAuth2 client supporting authorization code and client credentials flows.
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Confidential Client"
    And I set the field "Description" to "A test confidential OAuth2 client"
    And I click on "Confidential" "radio"
    And I set the field "Authorization Code" to "1"
    And I set the field "Client Credentials" to "1"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And I press "Create client"
    # Generate a secret for the client.
    And I press "Generate secret"
    And I click on "Close" "button" in the "Secret generated" "dialogue"
    And the following should exist in the "reportbuilder-table" table:
      | Status  | Actions |
      | Active  | Revoke  |
    And I click on "Go back to OAuth 2 clients" "link"
    # Revoke the client.
    And I click on "Revoke" "link" in the "Test Confidential Client" "table_row"
    And I click on "Revoke" "button" in the "Revoke \"Test Confidential Client\"?" "dialogue"
    # Enable the client.
    And "Enable" "link" should exist in the "Test Confidential Client" "table_row"
    And I click on "Enable" "link" in the "Test Confidential Client" "table_row"
    And "Re-enable \"Test Confidential Client\"?" "dialogue" should exist
    And I should see "This will restore this application's access to the authorization server. Any previously issued tokens remain invalid, and the client will need to re-authenticate or generate a new secret to resume normal operations." in the "Re-enable \"Test Confidential Client\"?" "dialogue"
    When I click on "Enable" "button" in the "Re-enable \"Test Confidential Client\"?" "dialogue"
    Then the following should exist in the "reportbuilder-table" table:
      | Name                     | Status  |
      | Test Confidential Client | Active  |
    And "Revoke" "link" should exist in the "Test Confidential Client" "table_row"
    And "Manage secrets" "link" should exist in the "Test Confidential Client" "table_row"
    And I click on "Edit" "link" in the "Test Confidential Client" "table_row"
    And "#client-active-secrets" "css_element" should exist
    And I click on "Manage secrets" "link" in the "#client-active-secrets" "css_element"
    # Revoked secrets cannot be re-enabled and must be regenerated.
    And I should see "No active client secrets are currently configured."

  Scenario: Delete OAuth2 client
    # Create a public OAuth2 client.
    Given I click on "Create client" "link"
    And I set the field "Name" to "Test Public Client"
    And I set the field "Description" to "A test public OAuth2 client"
    And I click on "Public" "radio"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And I press "Create client"
    # Create another public OAuth2 client.
    Given I click on "Create client" "link"
    And I set the field "Name" to "Another Public Client"
    And I set the field "Description" to "Another public OAuth2 client"
    And I click on "Public" "radio"
    And I set the field "redirecturi[0]" to "https://example.com/callback"
    And I press "Create client"
    And "Delete" "link" should not exist in the "Test Public Client" "table_row"
    # Revoke the client.
    And I click on "Revoke" "link" in the "Test Public Client" "table_row"
    And I click on "Revoke" "button" in the "Revoke \"Test Public Client\"?" "dialogue"
    # Delete the client.
    And "Delete" "link" should exist in the "Test Public Client" "table_row"
    And I click on "Delete" "link" in the "Test Public Client" "table_row"
    And "Delete \"Test Public Client\"?" "dialogue" should exist
    And I should see "This permanently removes the client, its secrets, and its redirect URIs. Unlike revoking, this can't be undone. Any integration using this client will stop working immediately." in the "Delete \"Test Public Client\"?" "dialogue"
    When I click on "Delete" "button" in the "Delete \"Test Public Client\"?" "dialogue"
    Then the following should not exist in the "reportbuilder-table" table:
      | Name               |
      | Test Public Client |
    And the following should exist in the "reportbuilder-table" table:
      | Name                  |
      | Another Public Client |
