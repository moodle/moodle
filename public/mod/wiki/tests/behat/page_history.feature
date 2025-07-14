@mod @mod_wiki
Feature: A history of each wiki page is available
  In order to know how a wiki page evolved over the time and how changed what
  As a user
  I need to check the history of a wiki page

  @javascript
  Scenario: Wiki page edition history changes list
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activity" exists:
      | course         | C1             |
      | activity       | wiki           |
      | name           | Test wiki name |
      | firstpagetitle | First page     |
      | wikimode       | collaborative  |
    And I am on the "Test wiki name" "wiki activity" page logged in as teacher1
    And I press "Create page"
    And I set the following fields to these values:
      | HTML format | First edition |
    And I press "Save"
    When I am on the "Test wiki name" "wiki activity" page logged in as student1
    And I select "Edit" from the "jump" singleselect
    And I set the following fields to these values:
      | HTML format | Second edition |
    And I press "Save"
    When I am on the "Test wiki name" "wiki activity" page logged in as student2
    And I select "Edit" from the "jump" singleselect
    And I set the following fields to these values:
      | HTML format | Third edition |
    And I press "Save"
    And I select "History" from the "jump" singleselect
    # Checking that there are 3 history items (the first one is are th)
    And "//*[@id='region-main']/descendant::table/descendant::tr[4]" "xpath_element" should exist
    And I click on "1" "link" in the "Teacher 1" "table_row"
    And I should see "First edition"
    And I should see "Teacher 1"
    And I follow "Back"
    And I click on "2" "link" in the "Student 1" "table_row"
    And I should see "Second edition"
    And I should see "Student 1"
    And I follow "Back"
    And I click on "3" "link" in the "Student 2" "table_row"
    And I should see "Third edition"
    And I should see "Student 2" in the "region-main" "region"
    And I follow "Back"
    And I click on "comparewith" "radio" in the "Student 1" "table_row"
    And I click on "compare" "radio" in the "Teacher 1" "table_row"
    And I press "Compare selected"
    And I should see "Comparing version 1 with version 3"
    And I follow "Next"
    And I should see "Comparing version 2 with version 3"
