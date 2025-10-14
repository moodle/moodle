@mod @mod_forum @core_rss @javascript
Feature: Verify that default RSS settings for forums are correctly applied.
  In order to ensure consistent RSS configuration across new forums
  As an admin
  I need to verify that the default RSS values set at site level appear when creating a new forum.

  Background:
    Given the following "courses" exist:
      | fullname        | shortname |
      | RSS Test Course | RSS101    |

  @javascript
  Scenario: Admin sets default forum RSS values and verifies them on new forum creation
    Given I log in as "admin"
    # Enable site-level RSS.
    And I navigate to "Advanced features" in site administration
    And I set the field "Enable RSS feeds" to "1"
    And I press "Save changes"
    # Configure forum module defaults.
    And I navigate to "Plugins > Activity modules > Forum" in site administration
    And I set the field "Enable RSS feeds" to "1"
    And I set the field "RSS feed type" to "Discussions"
    And I set the field "Number of RSS recent articles" to "5"
    And I press "Save changes"
    # Create a new forum and verify defaults appear in the add-activity form.
    When I am on the "RSS101" course page
    And I turn editing mode on
    And I add a forum activity to course "RSS101" section "New section"
    And I expand all fieldsets
    Then the "RSS feed for this activity" select box should contain "Discussions"
    And the "Number of RSS recent articles" select box should contain "5"
