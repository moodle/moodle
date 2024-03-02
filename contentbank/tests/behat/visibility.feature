@core @core_contentbank @core_h5p @contentbank_h5p @_file_upload @javascript
Feature: Make content public or unlisted
  In order to make content public or unlisted
  As a user
  I need to be able to access the edition options

  Background:
    Given I log in as "admin"
    And I am on site homepage
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    And I add the "Navigation" block if not present
    And I configure the "Navigation" block
    And I set the following fields to these values:
      | Page contexts | Display throughout the entire site |
    And I press "Save changes"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"

  Scenario: Users can make their content public or unlisted
    Given the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user  | contentname             | filepath                                    | visibility |
      | System       |           | contenttype_h5p | admin | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       | 1          |
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "filltheblanks.h5p" "link"
    And I wait until the page is ready
    And "filltheblanks.h5p (Unlisted)" "heading" should not exist
    And I click on "More" "button"
    And I should see "Make unlisted"
    And I click on "Make unlisted" "link"
    And I wait until the page is ready
    Then "filltheblanks.h5p (Unlisted)" "heading" should exist
    And I click on "More" "button"
    And I should see "Make public"

  Scenario: Unlisted content cannot be seen by other users
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | teacher2  | Teacher    | 2         | teacher2@example.com  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | teacher2  | C1      | editingteacher  |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname             | filepath                                    | visibility |
      | Course       | C1        | contenttype_h5p | teacher1 | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       | 2          |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    Then I should see "filltheblanks.h5p (Unlisted)"
    And I log out
    And I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    Then I should not see "filltheblanks.h5p"

  Scenario: Unlisted content is not found through search by other users
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | teacher2  | Teacher    | 2         | teacher2@example.com  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | teacher2  | C1      | editingteacher  |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname             | filepath                                    | visibility |
      | Course       | C1        | contenttype_h5p | teacher1 | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       | 2          |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I set the field "Search" to "filltheblanks.h5p"
    And I should see "filltheblanks.h5p"
    And I log out
    And I log in as "teacher2"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    When I set the field "Search" to "filltheblanks.h5p"
    Then I should not see "filltheblanks.h5p"

  Scenario: Managers can see other users' unlisted content
    Given the following "users" exist:
      | username  | firstname  | lastname  | email                 |
      | teacher1  | Teacher    | 1         | teacher1@example.com  |
      | manager1  | Manager    | 1         | manager1@example.com  |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
      | manager1  | C1      | manager         |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname             | filepath                                    | visibility |
      | Course       | C1        | contenttype_h5p | teacher1 | filltheblanks.h5p       | /h5p/tests/fixtures/filltheblanks.h5p       | 2          |
    And I log out
    And I log in as "manager1"
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I should see "filltheblanks.h5p (Unlisted)"
    And I set the field "Search" to "filltheblanks.h5p"
    And I should see "filltheblanks.h5p (Unlisted)"

  @_file_upload
  Scenario: Default content visibility can be set to unlisted
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I set the following administration settings values:
      | Default content visibility | 2 |
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Upload" "link"
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Upload content" filemanager
    And I click on "Save changes" "button"
    Then "filltheblanks.h5p (Unlisted)" "heading" should exist

  @_file_upload
  Scenario: User preference concerning content visibility overrides site-wide default content visibility
    Given the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And I set the following administration settings values:
      | Default content visibility | 2 |
    And the following "user preferences" exist:
      | user  | preference  | value |
      | admin | core_contentbank_visibility  | 1  |
    And I am on "Course 1" course homepage
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Content bank" "link" in the "Navigation" "block"
    And I click on "Upload" "link"
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "Upload content" filemanager
    And I click on "Save changes" "button"
    Then "filltheblanks.h5p" "heading" should exist
    And "filltheblanks.h5p (Unlisted)" "heading" should not exist
