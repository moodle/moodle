@core @core_files @test
Feature: Add a new custom file type
  In order to add files of a custom type
  As an admin
  I need to add a new custom file type


  @javascript
  Scenario: Add custom file type
    Given the following "courses" exist:
      | fullname | shortname | category | legacyfiles |
      | Course 1 | C1 | 0 | 2 |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "admin"
    And I navigate to "File types" node in "Site administration>Server"
    And I press "Add a new file type"
    And I set the following fields to these values:
      | Extension | mobi |
      | MIME type | application/x-mobipocket-ebook |
      | File icon | document                       |
      | Description type | Custom description specified in this form |
      | Custom description | Kindle ebook |
    And I press "Save changes"
    And I should see "mobi"
    And I log out
    And I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    When I add a "File" to section "1" and I fill the form with:
      | Name | Test file |
      | Select files | files/tests/fixtures/custom_filetype.mobi |
      | Show type    | 1                             |
      | Display resource description | 1             |
    And I follow "Course 1"
    Then I should see "Test file"
    And I should see "Kindle ebook" in the "span.resourcelinkdetails" "css_element"