@editor @editor_atto @atto
Feature: Add text direction and alignment
  In order to generate a content that can be displayed in the proper direction to everyone
  As a user
  I should see the Atto editor with explicit direction and alignment being set

  Background:
    Given the following "user preferences" exist:
      | user  | preference  | value |
      | admin | htmleditor  | atto  |
    And I log in as "admin"
    And I navigate to "Plugins > Text editors > Atto HTML editor > Atto toolbar settings" in site administration
    And I set the field "Toolbar config" to multiline:
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
    And I log out

  @javascript
  Scenario Outline: Atto should apply user's direction and alignment by default
    Given the following "courses" exist:
      | fullname  | shortname | summary | summaryformat |
      | Course 1  | C1        |         | 1             |
    And the following "language customisations" exist:
      | component   | stringid    | value         |
      | <component> | <stringid>  | <localstring> |
    And I log in as "admin"
    And I am on "Course 1" course homepage
    When I navigate to "Settings" in current page administration
    And I press "Show more buttons"
    And I press "HTML"
    Then I should see "<partialtext>"

    Examples:
      | component       | stringid      | localstring | partialtext                               |
      | core_langconfig | thisdirection | ltr         | dir=\"ltr\" style=\"text-align: left;\"   |
      | core_langconfig | thisdirection | rtl         | dir=\"rtl\" style=\"text-align: right;\"  |
