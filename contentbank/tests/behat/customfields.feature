@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Content bank custom fields
  In order to add/edit custom fields for content
  As a user
  I need to be able to access the custom fields

  Background:
    Given the following "blocks" exist:
      | blockname     | contextlevel | reference | pagetypepattern | defaultregion |
      | private_files | System       | 1         | my-index        | side-post     |
    And the following "custom field categories" exist:
      | name              | component        | area    | itemid |
      | Category for test | core_contentbank | content | 0      |
    And the following "custom fields" exist:
      | name       | category          | type | shortname |
      | Test field | Category for test | text | testfield |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname       | filepath                              |
      | System       |           | contenttype_h5p | admin | filltheblanks.h5p | /h5p/tests/fixtures/filltheblanks.h5p |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"

  Scenario: Users can edit customfields
    Given I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I follow "filltheblanks.h5p"
    And I click on "Edit" "link"
    And I set the following fields to these values:
      | Test field | My test value |
    When I click on "Save" "button"
    Then I should see "Test field: My test value"
