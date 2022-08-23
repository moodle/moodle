@mod @mod_forum @_file_upload
Feature: Add forum activities and discussions
  In order to discuss topics with other users
  As a teacher
  I need to add forum activities to moodle courses

  @javascript
  Scenario: Add a forum and a discussion attaching files
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activity" exists:
      | activity    | forum                  |
      | course      | C1                     |
      | name        | Test forum name        |
      | type        | general                |
    And the following forum discussions exist in course "Course 1":
      | user     | forum              | name                 | message          | attachments |
      | teacher1 | Test forum name    | Forum post 1         | this is the body |             |
      | student1 | Test forum name    | Post with attachment | this is the body | empty.txt   |
    And I log in as "student1"
    And I reply "Forum post 1" post from "Test forum name" forum with:
      | Subject | Reply with attachment |
      | Message | This is the body |
      | Attachment | lib/tests/fixtures/upload_users.csv |
    Then I should see "Reply with attachment"
    And I should see "upload_users.csv"
    And I am on the "Test forum name" "forum activity" page
    And I follow "Post with attachment"
    And I should see "empty.txt"
    And I follow "Edit"
    And the field "Attachment" matches value "empty.txt"

  @javascript
  Scenario: Test forum settings validation
    Given the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | 0        |
    And I log in as "admin"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Forum" to section "0" and I fill the form with:
      | Forum name | Test forum |
      | Forum type | single     |
      | Group mode | 1          |
    When I press "Save and display"
    Then I should see "Separate groups can't be used with a single simple discussion."
    And I should see "A single simple discussion can't be used with separate groups."
