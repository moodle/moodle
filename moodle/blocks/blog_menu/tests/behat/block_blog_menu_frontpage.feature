@block @block_blog_menu
Feature: Enable Block blog menu on the frontpage
  In order to enable the blog menu on the frontpage
  As an admin
  I can add blog menu block to the frontpage

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email | idnumber |
      | student1 | Student | 1 | student1@example.com | S1 |
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Turn editing on" node in "Front page settings"
    And I add the "Blog menu" block
    And I log out

  Scenario: Students use the blog menu block to post blogs
    Given I log in as "student1"
    And I am on site homepage
    And I follow "Add a new entry"
    When I set the following fields to these values:
      | Entry title | S1 First Blog |
      | Blog entry body | This is my awesome blog! |
    And I press "Save changes"
    Then I should see "S1 First Blog"
    And I should see "This is my awesome blog!"
    And I am on site homepage
    And I follow "Blog entries"
    And I should see "S1 First Blog"
    And I should see "This is my awesome blog!"
