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
    Then I should see "New Assignment"
    And I set the following fields to these values:
      | Assignment name | Test Assignment |
    And I press "Save and return to course"
    Then I should see "Test Assignment" in the "General" "section"

  Scenario: The teacher can choose to add an activity from the activity summary in the activity chooser
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    When I click on "Add a new Assignment" "link" in the "help" "core_course > Activity chooser screen"
    Then I should see "New Assignment"

  Scenario: Show summary
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should see "Assignment" in the "help" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."
    # Confirm show summary also works for weekly format course
    And I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    And I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    And I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Assignment" in the "help" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."

  Scenario: Hide summary
    Given I open the activity chooser
    When I click on "Information about the Assignment activity" "button" in the "modules" "core_course > Activity chooser screen"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "help" "core_course > Activity chooser screen"
    And I should see "Back" in the "help" "core_course > Activity chooser screen"
    When I click on "Back" "button" in the "help" "core_course > Activity chooser screen"
    Then "modules" "core_course > Activity chooser screen" should be visible
    And "help" "core_course > Activity chooser screen" should not be visible
    And "Back" "button" should not exist in the "modules" "core_course > Activity chooser screen"
    And I should not see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "Add an activity or resource" "dialogue"
    # Confirm hide summary also works for weekly format course
    And I am on "C2" course homepage with editing mode on
    And I click on "Add content" "button" in the "13 January - 19 January" "section"
    And I click on "Activity or resource" "button" in the "13 January - 19 January" "section"
    And I click on "Information about the Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Back" "button" in the "help" "core_course > Activity chooser screen"
    And "modules" "core_course > Activity chooser screen" should be visible
    And "help" "core_course > Activity chooser screen" should not be visible
    And "Back" "button" should not exist in the "modules" "core_course > Activity chooser screen"
    And I should not see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback." in the "Add an activity or resource" "dialogue"

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
    And I click on "Star Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Starred" in the "Add an activity or resource" "dialogue"
    When I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    Then I should see "Assignment" in the "favourites" "core_course > Activity chooser tab"
    And I click on "Information about the Assignment activity" "button" in the "favourites" "core_course > Activity chooser tab"
    And I should see "The assignment activity module enables a teacher to communicate tasks, collect work and provide grades and feedback."

  Scenario: Add a favourite module and check it exists when reopening the chooser
    Given I open the activity chooser
    And I click on "Star Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Star Forum activity" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Starred" in the "Add an activity or resource" "dialogue"
    And I click on "Close" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Add content" "button" in the "New section" "section"
    And I click on "Activity or resource" "button" in the "New section" "section"
    And I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    Then I should see "Forum" in the "favourites" "core_course > Activity chooser tab"

  Scenario: Add a favourite and then remove it whilst checking the tabs work as expected
    Given I open the activity chooser
    And I click on "Star Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Starred" "link" in the "Add an activity or resource" "dialogue"
    And I click on "Star Assignment activity" "button" in the "Add an activity or resource" "dialogue"
    Then I should not see "Starred" in the "Add an activity or resource" "dialogue"

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
    And ".option" "css_element" should not exist in the ".searchresultitemscontainer" "css_element"

  Scenario: Teacher can return to the default activity chooser state by manually removing the search query
    Given I open the activity chooser
    And I set the field "search" to "Lesson"
    And I should see "1 results found" in the "Add an activity or resource" "dialogue"
    And I should see "Lesson" in the "Add an activity or resource" "dialogue"
    When I set the field "search" to ""
    And I should not see "1 results found" in the "Add an activity or resource" "dialogue"
    Then ".searchresultscontainer" "css_element" should not be visible
    And ".optionscontainer" "css_element" should exist

  Scenario: Teacher can not see a "clear" button if a search query is not entered in the activity chooser search bar
    When I open the activity chooser
    Then "Clear search input" "button" should not be visible

  Scenario: Teacher can see a "clear" button after entering a search query in the activity chooser search bar
    Given I open the activity chooser
    When I set the field "search" to "Search query"
    Then "Clear search input" "button" should not be visible

  Scenario: Teacher can not see a "clear" button if the search query is removed in the activity chooser search bar
    Given I open the activity chooser
    And I set the field "search" to "Search query"
    And "Clear search input" "button" should exist
    When I set the field "search" to ""
    # Waiting for the animation to hide the button to finish.
    And I wait "1" seconds
    Then "Clear search input" "button" should not be visible

  Scenario: Teacher can instantly remove the search query from the activity search bar by clicking on the "clear" button
    Given I open the activity chooser
    And I set the field "search" to "Search query"
    And I should see "results found" in the "Add an activity or resource" "dialogue"
    When I click on "Clear search input" "button"
    Then I should not see "Search query"
    And ".searchresultscontainer" "css_element" should not be visible
    And ".optionscontainer" "css_element" should exist

  Scenario: Teacher gets the base case for the Activity Chooser tab mode
    Given I open the activity chooser
    And I should see "Activities" in the "Add an activity or resource" "dialogue"
    When I click on "Activities" "link" in the "Add an activity or resource" "dialogue"
    Then I should not see "Book" in the "activity" "core_course > Activity chooser tab"
    And I click on "Resources" "link" in the "Add an activity or resource" "dialogue"
    And I should not see "Assignment" in the "resources" "core_course > Activity chooser tab"

  Scenario: Teacher gets the simple case for the Activity Chooser tab mode
    Given I log out
    And I log in as "admin"
    And I am on site homepage
    When I navigate to "Courses > Activity chooser > Activity chooser settings" in site administration
    And I select "Starred, All, Recommended" from the "Activity chooser tabs" singleselect
    And I press "Save changes"
    And I log out
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on
    And I open the activity chooser
    Then I should not see "Activities" in the "Add an activity or resource" "dialogue"
    And I should not see "Resources" in the "Add an activity or resource" "dialogue"

  Scenario: Teacher gets the final case for the Activity Chooser tab mode
    Given I log out
    And I log in as "admin"
    And I am on site homepage
    When I navigate to "Courses > Activity chooser > Activity chooser settings" in site administration
    And I select "Starred, Activities, Resources, Recommended" from the "Activity chooser tabs" singleselect
    And I press "Save changes"
    And I log out
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on
    And I open the activity chooser
    Then I should not see "All" in the "Add an activity or resource" "dialogue"
    And I should see "Activities" in the "Add an activity or resource" "dialogue"
    And I should see "Resources" in the "Add an activity or resource" "dialogue"

  Scenario: Recommended tab is displayed in the right position depending on the activitychoosertabmode setting
    Given I log out
    And I log in as "admin"
    And I navigate to "Courses > Activity chooser > Recommended activities" in site administration
    And I click on ".activity-recommend-checkbox" "css_element" in the "Book" "table_row"
    And the following config values are set as admin:
      # 3 = Starred, Recommended, All, Activities, Resources
      | activitychoosertabmode | 3 |
    And I log out
    And I log in as "teacher"
    And I am on "Course" course homepage with editing mode on
    When I open the activity chooser
    Then "Recommended" "link" should appear before "All" "link" in the "Add an activity or resource" "dialogue"
    But the following config values are set as admin:
      # 0 = Starred, All, Activities, Resources, Recommended
      | activitychoosertabmode | 0 |
    And I reload the page
    And I open the activity chooser
    And "Recommended" "link" should appear after "Resources" "link" in the "Add an activity or resource" "dialogue"
    But the following config values are set as admin:
      # 1 = Starred, All, Recommended
      | activitychoosertabmode | 1 |
    And I reload the page
    And I open the activity chooser
    And "Recommended" "link" should appear after "All" "link" in the "Add an activity or resource" "dialogue"
    But the following config values are set as admin:
      # 2 = Starred, Activities, Resources, Recommended
      | activitychoosertabmode | 2 |
    And I reload the page
    And I open the activity chooser
    And "Recommended" "link" should appear after "Resources" "link" in the "Add an activity or resource" "dialogue"
    But the following config values are set as admin:
      # 4 = Starred, Recommended, All
      | activitychoosertabmode | 4 |
    And I reload the page
    And I open the activity chooser
    And "Recommended" "link" should appear before "All" "link" in the "Add an activity or resource" "dialogue"
    But the following config values are set as admin:
      # 5 = Starred, Recommended, Activities, Resources
      | activitychoosertabmode | 5 |
    And I reload the page
    And I open the activity chooser
    And "Recommended" "link" should appear before "Activities" "link" in the "Add an activity or resource" "dialogue"

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
