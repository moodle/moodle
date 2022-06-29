@tool @tool_langimport
Feature: Manage language packs
  In order to support different languages
  As an administrator
  I need to be able to add, update and remove language packs

  Background:
    Given remote langimport tests are enabled

  # The pirate language pack is used for testing because its small to download.

  Scenario: Install language pack
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    When I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    Then I should see "Language pack 'en_ar' was successfully installed"
    And the "Installed language packs" select box should contain "en_ar"
    And I navigate to "Reports > Live logs" in site administration
    And I should see "The language pack 'en_ar' was installed."

  Scenario: Install multiple language packs asynchronously in the background
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Available language packs" to "en_us,en_us_k12"
    When I press "Install selected language pack(s)"
    Then I should see "Language packs scheduled for installation."
    And I should see "The following language packs will be installed soon: en_us, en_us_k12."
    And I trigger cron
    And I am on homepage
    And I navigate to "Language > Language packs" in site administration
    And the "Installed language packs" select box should contain "en_us"
    And the "Installed language packs" select box should contain "en_us_k12"
    And I navigate to "Reports > Live logs" in site administration
    And I should see "The language pack 'en_us' was installed."
    And I should see "The language pack 'en_us_k12' was installed."

  @javascript
  Scenario: Search for available language pack
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    When I set the field "Search available language packs" to "pirate"
    Then the "Available language packs" select box should not contain "es"
    And I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    And I should see "Language pack 'en_ar' was successfully installed"

  Scenario: Update language pack
    Given outdated langpack 'en_ar' is installed
    And I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    When I press "Update all installed language packs"
    Then I should see "Language pack 'en_ar' was successfully updated"
    And I should see "Language pack update completed"
    And I navigate to "Reports > Live logs" in site administration
    And I should see "The language pack 'en_ar' was updated."

  Scenario: Inform admin that there are multiple installed languages and updating them all can take too long
    Given outdated langpack 'en_ar' is installed
    And outdated langpack 'en_us' is installed
    And outdated langpack 'en_us_k12' is installed
    When I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    Then I should see "Updating all installed language packs by clicking the button can take a long time and lead to timeouts."

  Scenario: Try to uninstall language pack
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    When I set the field "Installed language packs" to "en_ar"
    And I press "Uninstall selected language pack(s)"
    And I press "Continue"
    Then I should see "Language pack 'en_ar' was uninstalled"
    And the "Installed language packs" select box should not contain "en_ar"
    And the "Available language packs" select box should contain "en_ar"
    And I navigate to "Reports > Live logs" in site administration
    And I should see "The language pack 'en_ar' was removed."
    And I should see "Language pack uninstalled"

  Scenario: Try to uninstall English language pack
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    When I set the field "Installed language packs" to "en"
    And I press "Uninstall selected language pack(s)"
    Then I should see "The English language pack cannot be uninstalled."
    And I navigate to "Reports > Live logs" in site administration
    And I should not see "Language pack uninstalled"
