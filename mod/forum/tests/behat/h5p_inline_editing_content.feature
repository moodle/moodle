@mod @mod_forum @editor @editor_atto @atto @atto_h5p @filter @filter_displayh5p @core_h5p @_file_upload @_switch_iframe
Feature: Inline editing H5P content in mod_forum
  In order to edit an existing H5P content
  As a user
  I need to see the button and access to the H5P editor

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | teacher2 | Teacher   | 2        | teacher2@example.com |
      | student1  | Student  | 1        | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role           |
      | teacher1 | C1 | editingteacher |
      | teacher2 | C1 | teacher |
      | student1 | C1 | student        |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname        | filepath                                  |
      | Course       | C1        | contenttype_h5p | admin    | Greeting card      | /h5p/tests/fixtures/greeting-card.h5p     |
    And the following "activities" exist:
      | activity | name       | introformat | course | content  | contentformat | idnumber |
      | forum    | ForumName1 | 1           | C1     | H5Ptest  | 1             | 1        |
    And the "displayh5p" filter is "on"
    # Override this capability to let teachers and students to Turn editing on.
    And the following "permission overrides" exist:
      | capability                 | permission | role           | contextlevel | reference |
      | moodle/course:update       | Allow      | teacher        | System       |           |
      | moodle/course:update       | Allow      | student        | System       |           |

  @javascript @repository_contentbank
  Scenario: Edit H5P content from a forum intro using copy to content bank file
    Given I am on the "ForumName1" "forum activity editing" page logged in as admin
    # Add H5P content to the forum description.
    And I click on "Insert H5P" "button" in the "#fitem_id_introeditor" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Make a copy of the file" "radio"
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I click on "Save and display" "button"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # The Edit button is only displayed when editing mode is on.
    And I should not see "Edit H5P content"
    When I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    Then I should see "Edit H5P content"
    And I log out
    # Check teacher1 can see the Edit button too.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I should see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can't see the Edit button, because she can't edit the forum activity.
    And I log in as "teacher2"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I should not see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I should not see "Edit H5P content"

  @javascript @repository_contentbank
  Scenario: Edit H5P content from a forum post
    Given I am on the "ForumName1" "forum activity" page logged in as admin
    # Add H5P content to a forum post as admin.
    And I click on "Add discussion topic" "link"
    And I set the following fields to these values:
      | Subject | Forum post by admin |
    And I click on "Insert H5P" "button" in the "#fitem_id_message" "css_element"
    And I click on "Browse repositories..." "button" in the "Insert H5P" "dialogue"
    And I select "Content bank" repository in file picker
    And I click on "Greeting card" "file" in repository content area
    And I click on "Select this file" "button"
    And I click on "Insert H5P" "button" in the "Insert H5P" "dialogue"
    And I press "Post to forum"
    And I follow "Forum post by admin"
    And I switch to "h5p-iframe" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Hello world!"
    And I switch to the main frame
    # The Edit button is only displayed when editing mode is on.
    And I should not see "Edit H5P content"
    When I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I follow "Forum post by admin"
    Then I should see "Edit H5P content"
    And I log out
    # Check teacher1 can see the Edit button because she can edit the post too.
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I follow "Forum post by admin"
    And I should see "Edit H5P content"
    And I log out
    # Check teacher2 (non-editing teacher) can see the Edit button because she can edit the post too.
    And I log in as "teacher2"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I follow "Forum post by admin"
    And I should see "Edit H5P content"
    And I log out
    # Check student1 can't see the Edit button.
    And I log in as "student1"
    And I am on "Course 1" course homepage with editing mode on
    And I am on the "ForumName1" "forum activity" page
    And I follow "Forum post by admin"
    And I should not see "Edit H5P content"
