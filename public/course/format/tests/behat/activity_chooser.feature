@core @core_course @core_courseformat @javascript
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
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C      | editingteacher |
    And the following config values are set as admin:
      | enablemoodlenet | 0 | tool_moodlenet |
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on

  Scenario: The teacher can choose to add an activity from the activity items in the activity chooser
    # Validate the activity chooser is opened in this first scenario.
    Given I open the activity chooser
    And I should see "Assignment" in the "Add an activity or resource" "dialogue"
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
    When I click on "Information about the Lesson activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "Lesson" in the "help" "core_course > Activity chooser screen"
    # Confirm modulename_summary is displayed.
    And I should see "Create branching scenarios where students follow different paths based on their answers to questions."
    # Confirm modulename_help is displayed.
    And I should see "Create pages with text, images, video, and questions"
    # Confirm modulename_tip is displayed.
    And I should see "Tip: Map out the flow of your Lesson before building it. Planning the pages and paths ahead of time makes building your Lesson much easier."
    # Confirm more help link exists and it has a correct format.
    And "More help" "link" should exist
    And "Opens in new window" "link" should be visible
    # Validate information panel.
    And I should see "Interactive content" in the "help" "core_course > Activity chooser screen"
    And I should see "Assessment" in the "help" "core_course > Activity chooser screen"
    And I should see "Gradable" in the "help" "core_course > Activity chooser screen"
    And I should see "Yes" in the "help" "core_course > Activity chooser screen"
    And I click on "Back" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Information about the Book activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "Book" in the "help" "core_course > Activity chooser screen"
    And I should see "Organise and display content in a book-like format."
    And I should see "Resources" in the "help" "core_course > Activity chooser screen"
    And I should see "Gradable" in the "help" "core_course > Activity chooser screen"
    And I should see "No" in the "help" "core_course > Activity chooser screen"

  Scenario: The teacher use the activity chooser help panel to go to the specific purpose category
    Given I open the activity chooser
    When I click on "Information about the Forum activity" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Collaboration" "button" in the "help" "core_course > Activity chooser screen"
    Then I should see "Activities for collaborative learning" in the "Add an activity or resource" "dialogue"
    And "Assignment" "link" should not exist in the "collaboration" "core_course > Activity chooser tab"
    And "Database" "link" should exist in the "collaboration" "core_course > Activity chooser tab"
    And I click on "Information about the Forum activity" "button" in the "collaboration" "core_course > Activity chooser tab"
    And I click on "Communication" "button" in the "help" "core_course > Activity chooser screen"
    And I should see "Activities for real-time communication" in the "Add an activity or resource" "dialogue"
    And "Choice" "link" should exist in the "communication" "core_course > Activity chooser tab"

  Scenario: The teacher can hide the activity summary in the activity chooser
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "modules" "core_course > Activity chooser screen"
    And I should see "Collect student submissions such as essays, reports, or projects, and provide feedback and grades." in the "help" "core_course > Activity chooser screen"
    And I click on "Back" "button" in the "Add an activity or resource" "dialogue"
    Then "modules" "core_course > Activity chooser screen" should be visible
    And "help" "core_course > Activity chooser screen" should not be visible
    And "Back" "button" in the "Add an activity or resource" "dialogue" should not be visible
    And I should not see "Collect student submissions such as essays, reports, or projects, and provide feedback and grades." in the "Add an activity or resource" "dialogue"

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
    And I should see "Collect student submissions such as essays, reports, or projects, and provide feedback and grades."

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

  Scenario: The teacher can manage favourites from the favourites tab and the list is refreshed when the user change tab
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

  Scenario: The teacher can undo an unfavourite action done in the activity chooser favourites tab
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

  Scenario: The teacher can search for an activity by name or description in the activity chooser
    When I open the activity chooser
    # Test name.
    Then I set the field "search" to "Database"
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Database" in the "Add an activity or resource" "dialogue"
    # Test summary.
    And I set the field "search" to "Build a shared, searchable collection of entries, like a directory or gallery, that students can contribute to."
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Database" in the "Add an activity or resource" "dialogue"
    # Test help.
    And I set the field "search" to "Define the information each entry should include, such as text, dates, images, or links."
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Database" in the "Add an activity or resource" "dialogue"
    # Test tip.
    And I set the field "search" to "For a quick start, use one of the built-in presets such as an image gallery, journal, or resource list."
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Database" in the "Add an activity or resource" "dialogue"
    # Test non matching search.
    And I set the field "search" to "Random search query"
    And I should see "No results for \"Random search query\"" in the "Add an activity or resource" "dialogue"
    And I should see "Check your spelling or try different words." in the "Add an activity or resource" "dialogue"

  Scenario: Teacher can return to the default activity chooser state by manually removing the search query
    Given I open the activity chooser
    And I set the field "search" to "Forum"
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Forum" in the "Add an activity or resource" "dialogue"
    When I set the field "search" to ""
    And I should not see "1 results found" in the "Add an activity or resource" "dialogue"
    Then ".searchresultscontainer" "css_element" should not be visible
    And ".optionscontainer" "css_element" should exist

  Scenario: Teacher can see the clear button onyl when there is a search query
    When I open the activity chooser
    Then "Clear search input" "button" should not be visible
    And I set the field "search" to "Search query"
    And "Clear search input" "button" should be visible
    And I set the field "search" to "Search query"
    And "Clear search input" "button" should exist
    And I set the field "search" to ""
    # Waiting for the animation to hide the button to finish.
    And I wait "1" seconds
    And "Clear search input" "button" should not be visible

  Scenario: Teacher can instantly remove the search query from the activity search bar by clicking on the clear button
    Given I open the activity chooser
    And I set the field "search" to "exams"
    And I should see "results found" in the "Add an activity or resource" "dialogue"
    When I click on "Clear search input" "button"
    Then I should not see "exams"
    And ".searchresultscontainer" "css_element" should not be visible
    And ".optionscontainer" "css_element" should exist

  Scenario: Click on an activity chooser category should cancel the current search
    Given I open the activity chooser
    And I set the field "search" to "exams"
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
    And I should see "Activities that support evaluation and measurement of student understanding" in the "Add an activity or resource" "dialogue"
    And "Book" "link" should not exist in the "assessment" "core_course > Activity chooser tab"
    And "Assignment" "link" should exist in the "assessment" "core_course > Activity chooser tab"
    # Collaboration tab.
    And I click on "Collaboration" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Activities for collaborative learning" in the "Add an activity or resource" "dialogue"
    And "Assignment" "link" should not exist in the "collaboration" "core_course > Activity chooser tab"
    And "Database" "link" should exist in the "collaboration" "core_course > Activity chooser tab"
    # Communication tab.
    And I click on "Communication" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Activities for real-time communication" in the "Add an activity or resource" "dialogue"
    And "Database" "link" should not exist in the "communication" "core_course > Activity chooser tab"
    And "Choice" "link" should exist in the "communication" "core_course > Activity chooser tab"
    # Resources tab.
    And I click on "Resources" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Activities and tools for organising and displaying course materials" in the "Add an activity or resource" "dialogue"
    And "Choice" "link" should not exist in the "content" "core_course > Activity chooser tab"
    And "File" "link" should exist in the "content" "core_course > Activity chooser tab"
    # Interactive content tab.
    And I click on "Interactive content" "link" in the "Add an activity or resource" "dialogue"
    And I should see "Engaging interactive activities" in the "Add an activity or resource" "dialogue"
    And "File" "link" should not exist in the "interactivecontent" "core_course > Activity chooser tab"
    And "H5P" "link" should exist in the "interactivecontent" "core_course > Activity chooser tab"

  Scenario: Teacher can navigate through activity chooser
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

  Scenario: Check the activity chooser is compatible with weekly format course
    Given the following "courses" exist:
      | fullname  | shortname | format | startdate |
      | Course 2  | C2        | weeks  | 95713920  |
    And the following "course enrolments" exist:
      | user    | course | role           |
      | teacher | C2     | editingteacher |
    And I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    When I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    # We don't need to validate everything again, just some basic elements.
    Then I should see "All" in the "Add an activity or resource" "dialogue"
    And I should see "Collaboration" in the "Add an activity or resource" "dialogue"
    And I should see "Assignment" in the "all" "core_course > Activity chooser tab"
    And I should see "Book" in the "all" "core_course > Activity chooser tab"
    And "Add selected activity" "button" should exist in the "Add an activity or resource" "dialogue"
    And "Add selected activity" "button" should exist in the "Add an activity or resource" "dialogue"
    And "Add Assignment to starred" "button" should exist in the "Add an activity or resource" "dialogue"
