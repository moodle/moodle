@mod @mod_wiki
Feature: Display the course linear navigation in the wiki pages
  In order to quickly access the next and previous activities in a course
  As a user
  I want to see the course linear navigation in wiki pages

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | teacher  | Teacher   | 1        |
      | student  | Student   | 1        |
    And the following "courses" exist:
      | fullname | shortname | format | enablelinearnav |
      | Course 1 | C1        | topics | 1               |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | student | C1     | student        |
      | teacher | C1     | editingteacher |
    And the following "activities" exist:
      | activity | course | name  | idnumber | wikimode      | firstpagetitle |
      | wiki     | C1     | Wiki1 | wiki1    | collaborative | My wiki1 page  |
    And the following wiki pages exist:
      | wiki  | title          | content                 |
      | wiki1 | My wiki1 page  | 11111111 with [[Link1]] |

  @javascript
  Scenario: As a student I should see the course linear navigation in wiki pages that allow it
    Given I am on the "wiki1" "wiki activity" page logged in as "student"
    Then the course linear navigation should be visible
    # Print.
    And I click on "Print" "link"
    And I switch to a second window
    And the course linear navigation should not be visible
    And I close all opened windows
    # Edit.
    And I select "Edit" from the "jump" singleselect
    And the course linear navigation should not be visible
    And I press "Preview"
    And the course linear navigation should not be visible
    And I press "Cancel"
    # Search.
    And I set the field "searchstring" to "page"
    And I press "Search wikis"
    And the course linear navigation should be visible
    # Create.
    And I follow "Link1"
    And the course linear navigation should not be visible
    And I press "Create page"
    And the course linear navigation should not be visible
    And I set the field "HTML format" to "This is the new page"
    And I press "Save"
    And the course linear navigation should be visible
    # Comments.
    And I select "Comments" from the "jump" singleselect
    And the course linear navigation should be visible
    And I follow "Add comment"
    And the course linear navigation should not be visible
    And I set the following fields to these values:
      | Comment | student comment |
    And I press "Save"
    And the course linear navigation should be visible
    And I click on "Delete" "link" in the "wiki-comments" "table"
    And the course linear navigation should not be visible
    And I press "Cancel"
    # History.
    And I select "History" from the "jump" singleselect
    And the course linear navigation should be visible
    And I click on "1" "link" in the "Student 1" "table_row"
    And the course linear navigation should be visible
    And I follow "Restore this version"
    And the course linear navigation should not be visible
    And I press "No"
    And I click on "Back" "link"
    # Map.
    And I select "Map" from the "jump" singleselect
    And the course linear navigation should be visible
    # Files.
    And I select "Files" from the "jump" singleselect
    And the course linear navigation should be visible

  @javascript
  Scenario: As a teacher I should see the course linear navigation in wiki pages that allow it
    # Most pages are tested in the student scenario, so we will test only pages exclusive to teachers.
    Given I am on the "wiki1" "wiki activity" page logged in as "teacher"
    Then the course linear navigation should be visible
    # Files.
    And I select "Files" from the "jump" singleselect
    And the course linear navigation should be visible
    And I press "Edit wiki files"
    And the course linear navigation should not be visible
    And I press "Cancel"
    # Administration.
    And I select "Administration" from the "jump" singleselect
    And the course linear navigation should be visible
