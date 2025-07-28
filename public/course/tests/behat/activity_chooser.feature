@core @core_course @javascript
Feature: Display and choose from the available activities in course
  In order to add activities to a course
  As a teacher
  I should be enabled to choose from a list of available activities and also being able to read their summaries.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher | Teacher | 1 | teacher@example.com |
    And the following "courses" exist:
      | fullname | shortname | format | startdate |
      | Course   | C         | topics |           |
      | Course 2 | C2        | weeks  | 95713920  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C      | editingteacher |
      | teacher | C2     | editingteacher |
    And the following config values are set as admin:
      | enablemoodlenet | 0 | tool_moodlenet |
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on

  Scenario: The available activities are displayed to the teacher in the activity chooser
    Given I open the activity chooser
    Then I should see "Add an activity or resource" in the ".modal-title" "css_element"
    And I should see "Assignment" in the ".modal-body" "css_element"

  Scenario: The teacher can choose to add an activity from the activity items in the activity chooser
    Given I open the activity chooser
    When I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I click on "Add selected activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "New Assignment"
    And I set the following fields to these values:
      | Assignment name | Test Assignment |
    And I press "Save and return to course"
    Then I should see "Test Assignment" in the "General" "section"

  Scenario: The teacher can choose to add an activity from the activity summary in the activity chooser
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Add selected activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "New Assignment"

  Scenario: The teacher can see the activity summary in the activity chooser
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "Assignment" in the "help" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."
    And I should see "Activity details" in the "help" "core_course > Activity chooser screen"
    And I should see "Assessment" in the "help" "core_course > Activity chooser screen"
    # Confirm show summary also works for weekly format course
    And I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    And I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    And I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Assignment" in the "help" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."
    And I should see "Activity details" in the "help" "core_course > Activity chooser screen"
    And I should see "Assessment" in the "help" "core_course > Activity chooser screen"

  Scenario: The teacher can hide the activity summary in the activity chooser
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "modules" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "help" "core_course > Activity chooser screen"
    And I click on "Back" "button" in the "Add an activity or resource" "dialogue"
    Then "modules" "core_course > Activity chooser screen" should be visible
    And "help" "core_course > Activity chooser screen" should not be visible
    And "Back" "button" in the "Add an activity or resource" "dialogue" should not be visible
    And I should not see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "Add an activity or resource" "dialogue"
    And I should not see "Activity details" in the "help" "core_course > Activity chooser screen"
    # Confirm hide summary also works for weekly format course
    And I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    And I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    And I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Back" "button" in the "Add an activity or resource" "dialogue"
    And "modules" "core_course > Activity chooser screen" should be visible
    And "help" "core_course > Activity chooser screen" should not be visible
    And "Back" "button" should not exist in the "modules" "core_course > Activity chooser screen"
    And I should not see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "Add an activity or resource" "dialogue"
    And I should not see "Activity details" in the "Add an activity or resource" "dialogue"

  Scenario: View recommended activities
    When I log out
    And I log in as "admin"
    And I am on site homepage
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    And I click on ".activity-recommend-checkbox" "css_element" in the "Book" "table_row"
    # Setup done, lets check it works with a teacher.
    And I log out
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on
    And I open the activity chooser
    Then I should see "Recommended" in the "Add an activity or resource" "dialogue"
    And I click on "Recommended" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Book" in the "recommended" "core_course > Activity chooser tab"

  Scenario: Favourite a module in the activity chooser
    Given I open the activity chooser
    And I should not see "Starred" in the "Add an activity or resource" "dialogue"
    And I click on "Add Assignment to starred" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Starred" in the "Add an activity or resource" "dialogue"
    When I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    Then I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"
    And I click on "Information about the Assignment activity" "button" in the "favourites" "core_course > Activity chooser tab"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."

  Scenario: Add a favourite module and check it exists when reopening the chooser
    Given I open the activity chooser
    And I click on "Add Assignment to starred" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Add Forum to starred" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Starred" in the "Add an activity or resource" "dialogue"
    And I click on "Close" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Add content" "button" in the "New section" "section"
    And I click on "Activity or resource" "button" in the "New section" "section"
    And I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    Then I should see "Forum" in the "favourites" "core_course > Activity chooser tab"

  Scenario: Add a favourite and then remove it whilst checking the tabs work as expected
    Given I open the activity chooser
    And I should not see "Starred" in the "Add an activity or resource" "dialogue"
    When I click on "Add Assignment to starred" "button" in the "Add an activity or resource" "dialogue"
    # The favourite tab should appear once the user stars an activity.
    Then I should see "Starred" in the "Add an activity or resource" "dialogue"
    And I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    And I click on "Remove Assignment from starred" "button" in the "favourites" "core_course > Activity chooser tab"
    # The favourite tab should dissapear once the user changes tab.
    And I should see "Starred" in the "Add an activity or resource" "dialogue"
    And I click on "All" "link" in the "Add an activity or resource" "dialogue"
    And I should not see "Starred" in the "Add an activity or resource" "dialogue"

  Scenario: The teacher can manage favourites form the favourites tab and the list is refreshed when the user change tab
    Given I open the activity chooser
    And I click on "Add Assignment to starred" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Add Forum to starred" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Starred" in the "Add an activity or resource" "dialogue"
    When I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"
    And I should see "Forum" in the "favourites" "core_course > Activity chooser tab"
    And I click on "Remove Assignment from starred" "button" in the "favourites" "core_course > Activity chooser tab"
    Then I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"
    And I click on "Resources" "link" in the "Add an activity or resource" "dialogue"
    And I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Forum" in the "favourites" "core_course > Activity chooser tab"
    And I should not see "Assignment" in the "favourites" "core_course > Activity chooser tab"

  Scenario: The teacher can undo a unfavourite action done in the activity chooser favourites tab
    Given I open the activity chooser
    And I click on "Add Assignment to starred" "button" in the "Add an activity or resource" "dialogue"
    When I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"
    And I click on "Remove Assignment from starred" "button" in the "favourites" "core_course > Activity chooser tab"
    Then I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"
    And I click on "Add Assignment to starred" "button" in the "favourites" "core_course > Activity chooser tab"
    # Change tab to refresh the favourites tab.
    And I click on "Resources" "link" in the "Add an activity or resource" "dialogue"
    And I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"

  Scenario: The teacher can search for an activity by it's name
    Given I open the activity chooser
    When I set the field "search" to "Lesson"
    Then I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Lesson" in the "Add an activity or resource" "dialogue"

  Scenario: The teacher can search for an activity by it's description
    Given I open the activity chooser
    When I set the field "search" to "The lesson activity module enables a teacher to deliver content"
    Then I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Lesson" in the "Add an activity or resource" "dialogue"

  Scenario: Search results are not returned if the search query does not match any activity name or description
    Given I open the activity chooser
    When I set the field "search" to "Random search query"
    Then I should see "0 results found" in the "Add an activity or resource" "dialogue"

  Scenario: Teacher can return to the default activity chooser state by manually removing the search query
    Given I open the activity chooser
    And I set the field "search" to "Lesson"
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Lesson" in the "Add an activity or resource" "dialogue"
    When I set the field "search" to ""
    And I should not see "1 results found" in the "Add an activity or resource" "dialogue"
    Then ".searchresultscontainer" "css_element" should not be visible
    And ".optionscontainer" "css_element" should exist

  Scenario: Teacher can not see a clear button if a search query is not entered in the activity chooser search bar
    When I open the activity chooser
    Then "Clear search input" "button" should not be visible

  Scenario: Teacher can see a clear button after entering a search query in the activity chooser search bar
    Given I open the activity chooser
    When I set the field "search" to "Search query"
    Then "Clear search input" "button" should be visible

  Scenario: Teacher can not see a clear button if the search query is removed in the activity chooser search bar
    Given I open the activity chooser
    And I set the field "search" to "Search query"
    And "Clear search input" "button" should exist
    When I set the field "search" to ""
    # Waiting for the animation to hide the button to finish.
    And I wait "1" seconds
    Then "Clear search input" "button" should not be visible

  Scenario: Teacher can instantly remove the search query from the activity search bar by clicking on the clear button
    Given I open the activity chooser
    And I set the field "search" to "Search query"
    And I should see "results found" in the "Add an activity or resource" "dialogue"
    When I click on "Clear search input" "button"
    Then I should not see "Search query"
    And ".searchresultscontainer" "css_element" should not be visible
    And ".optionscontainer" "css_element" should exist

  Scenario: Click on an activity chosser category should cancel the current search
    Given I open the activity chooser
    And I set the field "search" to "Search query"
    And I should see "results found" in the "Add an activity or resource" "dialogue"
    And "Clear search input" "button" should be visible
    When I click on "Resources" "link" in the "Add an activity or resource" "dialogue"
    And "Clear search input" "button" should not be visible
    And the field "search" matches value ""
    And I should see "Book" in the "content" "core_course > Activity chooser tab"

  Scenario: Teacher gets the base case for the Activity Chooser tab mode
    When I open the activity chooser
    Then I should see "Assignment" in the "all" "core_course > Activity chooser tab"
    And I should see "Book" in the "all" "core_course > Activity chooser tab"
    # Assessment tab.
    And I click on "Assessment" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Activities that allow evaluation and measurement of student" in the "Add an activity or resource" "dialogue"
    And "Book" "link" should not exist in the "assessment" "core_course > Activity chooser tab"
    And "Assignment" "link" should exist in the "assessment" "core_course > Activity chooser tab"
    # Collaboration tab.
    And I click on "Collaboration" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Tools for collaborative learning" in the "Add an activity or resource" "dialogue"
    And "Assignment" "link" should not exist in the "collaboration" "core_course > Activity chooser tab"
    And "Database" "link" should exist in the "collaboration" "core_course > Activity chooser tab"
    # Communication tab.
    And I click on "Communication" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Activities that facilitate real-time communication" in the "Add an activity or resource" "dialogue"
    And "Database" "link" should not exist in the "communication" "core_course > Activity chooser tab"
    And "Choice" "link" should exist in the "communication" "core_course > Activity chooser tab"
    # Resources tab.
    And I click on "Resources" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Activities and tools to organise and display course materials" in the "Add an activity or resource" "dialogue"
    And "Choice" "link" should not exist in the "content" "core_course > Activity chooser tab"
    And "File" "link" should exist in the "content" "core_course > Activity chooser tab"
    # Interactive content tab.
    And I click on "Interactive content" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Engaging interactive activities" in the "Add an activity or resource" "dialogue"
    And "File" "link" should not exist in the "interactivecontent" "core_course > Activity chooser tab"
    And "H5P" "link" should exist in the "interactivecontent" "core_course > Activity chooser tab"

  Scenario: Teacher can navigate through activity chooser in Topics format course
    When I open the activity chooser
    Then I should see "All" in the "Add an activity or resource" "dialogue"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    # Confirm right key works
    And I press the right key
    And I press the right key
    And the focused element is "Choice" "menuitem" in the "Add an activity or resource" "dialogue"
    # Confirm left key works
    And I press the left key
    And the focused element is "Book" "menuitem" in the "Add an activity or resource" "dialogue"
    # Confirm clicking "x" button closes modal
    And I click on "Close" "button" in the "Add an activity or resource" "dialogue"
    And "Add an activity or resource" "dialogue" should not be visible
    And I open the activity chooser
    # Confirm escape key closes the modal
    And I press the escape key
    And "Add an activity or resource" "dialogue" should not be visible

  Scenario: Teacher can navigate through activity chooser in Weekly format course
    Given I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    When I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    Then I should see "All" in the "Add an activity or resource" "dialogue"
    And I press the tab key
    And I press the tab key
    And I press the tab key
    And I press the tab key
    # Confirm right key works
    And I press the right key
    And I press the right key
    And the focused element is "Choice" "menuitem" in the "Add an activity or resource" "dialogue"
    # Confirm left key works
    And I press the left key
    And the focused element is "Book" "menuitem" in the "Add an activity or resource" "dialogue"
    # Confirm clicking "x" button closes modal
    And I click on "Close" "button" in the "Add an activity or resource" "dialogue"
    And "Add an activity or resource" "dialogue" should not be visible
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    And I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    # Confirm escape key closes the modal
    And I press the escape key
    And "Add an activity or resource" "dialogue" should not be visible

  Scenario: Teacher can access 'More help' from activity information in activity chooser
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    # Confirm more help link exists
    Then "More help" "link" should exist
    # Confirm that corresponding help icon exist
    And ".fa-book" "css_element" should exist
    # Confirm that link opens in new window
    And "Opens in new window" "link" should be visible
    # Confirm the same behaviour for weekly format course
    And I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    And I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    And I should see "All" in the "Add an activity or resource" "dialogue"
    And I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    # Confirm more help link exists
    And "More help" "link" should exist
    # Confirm that corresponding help icon exist
    And ".fa-book" "css_element" should exist
    # Confirm that link opens in new window
    And "Opens in new window" "link" should be visible
