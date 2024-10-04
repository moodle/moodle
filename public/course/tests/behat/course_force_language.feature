@core_course
Feature: Force course language
  To be able to force a language for the course
  As a teacher
  I need to be able to set force language setting for the course

  Background:
    Given remote langimport tests are enabled
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | One      | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "language pack" exists:
      | language | fr | es |

  Scenario: A teacher can set a course language
    Given I am on the "Course 1" course page logged in as "teacher1"
    # Site default language is English.
    And I should see "Settings"
    And I follow "Preferences" in the user menu
    And I follow "Preferred language"
    # Change preferred language to French.
    And I set the field "Preferred language" to "fr"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    And I should see "Paramètres"
    # Change course language to Spanish.
    And I navigate to "Paramètres" in current page administration
    When I set the following fields to these values:
      | id_lang | es |
    And I press "Enregistrer et afficher"
    # Course language is now Spanish.
    Then I should see "Configuración"
