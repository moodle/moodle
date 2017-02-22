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
    And I navigate to "Language packs" node in "Site administration > Language"
    When I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    Then I should see "Language pack 'en_ar' was successfully installed"
    And the "Installed language packs" select box should contain "en_ar"
    And I navigate to "Live logs" node in "Site administration > Reports"
    And I should see "The language pack 'en_ar' was installed."
    And I log out

  Scenario: Update language pack
    Given outdated langpack 'en_ar' is installed
    And I log in as "admin"
    And I navigate to "Language packs" node in "Site administration > Language"
    When I press "Update all installed language packs"
    Then I should see "Language pack 'en_ar' was successfully updated"
    And I should see "Language pack update completed"
    And I navigate to "Live logs" node in "Site administration > Reports"
    And I should see "The language pack 'en_ar' was updated."
    And I log out

  Scenario: Try to uninstall language pack
    Given I log in as "admin"
    And I navigate to "Language packs" node in "Site administration > Language"
    And I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    When I set the field "Installed language packs" to "en_ar"
    And I press "Uninstall selected language pack(s)"
    And I press "Continue"
    Then I should see "Language pack 'en_ar' was uninstalled"
    And the "Installed language packs" select box should not contain "en_ar"
    And the "Available language packs" select box should contain "en_ar"
    And I navigate to "Live logs" node in "Site administration > Reports"
    And I should see "The language pack 'en_ar' was removed."
    And I should see "Language pack uninstalled"
    And I log out

  Scenario: Try to uninstall English language pack
    Given I log in as "admin"
    And I navigate to "Language packs" node in "Site administration > Language"
    When I set the field "Installed language packs" to "en"
    And I press "Uninstall selected language pack(s)"
    Then I should see "The English language pack cannot be uninstalled."
    And I navigate to "Live logs" node in "Site administration > Reports"
    And I should not see "Language pack uninstalled"
    And I log out
