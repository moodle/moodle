@core @core_contentbank @core_h5p @contenttype_h5p @_file_upload @_switch_iframe @javascript
Feature: Replace H5P file from an existing content requires special capabilities
  In order replace an H5P content from the content bank
  As a teacher
  I need to be able to replace the content only if certain capabilities are allowed

  Background:
    Given I log in as "admin"
    And I navigate to "H5P > Manage H5P content types" in site administration
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And I upload "h5p/tests/fixtures/filltheblanks.h5p" file to "H5P content type" filemanager
    And I click on "Upload H5P content types" "button" in the "#fitem_id_uploadlibraries" "css_element"
    And the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "categories" exist:
      | name  | category | idnumber |
      | Cat 1 | 0        | CAT1     |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1        | CAT1     |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    And the following "contentbank content" exist:
      | contextlevel | reference | contenttype     | user     | contentname       | filepath                              |
      | Course       | C1        | contenttype_h5p | admin    | admincontent      | /h5p/tests/fixtures/ipsums.h5p        |
      | Course       | C1        | contenttype_h5p | teacher1 | teachercontent    | /h5p/tests/fixtures/filltheblanks.h5p |
    And I log out
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add the "Navigation" block if not present
    And I expand "Site pages" node
    And I click on "Content bank" "link"
    # Force the content deploy
    And I click on "admincontent" "link"
    And I click on "Content bank" "link"

  Scenario: Teacher can replace its own H5P files
    Given I click on "teachercontent" "link"
    When I open the action menu in "region-main-settings-menu" "region"
    And I choose "Replace with file" in the open action menu
    And I upload "h5p/tests/fixtures/ipsums.h5p" file to "Upload content" filemanager
    And I click on "Save changes" "button"
    Then I switch to "h5p-player" class iframe
    And I switch to "h5p-iframe" class iframe
    And I should see "Lorum ipsum"
    And I switch to the main frame

  Scenario: Teacher cannot replace another user's H5P files
    When I click on "admincontent" "link"
    Then "Replace with file" "link" should not exist in the "region-main-settings-menu" "region"

  Scenario: Teacher cannot replace a content without having upload capability
    Given the following "permission overrides" exist:
      | capability                | permission | role           | contextlevel | reference |
      | moodle/contentbank:upload | Prevent    | editingteacher | Course       | C1        |
    When I click on "teachercontent" "link"
    Then "Replace with file" "link" should not exist in the "region-main-settings-menu" "region"

  Scenario: Teacher cannot replace a content without having the H5P upload capability
    Given the following "permission overrides" exist:
      | capability             | permission | role           | contextlevel | reference |
      | contenttype/h5p:upload | Prevent    | editingteacher | Course       | C1        |
    When I click on "teachercontent" "link"
    Then "Replace with file" "link" should not exist in the "region-main-settings-menu" "region"

  Scenario: Teacher cannot replace a content without having the manage own content capability
    Given the following "permission overrides" exist:
      | capability                          | permission | role           | contextlevel | reference |
      | moodle/contentbank:manageowncontent | Prevent    | editingteacher | Course       | C1        |
    When I click on "teachercontent" "link"
    Then "Replace with file" "link" should not exist in the "region-main-settings-menu" "region"
