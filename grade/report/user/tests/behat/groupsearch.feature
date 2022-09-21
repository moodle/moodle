@core @core_grades @gradereport_user @javascript
Feature: Group searching functionality within the user report.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following "users" exist:
      | username  | firstname | lastname  | email                 | idnumber  |
      | teacher1  | Teacher   | 1         | teacher1@example.com  | t1        |
      | student1  | Student   | 1         | student1@example.com  | s1        |
      | student2  | Student   | 2         | student2@example.com  | s2        |
    And the following "course enrolments" exist:
      | user      | course | role           |
      | teacher1  | C1     | editingteacher |
      | student1  | C1     | student        |
      | student2  | C1     | student        |
    And the following "groups" exist:
      | name          | course | idnumber |
      | Default group | C1     | dg       |
      | Tutor group   | C1     | tg       |
      | Marker group  | C1     | mg       |
    And the following "group members" exist:
      | user     | group |
      | student1 | dg    |
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I change window size to "large"

  Scenario: A teacher can see the 'group' search widget only when group mode is enabled in the course
    Given I navigate to "View > User report" in the course gradebook
    And ".groupwidget" "css_element" should exist
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | id_groupmode | No groups |
    And I press "Save and display"
    When I navigate to "View > User report" in the course gradebook
    Then ".groupwidget" "css_element" should not exist

  Scenario: A teacher can search for and find a group to find a user in
    Given I navigate to "View > User report" in the course gradebook
    And I click on ".groupwidget" "css_element"
    And I wait until "Select a group" "dialogue" exists
    And I should see "Tutor group" in the "Select a group" "dialogue"
    And I should see "Marker group" in the "Select a group" "dialogue"
    When I set the field "searchinput" to "tutor"
    And I wait "1" seconds
    Then I should see "Tutor group" in the "Select a group" "dialogue"
    And I should not see "Marker group" in the "Select a group" "dialogue"

  Scenario: A teacher can only see the group members in the 'user' search widget after selecting a group option
    Given I navigate to "View > User report" in the course gradebook
    # Confirm that all users are initially displayed in the 'user' search widget.
    And I click on ".userwidget" "css_element"
    And I wait until "Select a user" "dialogue" exists
    And I should see "Student 1" in the "Select a user" "dialogue"
    And I should see "Student 2" in the "Select a user" "dialogue"
    And I click on "Close" "button" in the "Select a user" "dialogue"
    # Select a particular group from the 'group' search widget.
    When I click on "Default group" in the "group" search widget
    # Confirm that only users which are members of the selected group are displayed in the 'user' search widget.
    And I click on ".userwidget" "css_element"
    And I wait until "Select a user" "dialogue" exists
    Then I should see "Student 1" in the "Select a user" "dialogue"
    And I should not see "Student 2" in the "Select a user" "dialogue"
    And I click on "Close" "button" in the "Select a user" "dialogue"
    And I click on "Tutor group" in the "group" search widget
    And I click on ".userwidget" "css_element"
    And I wait until "Select a user" "dialogue" exists
    And I should not see "Student 1" in the "Select a user" "dialogue"
    And I should not see "Student 2" in the "Select a user" "dialogue"
    And I click on "Close" "button" in the "Select a user" "dialogue"
    And I click on "All participants" in the "group" search widget
    And I click on ".userwidget" "css_element"
    And I wait until "Select a user" "dialogue" exists
    And I should see "Student 1" in the "Select a user" "dialogue"
    And I should see "Student 2" in the "Select a user" "dialogue"
