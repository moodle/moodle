@javascript @theme_boost
Feature: Language selector menu
  To be able to set the preferred language for the site
  As a user
  I need to be presented with a language selector menu

  Background:
    Given remote langimport tests are enabled
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Available language packs" to "en_ar"
    And I press "Install selected language pack(s)"
    And the "Installed language packs" select box should contain "en_ar"
    And I log out

  Scenario: Logged user is presented with a language selector which is placed within the user menu
    Given I log in as "teacher1"
    And I am on site homepage
    # The language selector menu is not present in the navbar when a user is logged in.
    And language selector menu should not exist in the navbar
    # The language selector is present within the user menu.
    And "Language" "link" should exist in the user menu
    When I follow "Language" in the user menu
    Then I should see "Language selector" user submenu
    And "English ‎(en)‎" "link" should exist in the "Language selector" user submenu
    And "English (pirate) ‎(en_ar)‎" "link" should exist in the "Language selector" user submenu

  Scenario: Non-logged user is presented with a language selector which is placed within the navbar
    Given I am on site homepage
    # The language selector menu is present in the navbar when a user is not logged in.
    And language selector menu should exist in the navbar
    And "English ‎(en)‎" "link" should exist in the language selector menu
    And "English (pirate) ‎(en_ar)‎" "link" should exist in the language selector menu

  Scenario: Logged user is not presented with a language selector in a course if a language is forced in that context
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And I set the following fields to these values:
      | id_lang | en |
    And I press "Save and display"
    # The language selector is not present within the user menu in the course context when a language is enforced.
    When I am on "Course 1" course homepage
    And "Language" "link" should not exist in the user menu
    # The language selector is present within the user menu in other contexts.
    And I am on site homepage
    And "Language" "link" should exist in the user menu

  Scenario: Logged user is not presented with a language selector if there is less than two installed languages
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Installed language packs" to "en_ar"
    And I press "Uninstall selected language pack(s)"
    And I click on "Yes" "button" in the "Uninstall selected language pack(s)" "dialogue"
    And the "Installed language packs" select box should not contain "en_ar"
    When I am on site homepage
    # The language selector is not present within the user menu.
    And "Language" "link" should not exist in the user menu

  Scenario: Non-logged user is not presented with a language selector if there is less than two installed languages
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Installed language packs" to "en_ar"
    And I press "Uninstall selected language pack(s)"
    And I click on "Yes" "button" in the "Uninstall selected language pack(s)" "dialogue"
    And the "Installed language packs" select box should not contain "en_ar"
    And I log out
    When I am on site homepage
    # The language selector menu is not present in the navbar.
    Then language selector menu should not exist in the navbar
