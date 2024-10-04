@mod @mod_glossary
Feature: Glossary can set autolinked entries in text and media areas
  In order to display the glossary entries for concepts in texts
  As a teacher
  I can set the glossary activity to autolink the entries

  Background:
    Given remote langimport tests are enabled
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity | name                                                                                                                                                                 | intro                     | displayformat | course | idnumber  |
      | glossary | Test <span class="multilang" lang="en">glossary</span><span class="multilang" lang="fr">glossaire</span><span class="multilang" lang="es">glossario</span> | Test glossary description | encyclopedia  | C1     | glossary1 |
    And the following "mod_glossary > entries" exist:
      | glossary  | concept                                                                                                                                               | definition                                                                                                                                                                                                                                                                                                                | usedynalink |
      | glossary1 | <span class="multilang" lang="en">English</span><span class="multilang" lang="fr">Anglais</span><span class="multilang" lang="es">inglés</span>       | <span class="multilang" lang="en">Relating to England, its people, or the language spoken there.</span><span class="multilang" lang="fr">Relatif à l'Angleterre, à son peuple ou à la langue parlée là-bas.</span><span class="multilang" lang="es">Relacionado con Inglaterra, su gente o el idioma hablado allí.</span> | 1           |
      | glossary1 | <span class="multilang" lang="en">Spanish</span><span class="multilang" lang="fr">Espagnol</span><span class="multilang" lang="es">Castellano</span> | <span class="multilang" lang="en">Relating to Spain, its people, or the language spoken there.</span><span class="multilang" lang="fr">Relatif à l'Espagne, à son peuple ou à la langue parlée là-bas.</span><span class="multilang" lang="es">Relacionado con España, su gente o el idioma hablado allí.</span>          | 1           |
    And the following "activities" exist:
      | activity | name                | intro                                                                                                                                                                                                                                                                                                                              | course | idnumber |
      | label    | Text and media area | <p>This is a text with the multilang syntax on the <span class="multilang" lang="en">English</span><span class="multilang" lang="fr">Anglais</span><span class="multilang" lang="es">Inglés</span> word that should be auto-linked.</p><p>This are plain text words that should also be auto-linked: English, Anglais, Inglés.</p> | C1     | label1   |
    And the "glossary" filter is "on"
    And the following "language pack" exists:
      | language | fr | es |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"

  @javascript
  Scenario: Glossary entries show up in text and media areas in the correct user interface/language combination
    When I am on the "Course 1" course page logged in as teacher1
    Then "English" "link" should exist in the ".modtype_label" "css_element"
    And "Anglais" "link" should not exist in the ".modtype_label" "css_element"
    And "Inglés" "link" should not exist in the ".modtype_label" "css_element"
    And the "title" attribute of ".glossary.autolink" "css_element" should contain "Test glossary: English"
    And I follow "Preferences" in the user menu
    And I follow "Preferred language"
    # Change preferred language to Spanish.
    And I set the field "Preferred language" to "es"
    And I press "Save changes"
    And I am on "Course 1" course homepage
    Then "English" "link" should not exist in the ".modtype_label" "css_element"
    And "Anglais" "link" should not exist in the ".modtype_label" "css_element"
    And "Inglés" "link" should exist in the ".modtype_label" "css_element"
    And the "title" attribute of ".glossary.autolink" "css_element" should contain "Test glossario: inglés"
    And I follow "Preferencias" in the user menu
    And I follow "Idioma preferido"
    # Change preferred language to French.
    And I set the field "Idioma preferido" to "fr"
    And I press "Guardar cambios"
    And I am on "Course 1" course homepage
    Then "English" "link" should not exist in the ".modtype_label" "css_element"
    And "Anglais" "link" should exist in the ".modtype_label" "css_element"
    And "Inglés" "link" should not exist in the ".modtype_label" "css_element"
    And the "title" attribute of ".glossary.autolink" "css_element" should contain "Test glossaire : Anglais"
