@editor @editor_atto @atto @mod @mod_forum @javascript
Feature: Add text direction
  To support bidi text sent by email

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode | summary | summaryformat |
      | Course 1 | C1 | 0 | 1 |                               | 1             |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "Plugins > Atto HTML editor" in site administration
    And I set the field "id_s_editor_atto_toolbar" to multiline:
    """
    collapse = collapse
    style1 = title, bold, italic
    list = unorderedlist, orderedlist
    links = link
    files = image, media, recordrtc, managefiles, h5p
    style2 = underline, strike, subscript, superscript
    align = align,rtl
    indent = indent
    insert = equation, charmap, table, clear
    undo = undo
    accessibility = accessibilitychecker, accessibilityhelper
    other = html
    """
    And I press "Save changes"
    And I navigate to "Plugins > Manage editors" in site administration
    And I click on "Disable" "link" in the "Plain text area" "table_row"
    And I click on "Disable" "link" in the "TinyMCE HTML editor" "table_row"
    And I log out

  @javascript
  Scenario: Check default direction exist in atto
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I wait until the page is ready
    And I press "Show more buttons"
    And I press "HTML"
    Then I should see "dir=\"ltr\" style=\"text-align: left;\"" in the "//span[@role='presentation']" "xpath_element"

  @javascript
  Scenario: Test RTL support in atto
    Given I log in as "admin"
    And I navigate to "Language > Language packs" in site administration
    And I set the field "Available language packs" to "he"
    And I press "Install selected language pack(s)"
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I navigate to "Edit settings" in current page administration
    And I set the field "Course summary" to "<p dir=\"ltr\" style=\"text-align: left;\">My ltr text</p>"
    And I click on "Save and display" "button"
    And I follow "Preferences" in the user menu
    And I follow "Preferred language"
    And I set the field "Preferred language" to "he"
    And I press "Save changes"
    And I wait until the page is ready
    And I am on "Course 1" course homepage
    And I navigate to "הגדרות" in current page administration
    And I wait until the page is ready
    And I set the field "תקציר הקורס" to ""
    And I click on "שמירת השינויים והצגתם" "button"
    And I navigate to "הגדרות" in current page administration
    And I wait until the page is ready
    And I press "הצגת כפתורים נוספים"
    And I press "HTML"
    Then I should see "dir=\"rtl\" style=\"text-align: right;\"" in the "//span[@role='presentation']" "xpath_element"