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
      | Default <span class="multilang" lang="de">Gruppe</span><span class="multilang" lang="en">group</span> | C1     | dg       |
      | Tutor <span class="multilang" lang="de">Gruppe</span><span class="multilang" lang="en">group</span>   | C1     | tg       |
      | Marker <span class="multilang" lang="de">Gruppe</span><span class="multilang" lang="en">group</span>  | C1     | mg       |
    And the following "group members" exist:
      | user     | group |
      | student1 | dg    |
    And the "multilang" filter is "on"
    And the "multilang" filter applies to "content and headings"
    And I am on the "C1" "Course" page logged in as "teacher1"
    And I change window size to "large"

  Scenario: A teacher can see the 'group' search widget only when group mode is enabled in the course
    Given I navigate to "View > User report" in the course gradebook
    And ".search-widget[data-searchtype='group']" "css_element" should exist
    And I am on "Course 1" course homepage
    And I navigate to "Settings" in current page administration
    And I set the following fields to these values:
      | id_groupmode | No groups |
    And I press "Save and display"
    When I navigate to "View > User report" in the course gradebook
    Then ".search-widget[data-searchtype='group']" "css_element" should not exist

  Scenario: A teacher can search for and find a group to find a user in
    Given I navigate to "View > User report" in the course gradebook
    And I click on ".search-widget[data-searchtype='group']" "css_element"
    And I confirm "Tutor group" in "group" search within the gradebook widget exists
    And I confirm "Marker group" in "group" search within the gradebook widget exists
    When I set the field "Search groups" to "tutor"
    And I wait "1" seconds
    Then I confirm "Tutor group" in "group" search within the gradebook widget exists
    And I confirm "Marker group" in "group" search within the gradebook widget does not exist

  Scenario: A teacher can only see the group members in the 'user' search widget after selecting a group option
    Given I navigate to "View > User report" in the course gradebook
    # Confirm that all users are initially displayed in the 'user' search widget.
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget exists
    # Select a particular group from the 'group' search widget.
    When I click on "Default group" in the "group" search widget
    # Confirm that only users which are members of the selected group are displayed in the 'user' search widget.
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget does not exist
    And I click on "Tutor group" in the "group" search widget
    And I confirm "Student 1" in "user" search within the gradebook widget does not exist
    And I confirm "Student 2" in "user" search within the gradebook widget does not exist
    And I click on "All participants" in the "group" search widget
    And I confirm "Student 1" in "user" search within the gradebook widget exists
    And I confirm "Student 2" in "user" search within the gradebook widget exists
