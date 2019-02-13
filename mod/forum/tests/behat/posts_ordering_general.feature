@mod @mod_forum
Feature: New discussions and discussions with recently added replies are displayed first
  In order to use forum as a discussion tool
  As a user
  I need to see currently active discussions first

  Background:
    Given the following "users" exist:
      | username  | firstname | lastname  | email                 |
      | teacher1  | Teacher   | 1         | teacher1@example.com  |
      | student1  | Student   | 1         | student1@example.com  |
    And the following "courses" exist:
      | fullname  | shortname | category  |
      | Course 1  | C1        | 0         |
    And the following "course enrolments" exist:
      | user      | course    | role            |
      | teacher1  | C1        | editingteacher  |
      | student1  | C1        | student         |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "1" and I fill the form with:
      | Forum name  | Course general forum                |
      | Description | Single discussion forum description |
      | Forum type  | Standard forum for general use      |
    And I log out

  #
  # We need javascript/wait to prevent creation of the posts in the same second. The threads
  # would then ignore each other in the prev/next navigation as the Forum is unable to compute
  # the correct order.
  #
  @javascript
  Scenario: Replying to a forum post or editing it puts the discussion to the front
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Course general forum"
    #
    # Add three posts into the forum.
    #
    When I add a new discussion to "Course general forum" forum with:
      | Subject | Forum post 1            |
      | Message | This is the first post  |
    And I add a new discussion to "Course general forum" forum with:
      | Subject | Forum post 2            |
      | Message | This is the second post |
    And I add a new discussion to "Course general forum" forum with:
      | Subject | Forum post 3            |
      | Message | This is the third post  |
    #
    # Edit one of the forum posts.
    #
    And I follow "Forum post 2"
    And I click on "Edit" "link" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' forumpost ')][contains(., 'Forum post 2')]" "xpath_element"
    And I set the following fields to these values:
      | Subject | Edited forum post 2     |
    And I press "Save changes"
    And I wait to be redirected
    And I log out
    #
    # Reply to another forum post.
    #
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I follow "Course general forum"
    And I follow "Forum post 1"
    And I click on "Reply" "link" in the "//div[@aria-label='Forum post 1 by Student 1']" "xpath_element"
    And I set the following fields to these values:
      | Message | Reply to the first post |
    And I press "Post to forum"
    And I wait to be redirected
    And I am on "Course 1" course homepage
    And I follow "Course general forum"
    #
    # Make sure the order of the forum posts is as expected, with most recent new participation first (ie excluding edits).
    #
    Then I should see "Forum post 1" in the "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ')][position()=1]" "xpath_element"
    And I should see "Forum post 3" in the "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ')][position()=2]" "xpath_element"
    And I should see "Edited forum post 2" in the "//tr[contains(concat(' ', normalize-space(@class), ' '), ' discussion ')][position()=3]" "xpath_element"
    #
    # Make sure the next/prev navigation uses the same order of the posts.
    #
    And I follow "Forum post 3"
    And "//a[@aria-label='Next discussion: Forum post 1']" "xpath_element" should exist
    And "//a[@aria-label='Previous discussion: Edited forum post 2']" "xpath_element" should exist
