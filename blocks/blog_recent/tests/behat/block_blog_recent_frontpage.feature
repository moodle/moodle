@block @block_blog_recent
Feature: Feature: Students can use the recent blog entries block to view recent entries on the frontpage
  In order to enable the recent blog entries block on the frontpage
  As an admin
  I can add the recent blog entries block to the frontpage

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I add the "Recent blog entries" block
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 site "Blogs" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I log out

  Scenario: Students use the recent blog entries block to view blogs
    Given I log in as "student1"
    And I am on site homepage
    And I click on "Site blogs" "link" in the "Navigation" "block"
    And I follow "Add a new entry"
    When I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    Then I should see "S1 First Blog"
    And I should see "This is my awesome blog!"
    And I am on site homepage
    And I should see "S1 First Blog"
    And I follow "S1 First Blog"
    And I should see "This is my awesome blog!"

  Scenario: Students only see a few entries in the recent blog entries block
    Given I log in as "student1"
    And I am on site homepage
    And I click on "Site blogs" "link" in the "Navigation" "block"
    And I follow "Add a new entry"
    # Blog 1 of 5
    And I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I follow "Add a new entry"
    # Blog 2 of 5
    And I set the following fields to these values:
      | Entry title | S1 Second Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I should see "S1 Second Blog"
    And I should see "This is my awesome blog!"
    And I follow "Add a new entry"
    # Blog 3 of 5
    And I set the following fields to these values:
      | Entry title | S1 Third Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I should see "S1 Third Blog"
    And I should see "This is my awesome blog!"
    And I follow "Add a new entry"
    # Blog 4 of 5
    And I set the following fields to these values:
      | Entry title | S1 Fourth Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I wait "1" seconds
    And I should see "S1 Fourth Blog"
    And I should see "This is my awesome blog!"
    And I follow "Add a new entry"
    # Blog 5 of 5
    And I set the following fields to these values:
      | Entry title | S1 Fifth Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    And I should see "S1 Fifth Blog"
    And I should see "This is my awesome blog!"
    When I am on site homepage
    And I should not see "S1 First Blog"
    And I should see "S1 Second Blog"
    And I should see "S1 Third Blog"
    And I should see "S1 Fourth Blog"
    And I should see "S1 Fifth Blog"
    And I follow "S1 Fifth Blog"
    And I should see "This is my awesome blog!"
    Then I log out
    And I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And I configure the "Recent blog entries" block
    And I set the following fields to these values:
      | config_numberofrecentblogentries | 2 |
    And I press "Save changes"
    And I should see "S1 Fourth Blog"
    And I should see "S1 Fifth Blog"
