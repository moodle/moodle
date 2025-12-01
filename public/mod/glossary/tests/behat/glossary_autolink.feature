@mod @mod_glossary
Feature: Glossary entries are displayed properly when autolinked
  In order to display linked glossary entries for concepts
  As an admin
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
      | activity | name                                                                                                                                                       | intro                           | displayformat | course               | idnumber  | globalglossary |
      | glossary | Test <span class="multilang" lang="en">glossary</span><span class="multilang" lang="fr">glossaire</span><span class="multilang" lang="es">glossario</span> | Test glossary description       | encyclopedia  | C1                   | glossary1 | 0              |
      | glossary | Autolinked Glossary                                                                                                                                        | Autolinked glossary description | encyclopedia  | Acceptance test site | glossary2 | 1              |
    And the following "mod_glossary > entries" exist:
      | glossary  | concept                                                                                                                                               | definition                                                                                                                                                                                                                                                                                                                | usedynalink |
      | glossary1 | <span class="multilang" lang="en">English</span><span class="multilang" lang="fr">Anglais</span><span class="multilang" lang="es">inglés</span>       | <span class="multilang" lang="en">Relating to England, its people, or the language spoken there.</span><span class="multilang" lang="fr">Relatif à l'Angleterre, à son peuple ou à la langue parlée là-bas.</span><span class="multilang" lang="es">Relacionado con Inglaterra, su gente o el idioma hablado allí.</span> | 1           |
      | glossary1 | <span class="multilang" lang="en">Spanish</span><span class="multilang" lang="fr">Espagnol</span><span class="multilang" lang="es">Castellano</span>  | <span class="multilang" lang="en">Relating to Spain, its people, or the language spoken there.</span><span class="multilang" lang="fr">Relatif à l'Espagne, à son peuple ou à la langue parlée là-bas.</span><span class="multilang" lang="es">Relacionado con España, su gente o el idioma hablado allí.</span>          | 1           |
      | glossary2 | Linked entry                                                                                                                                          | This is the linked entry definition                                                                                                                                                                                                                                                                                       | 1           |
      | glossary2 | Normal entry                                                                                                                                          | This is the normal entry definition                                                                                                                                                                                                                                                                                       | 0           |
    And the following "activities" exist:
      | activity | name                | intro                                                                                                                                                                                                                                                                                                                              | course | idnumber | content                                         | showdescription |
      | label    | Text and media area | <p>This is a text with the multilang syntax on the <span class="multilang" lang="en">English</span><span class="multilang" lang="fr">Anglais</span><span class="multilang" lang="es">Inglés</span> word that should be auto-linked.</p><p>This are plain text words that should also be auto-linked: English, Anglais, Inglés.</p> | C1     | label1   |                                                 | 0               |
    And the "glossary" filter is "on"
    And the following "language pack" exists:
      | language | fr | es |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"

  Scenario: Autolinked glossary entries are correctly linked in front page context
    Given I log in as "admin"
    When I am on site homepage
    And I turn editing mode on
    And I follow "Edit"
    And I set the following fields to these values:
      | Description | This is a Linked entry. This is a Normal entry |
    And I press "Save changes"
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

  Scenario: Autolinked glossary entries are correctly linked in course category
    Given I log in as "admin"
    And I navigate to "Courses > Manage courses and categories" in site administration
    And I click on "edit" action for "Category 1" in management category listing
    And I set the following fields to these values:
      | Description | This is a Linked entry. This is a Normal entry |
    And I press "Save changes"
    When I click on "view" action for "Category 1" in management category listing
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

  Scenario: Autolinked glossary entries are correctly linked in course summary
    # Course summary is automatically generated so editing it is necessary.
    Given I am on the "Course 1" "course editing" page logged in as "teacher1"
    And I set the following fields to these values:
      | Course summary | This is a Linked entry. This is the Normal entry |
    And I press "Save and display"
    When I am on site homepage
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

  Scenario Outline: Autolinked glossary entries are correctly linked in text and media areas and url
    Given the following "activities" exist:
      | activity       | name           | intro                                          | course | showdescription |
      | <activitytype> | <activityname> | This is a Linked entry. This is a Normal entry | C1     | 1               |
    When I am on the "Course 1" course page logged in as teacher1
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

    Examples:
      | activityname        | activitytype |
      | Text and media area | label        |
      | Url 1               | url          |

  @javascript
  Scenario: Autolinked glossary entries are correctly linked in text blocks
    Given I am on the "Course 1" course page logged in as teacher1
    And I turn editing mode on
    When I add the "Text" block to the default region with:
      | Text block title | Text block                                       |
      | Content          | This is a Linked entry. This is the Normal entry |
    # Confirm that the text "linked entry" is automatically linked when referenced.
    And "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

  Scenario Outline: Autolinked glossary entries are correctly linked in activities and resources
    Given the following "activities" exist:
      | activity       | name           | intro                                          | course | content       |
      | <activitytype> | <activityname> | This is a Linked entry. This is a Normal entry | C1     | <pagecontent> |
    When I am on the "<activityname>" "<activitytype> activity" page logged in as teacher1
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

    Examples:
      | activityname | activitytype | pagecontent                                    |
      | Page 1       | page         | This is a Linked entry. This is a Normal entry |
      | Assign 1     | assign       |                                                |
      | Folder 1     | folder       |                                                |
      | Quiz 1       | quiz         |                                                |
      | Workshop 1   | workshop     |                                                |
      | Choice 1     | choice       |                                                |

  Scenario: Autolinked glossary entries are correctly linked in database
    Given the following "activities" exist:
      | activity | name       | intro                                          | course | idnumber |
      | data     | Database 1 | This is a Linked entry. This is a Normal entry | C1     | data1    |
    And the following "mod_data > fields" exist:
      | database | type | name            | description            |
      | data1    | text | Test field name | Test field description |
    And the following "mod_data > entries" exist:
      | database | user     | Test field name |
      | data1    | student1 | Student entry   |
    When I am on the "Database 1" "data activity" page logged in as teacher1
    # Confirm that the text "linked entry" is automatically linked when referenced in list view.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced in list view.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist
    And I select "Single view" from the "jump" singleselect
    # Confirm that the text "linked entry" is automatically linked when referenced in single view.
    And "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced in single view.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

  Scenario: Autolinked glossary entries are correctly linked in wiki page
    Given the following "activities" exist:
      | activity | course | name   | wikimode      |
      | wiki     | C1     | Wiki 1 | collaborative |
    When I am on the "Wiki 1" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | This is a Linked entry. This is a Normal entry. |
    And I press "Save"
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

  Scenario: Autolinked glossary entries are correctly linked in forum posts
    Given the following "activities" exist:
      | activity | course | name    | idnumber |
      | forum    | C1     | Forum 1 | forum1   |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name         | message                                         |
      | teacher1 | forum1 | Discussion 1 | This is a Linked entry. This is a Normal entry. |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to post "Discussion 1" in "Forum 1" forum
    # Confirm that the text "linked entry" is automatically linked when referenced.
    Then "Linked entry" "link" should exist
    # Confirm that the text "normal entry" is not linked when referenced.
    And "Normal entry" "link" should not exist
    And "Normal entry" "text" should exist

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
