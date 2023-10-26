@mod @mod_resource @_file_upload
Feature: Teacher can specify different display options for the resource
  In order to provide more information about a file
  As a teacher
  I need to be able to show size, type and modified date

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
    And the following "activities" exist:
      | activity | course | name   | defaultfilename                            | uploaded |
      | resource | C1     | Myfile | mod/resource/tests/fixtures/samplefile.txt | 1        |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

  @javascript
  Scenario: Specifying no additional display options for a file resource
    When I am on the "Myfile" "resource activity editing" page
    And I set the following fields to these values:
      | Show upload/modified date | 0 |
      | showsize                  | 0 |
      | showtype                  | 0 |
    And I press "Save and display"
    Then ".resourcedetails" "css_element" should not exist
    And I am on "Course 1" course homepage
    And ".activity.resource .resourcelinkdetails" "css_element" should not exist

  @javascript
  Scenario Outline: Specifying different display options for a file resource
    When I am on the "Myfile" "resource activity editing" page
    And I set the following fields to these values:
      | display                   | 5          |
      | Show size                 | <showsize> |
      | Show type                 | <showtype> |
      | Show upload/modified date | <showdate> |
    And I press "Save and display"
    Then I <seesize> see "6 bytes" in the ".resourcedetails" "css_element"
    And I <seetype> see "Text file" in the ".resourcedetails" "css_element"
    And I <seedate> see "Uploaded" in the ".resourcedetails" "css_element"
    And I am on "Course 1" course homepage
    And I <seesize> see "6 bytes" in the ".activity.resource .resourcelinkdetails" "css_element"
    And I <seetype> see "Text file" in the ".activity.resource .resourcelinkdetails" "css_element"
    And I <seedate> see "Uploaded" in the ".activity.resource .resourcelinkdetails" "css_element"

    Examples:
      | showsize | showtype | showdate | seesize    | seetype    | seedate    |
      | 1        | 0        | 0        | should     | should not | should not |
      | 0        | 1        | 0        | should not | should     | should not |
      | 0        | 0        | 1        | should not | should not | should     |
      | 1        | 1        | 0        | should     | should     | should not |
      | 1        | 0        | 1        | should     | should not | should     |
      | 0        | 1        | 1        | should not | should     | should     |
      | 1        | 1        | 1        | should     | should     | should     |
