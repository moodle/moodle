@editor @editor_tiny @tiny @tiny_wordimport
Feature: Tiny editor admin settings for wordimport plugin
  To be able to actually import word documents in the editor, the capability must be given.

  Background:
    Given the following "courses" exist:
      | shortname | fullname |
      | C1        | Course 1 |
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name      | intro     | introformat | course | content | contentformat | idnumber |
      | page     | PageName1 | PageDesc1 | 1           | C1     | Test    | 1             | 1        |

  @javascript
  Scenario: When a user does not have the wordimport capability, they cannot import a word file in TinyMCE
    Given the following "permission overrides" exist:
      | capability          | permission | role           | contextlevel | reference |
      | tiny/wordimport:add | Prohibit   | editingteacher | Course       | C1        |
    When I am on the "PageName1" "page activity editing" page logged in as "teacher1"
    Then "Import Word File" "button" should not exist

  @javascript
  Scenario: When a user does have the wordimport capability, they can import a word file in TinyMCE
    Given I am on the "PageName1" "page activity editing" page logged in as "teacher1"
    Then "Import Word File" "button" should exist

  @javascript @_file_upload
  Scenario: A teacher imports a word file in TinyMCE within a page activity.
    Given I am on the "PageName1" "page activity editing" page logged in as "teacher1"
    When I click on the "Import Word File" button for the "Page content" TinyMCE editor
    And I upload "/lib/editor/tiny/plugins/wordimport/tests/behat/fixtures/sample.docx" to the file picker
    And I click on the "View > Source code" menu item for the "Page content" TinyMCE editor
    # The Heading
    Then I should find this multiline source code within the "Page content" TinyMCE editor:
      """
      <h3><span style="color: #c00000;">Sample Document</span></h3>
      """
    # The first paragraph
    And I should find this multiline source code within the "Page content" TinyMCE editor:
      """
        <p>This document was created using accessibility techniques for headings,
          lists, image alternate text, tables, and columns. It should be completely
          accessible using assistive technologies such as screen readers.</p>
      """
