@core @core_availability
Feature: edit_availability
  In order to control which students can see activities
  As a teacher
  I need to set up availability options for activities and sections

  # PURPOSE OF THIS TEST FEATURE:
  #
  # This test covers the user interface around editing availability conditions,
  # especially the JavaScript code which is not tested elsewhere (e.g. does the
  # 'Add restriction' dialog work). It tests both forms and also the admin
  # setting interface.
  #
  # This test does not check the detailed behaviour of the availability system,
  # which is mainly covered in PHPUnit (and, from the user interface
  # perspective, in the other Behat tests for each type of condition).

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1        | topics |
    And the following "users" exist:
      | username |
      | teacher1 |
      | student1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activity" exists:
      | activity | forum   |
      | course   | C1      |
      | name     | MyForum |

  Scenario: Confirm the 'enable availability' option is working
    Given the following config values are set as admin:
      | enableavailability | 0 |
    When I log in as "teacher1"
    And the following "activity" exists:
      | activity    | page                        |
      | course      | C1                          |
      | idnumber    | 0001                        |
      | section     | 1                           |
      | name        | Page1                       |
      | intro       | pageintro                   |
    And I am on "Course 1" course homepage with editing mode on
    And I follow "Page1"
    And I navigate to "Settings" in current page administration
    Then "Restrict access" "fieldset" should not exist

    Given I am on "Course 1" course homepage
    When I edit the section "1"
    Then "Restrict access" "fieldset" should not exist

    And the following config values are set as admin:
      | enableavailability | 1 |

    And the following "activity" exists:
      | activity    | page                        |
      | course      | C1                          |
      | idnumber    | 0002                        |
      | name        | Page2                       |
    And I am on the "Page2" "page activity editing" page
    Then "Restrict access" "fieldset" should exist

    Given I am on "Course 1" course homepage
    When I edit the section "1"
    Then "Restrict access" "fieldset" should exist

  @javascript
  Scenario: Edit availability using settings in activity form
    # Set up.
    Given the following "activity" exists:
      | activity | page |
      | course   | C1   |
      | section  | 1    |
      | name     | P1   |
    And I am on the "P1" "page activity editing" page logged in as "teacher1"
    And I expand all fieldsets
    Then I should see "None" in the "Restrict access" "fieldset"

    # Add a Date restriction and check it appears.
    When I click on "Add restriction..." "button"
    Then "Add restriction..." "dialogue" should be visible
    When I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then "Add restriction..." "dialogue" should not exist
    And I should not see "None" in the "Restrict access" "fieldset"
    And "Restriction type" "select" should be visible
    And I should see "Date" in the "Restrict access" "fieldset"
    And ".availability-item .availability-eye img" "css_element" should be visible
    And ".availability-item .availability-delete img" "css_element" should be visible
    And the "alt" attribute of ".availability-item .availability-eye img" "css_element" should contain "Displayed if student"

    # Toggle the eye icon.
    When I click on ".availability-item .availability-eye img" "css_element"
    Then the "alt" attribute of ".availability-item .availability-eye img" "css_element" should contain "Hidden entirely"
    When I click on ".availability-item .availability-eye img" "css_element"
    Then the "alt" attribute of ".availability-item .availability-eye img" "css_element" should contain "Displayed if student"

    # Click the delete button.
    When I click on ".availability-item .availability-delete img" "css_element"
    Then I should not see "Date" in the "Restrict access" "fieldset"

    # Add a nested restriction set and check it appears.
    When I click on "Add restriction..." "button"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    Then ".availability-children .availability-list" "css_element" should be visible
    And I should see "None" in the ".availability-children .availability-list" "css_element"
    And I should see "Please set" in the ".availability-children .availability-list" "css_element"
    And I should see "Add restriction" in the ".availability-children .availability-list" "css_element"

    # Click on the button to add a restriction inside the nested set.
    When I click on "Add restriction..." "button" in the ".availability-children .availability-list" "css_element"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then I should not see "None" in the ".availability-children .availability-list" "css_element"
    And I should not see "Please set" in the ".availability-children .availability-list" "css_element"
    And I should see "Date" in the ".availability-children .availability-list" "css_element"

    # OK, let's delete the date inside the nested set...
    When I click on ".availability-item .availability-delete img" "css_element" in the ".availability-item" "css_element"
    Then I should not see "Date" in the ".availability-children .availability-list" "css_element"
    And I should see "None" in the ".availability-children .availability-list" "css_element"

    # ...and the nested set itself.
    When I click on ".availability-none .availability-delete img" "css_element"
    Then ".availability-children .availability-list" "css_element" should not exist

    # Add two dates so we can check the connectors.
    When I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then I should see "and" in the "Restrict access" "fieldset"
    And "Required restrictions" "select" should be visible

    # Try changing the connector type.
    When I set the field "Required restrictions" to "any"
    Then I should not see "and" in the "Restrict access" "fieldset"
    And I should see "or" in the "Restrict access" "fieldset"

    # Now delete one of the dates and check the connector goes away.
    When I click on ".availability-item .availability-delete img" "css_element"
    Then I should not see "or" in the "Restrict access" "fieldset"

    # Add a nested restriction set with two dates so there will be inner connector.
    When I click on "Add restriction..." "button"
    And I click on "Restriction set" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button" in the ".availability-children .availability-list" "css_element"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I click on "Add restriction..." "button" in the ".availability-children .availability-list" "css_element"
    And I click on "Date" "button" in the "Add restriction..." "dialogue"
    Then I should see "and" in the ".availability-children .availability-list .availability-connector" "css_element"

    # Check changing the outer one does not affect the inner one.
    When I set the field "Required restrictions" to "all"
    Then I should not see "or" in the "Restrict access" "fieldset"
    When I set the field "Required restrictions" to "any"
    Then I should see "or" in the "Restrict access" "fieldset"
    And I should not see "or" in the ".availability-children .availability-list .availability-connector" "css_element"

  @javascript
  Scenario: Edit availability using settings in section form
    # Set up.
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Edit a section
    When I edit the section "1"
    And I expand all fieldsets
    Then I should see "None" in the "Restrict access" "fieldset"

    # Add a Date restriction and check it appears.
    When I click on "Add restriction..." "button"
    When I click on "Date" "button" in the "Add restriction..." "dialogue"
    And I should not see "None" in the "Restrict access" "fieldset"
    And "Restriction type" "select" should be visible
    And I should see "Date" in the "Restrict access" "fieldset"

  @javascript
  Scenario: 'Add group/grouping access restriction' button unavailable
    # Button does not exist when conditional access restrictions are turned off.
    Given the following config values are set as admin:
      | enableavailability | 0 |
    And I am on the "MyForum" "forum activity editing" page logged in as admin
    When I expand all fieldsets
    Then "Add group/grouping access restriction" "button" should not exist

  @javascript
  Scenario: Use the 'Add group/grouping access restriction' button
    # Button should initially be disabled.
    Given the following "groupings" exist:
      | name | course | idnumber |
      | GX1  | C1     | GXI1     |
    And I am on the "MyForum" "forum activity editing" page logged in as admin
    When I expand all fieldsets
    Then the "Add group/grouping access restriction" "button" should be disabled

    # Turn on separate groups.
    And I set the field "Group mode" to "Separate groups"
    And the "Add group/grouping access restriction" "button" should be enabled

    # Press the button and check it adds a restriction and disables itself.
    And I should see "None" in the "Restrict access" "fieldset"
    And I press "Add group/grouping access restriction"
    And I should see "Group" in the "Restrict access" "fieldset"
    And the "Add group/grouping access restriction" "button" should be disabled

    # Delete the restriction and check it is enabled again.
    And I click on "Delete" "link" in the "Restrict access" "fieldset"
    And the "Add group/grouping access restriction" "button" should be enabled

    # Try a grouping instead.
    And I set the field "Grouping" to "GX1"
    And I press "Add group/grouping access restriction"
    And I should see "Grouping" in the "Restrict access" "fieldset"

    # Check the button still works after saving and editing.
    And I press "Save and display"
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    And the "Add group/grouping access restriction" "button" should be disabled
    And I should see "Grouping" in the "Restrict access" "fieldset"

    # And check it's still active if I delete the condition.
    And I click on "Delete" "link" in the "Restrict access" "fieldset"
    And the "Add group/grouping access restriction" "button" should be enabled
