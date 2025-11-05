@mod @mod_board @javascript
Feature: Add and update media attachments in mod_board

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | First     | Student  | student1@example.com |
      | student2 | Second    | Student  | student2@example.com |
      | student3 | Third     | Student  | student3@example.com |
      | teacher1 | First     | Teacher  | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activity" exists:
      | activity       | board                  |
      | course         | C1                     |
      | name           | Sample board           |
      | groupmode      | 0                      |
      | singleusermode | 0                      |
    And the following config values are set as admin:
      | media_selection | 0 | mod_board |

  Scenario: Users may add URL media attachment in mod_board
    Given I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Media      | Link                       |
      | Post title | My post S1                 |
      | Link title | My fancy link              |
      | Link URL   | https://www.example.com/1/ |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "My fancy link" in the "My post S1" "mod_board > note"

    When I click on "Edit post My post S1" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Media      | Link                       |
      | Post title |                            |
      | Link title | My less fancy link         |
      | Link URL   | https://www.example.com/2/ |
    And I click on "Post" "button" in the "Edit post for column First Column" "dialogue"
    Then I should see "My less fancy link"

    When I click on "Edit post My less fancy link" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Post title | My post X1                 |
      | Media      | None                       |
    And I click on "Post" "button" in the "Edit post for column First Column" "dialogue"
    Then I should not see "My less fancy link"

  @_file_upload
  Scenario: Users may add Image media attachment in mod_board
    Given I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Media       | Image                      |
      | Post title  | My post S1                 |
      | Image title | My fancy image             |
    And I upload "pix/moodlelogo.png" file to "Image file" filemanager
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then "img[alt='My fancy image']" "css_element" should exist in the "My post S1" "mod_board > note"

  @_file_upload
  Scenario: Users may add File media attachment in mod_board
    Given the following config values are set as admin:
      | acceptedfiletypeforgeneral | txt | mod_board |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Media       | File                       |
      | Post title  | My post S1                 |
    And I upload "security.txt" file to "File" filemanager
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then I should see "security.txt" in the "My post S1" "mod_board > note"

  Scenario: Users may add YouTube media attachment in mod_board
    Given I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I am on the "Sample board" "board activity" page logged in as "student1"

    When I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"
    And I set the following fields to these values:
      | Media       | Video (YouTube)                         |
      | Post title  | My post S1                              |
      | Video title | My fancy video                          |
      | YouTube URL | https://youtube.com/watch?v=1234567890A |
    And I click on "Post" "button" in the "New post for column First Column" "dialogue"
    Then "iframe.mod_board_preview_element" "css_element" should exist in the "My post S1" "mod_board > note"

  @_file_upload
  Scenario: Users may use media selection buttons to add attachments in mod_board
    Given the following config values are set as admin:
      | media_selection            | 1   | mod_board |
      | acceptedfiletypeforgeneral | txt | mod_board |
    And I am on the "Sample board" "board activity" page logged in as "teacher1"
    And I change mod_board "1" column name to "First Column"
    And I change mod_board "2" column name to "Second Column"
    And I change mod_board "3" column name to "Third Column"

    And I am on the "Sample board" "board activity" page logged in as "student1"
    And I click on "Add new post to column First Column" "mod_board > button" in the "1" "mod_board > column"

    When I click on "Add Link for new post from column First Column" "mod_board > button" in the "New post for column First Column" "dialogue"
    Then I should see "Link title"
    And I should see "Link URL"

    When I click on "Add Image for new post from column First Column" "mod_board > button" in the "New post for column First Column" "dialogue"
    Then I should see "Image title"
    And I should see "Image file"

    When I click on "Add File for new post from column First Column" "mod_board > button" in the "New post for column First Column" "dialogue"
    Then I should see "File"

    When I click on "Add Video (YouTube) for new post from column First Column" "mod_board > button" in the "New post for column First Column" "dialogue"
    Then I should see "Video title"
    And I should see "YouTube URL"

    And I click on "Cancel" "button" in the "New post for column First Column" "dialogue"
