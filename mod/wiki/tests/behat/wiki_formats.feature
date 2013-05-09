@mod @mod_wiki
Feature: There is a choice of formats for editing a wiki page
  In order to allow users to use their favorite wiki format
  As a user
  I need to choose which wiki format do I want to use

  Background:
    Given the following "users" exists:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@asd.com |
    And the following "courses" exists:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exists:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add a "Wiki" to section "1" and I fill the form with:
      | Wiki name | Test wiki name |
      | Description | Test wiki description |
      | First page name | First page |
    And I follow "Test wiki name"

  @javascript
  Scenario: Creole format
    When I fill the moodle form with:
      | Creole format | 1 |
    And I press "Create page"
    Then "div.wikieditor-toolbar" "css_element" should exists
    # Click on bold, italic, interal link and H1
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[1]" "xpath_element"
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[2]" "xpath_element"
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[4]" "xpath_element"
    And the "newcontent" field should match "**Bold text**//Italic text//[[Internal link]]" value
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[8]" "xpath_element"
    And I press "Save"
    And I should see "Bold textItalic textInternal link"
    And I should see "Level 1 Header"
    And I should see "Table of contents"
    And I click on "Level 1 Header" "link" in the ".wiki-toc" "css_element"
    And I follow "Internal link"
    And I should see "New page title"

  @javascript
  Scenario: NWiki format
    When I fill the moodle form with:
      | NWiki format | 1 |
    And I press "Create page"
    Then "div.wikieditor-toolbar" "css_element" should exists
    # Click on italic, interal link and H1
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[2]" "xpath_element"
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[4]" "xpath_element"
    And the "newcontent" field should match "'''Italic text'''[[Internal link]]" value
    And I click on "//div[@class='wikieditor-toolbar']/descendant::a[8]" "xpath_element"
    And I press "Save"
    And I should see "Italic textInternal link"
    And I should see "Level 1 Header"
    And I should see "Table of contents"
    And I click on "Level 1 Header" "link" in the ".wiki-toc" "css_element"
    And I follow "Internal link"
    And I should see "New page title"

  @javascript
  Scenario: HTML format
    When I fill the moodle form with:
      | HTML format | 1 |
    And I press "Create page"
    Then "#id_newcontent_editor_tbl" "css_element" should exists
    And ".mce_bold" "css_element" should exists
    And I fill the moodle form with:
      | HTML format | I'm a text |
    And I press "Save"
    And I should see "I'm a text"
