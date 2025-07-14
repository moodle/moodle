@block @block_tag_flickr
Feature: Adding and configuring Flickr block
  In order to have the Flickr block used
  As a admin
  I need to add the Flickr block to the tags site page

  Background:
    # We need to create a user to use tag here because the tags site page only shows the tag that currently being used.
    Given the following "users" exist:
      | username | interests |
      | student1 | Cats      |
    And I log in as "admin"
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And the following "blocks" exist:
      | blockname  | contextlevel | reference | pagetypepattern | defaultregion |
      | tag_flickr | System       | 1         | tag-search      | side-post     |
      | tag_flickr | System       | 1         | tag-index       | side-post     |
    # TODO MDL-57120 site "Tags" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Tags" "link" in the "Navigation" "block"

  @javascript
  Scenario: Adding Flickr block to the tags site page
    Given "block_tag_flickr" "block" should exist
    When I configure the "Flickr" block
    Then I should see "Flickr block title"
    And I set the field "Flickr block title" to "The Flickr block header"
    And I press "Save changes"
    And "block_tag_flickr" "block" should exist
    And "The Flickr block header" "block" should exist

  @javascript
  Scenario: Adding Flickr block to a specific tag page
    Given I click on "Cats" "link"
    Then "block_tag_flickr" "block" should exist
    And ".flickr-photos" "css_element" should exist in the "block_tag_flickr" "block"
