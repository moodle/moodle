@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Delete H5P file from the content bank
  In order remove H5P content from the content bank
  As an admin
  I need to be able to delete any H5P content from the content bank

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And I log in as "admin"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "filltheblanks.h5p" "link"
    And I set the field "Save as" to "content2delete.h5p"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"

  Scenario: Admins can delete content from the content bank
    Given I click on "More" "button"
    And I should see "Delete"
    And I click on "Delete" "link" in the ".cb-toolbar-container" "css_element"
    And I should see "Are you sure you want to delete the content 'content2delete.h5p'"
    And I should not see "The content will only be deleted from the content bank"
    And I click on "Cancel" "button" in the "Delete content" "dialogue"
    Then I should see "content2delete.h5p"
    And  I click on "More" "button"
    And I click on "Delete" "link" in the ".cb-toolbar-container" "css_element"
    And I click on "Delete" "button" in the "Delete content" "dialogue"
    And I wait until the page is ready
    And I should see "The content has been deleted."
    And I should not see "content2delete.h5p"

  Scenario: Users without the required capability can only delete their own content
    Given the following "permission overrides" exist:
      | capability                            | permission | role    | contextlevel | reference |
      | moodle/contentbank:deleteanycontent   | Prohibit   | manager | System       |           |
    And the following "users" exist:
      | username    | firstname | lastname | email              |
      | manager     | Max       | Manager  | man@example.com    |
    And the following "role assigns" exist:
      | user        | role      | contextlevel  | reference     |
      | manager     | manager       | System    |               |
    And I log out
    And I log in as "manager"
    And I follow "Manage private files..."
    And I upload "h5p/tests/fixtures/find-the-words.h5p" file to "Files" filemanager
    And I click on "Save changes" "button"
    When I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I should see "content2delete.h5p"
    And I follow "content2delete.h5p"
    And I click on "More" "button"
    Then I should not see "Delete"
    And I click on "Content bank" "link"
    And I click on "Upload" "link"
    And I click on "Choose a file..." "button"
    And I click on "Private files" "link" in the ".fp-repo-area" "css_element"
    And I click on "find-the-words.h5p" "link"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    And  I click on "More" "button"
    And I should see "Delete"

  Scenario: The number of times a content is used is displayed before removing it
    Given I follow "Dashboard"
    And I follow "Manage private files..."
    And I click on "Add..." "button"
    And I select "Content bank" repository in file picker
    And I click on "content2delete.h5p" "file" in repository content area
    And I click on "Link to the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Save changes" "button"
    When I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I follow "content2delete.h5p"
    And  I click on "More" "button"
    And I click on "Delete" "link" in the ".cb-toolbar-container" "css_element"
    Then I should see "Are you sure you want to delete the content 'content2delete.h5p'"
    And I should see "The content will only be deleted from the content bank"
    And I click on "Delete" "button" in the "Delete content" "dialogue"
    And I should see "The content has been deleted."
    And I should not see "content2delete.h5p"
