@availability @availability_completion
Feature: Confirm that availability_completion works with previous activity setting
  In order to control student access to activities
  As a teacher
  I need to set completion conditions which prevent student access

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | enablecompletion | numsections |
      | Course 1 | C1        | topics | 1                | 5           |
    And the following "users" exist:
      | username |
      | teacher1 |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
    Given the following "activities" exist:
      | activity | name           | intro              | course | idnumber | groupmode | completion | section |
      | page     | Page1          | Page 1 description | C1     | page1    | 1         | 1          | 1       |
      | page     | Page Ignored 1 | Page Ignored       | C1     | pagei1   | 1         | 0          | 1       |
      | page     | Page2          | Page 2 description | C1     | page2    | 1         | 1          | 3       |
      | page     | Page3          | Page 3 description | C1     | page3    | 1         | 1          | 4       |

  @javascript
  Scenario: Test condition with previous activity on an activity
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Set Page3 restriction to Previous Activity with completion.
    When I open "Page3" actions menu
    And I click on "Edit settings" "link" in the "Page3" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save and return to course"
    Then I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    When I turn editing mode off
    Then I should see "Not available unless: The activity Page2 is marked complete" in the "region-main" "region"

    # Remove Page 2 and check Page3 depends now on Page1.
    When I turn editing mode on
    And I change window size to "large"
    And I delete "Page2" activity
    And I turn editing mode off
    Then I should see "Not available unless: The activity Page1 is marked complete" in the "region-main" "region"

  @javascript
  Scenario: Test previous activity availability when duplicate an activity
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Set Page3 restriction to Previous Activity with completion.
    When I open "Page3" actions menu
    And I click on "Edit settings" "link" in the "Page3" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save and return to course"
    Then I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    When I turn editing mode off
    Then I should see "Not available unless: The activity Page2 is marked complete" in the "region-main" "region"

    # Duplicate Page3.
    When I turn editing mode on
    And I duplicate "Page3" activity
    And I turn editing mode off
    Then I should see "Not available unless: The activity Page3 is marked complete" in the "region-main" "region"

  @javascript
  Scenario: Test previous activity availability when modify completion tacking
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Set Page3 restriction to Previous Activity with completion.
    When I open "Page3" actions menu
    And I click on "Edit settings" "link" in the "Page3" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save and return to course"
    Then I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    When I turn editing mode off
    Then I should see "Not available unless: The activity Page2 is marked complete" in the "region-main" "region"

    # Test if I disable completion tracking on Page2 section 5 depends on Page2.
    When I turn editing mode on
    And I change window size to "large"
    When I open "Page2" actions menu
    And I click on "Edit settings" "link" in the "Page2" activity
    And I set the following fields to these values:
      | None | 1 |
    And I press "Save and return to course"
    When I turn editing mode off
    Then I should see "Not available unless: The activity Page1 is marked complete" in the "region-main" "region"

  @javascript
  Scenario: Test condition with previous activity on a section
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Set section 4 restriction to Previous Activity with completion.
    When I edit the section "4"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save changes"
    Then I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    When I turn editing mode off
    Then I should see "Not available unless: The activity Page2 is marked complete" in the "region-main" "region"

    # Remove Page 2 and check Section 4 depends now on Page1.
    When I am on "Course 1" course homepage with editing mode on
    And I change window size to "large"
    And I delete "Page2" activity
    And I turn editing mode off
    Then I should see "Not available unless: The activity Page1 is marked complete" in the "region-main" "region"

  @javascript
  Scenario: Test condition with previous activity on the first activity of the course
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on

    # Try to set Page1 restriction to Previous Activity with completion.
    When I open "Page1" actions menu
    And I click on "Edit settings" "link" in the "Page1" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    Then the "Activity or resource" select box should not contain "Previous activity with completion"

    # Set Page2 restriction to Previous Activity with completion and delete Page1.
    When I am on "Course 1" course homepage
    When I open "Page2" actions menu
    And I click on "Edit settings" "link" in the "Page2" activity
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save and return to course"
    Then I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    # Delete Page 1 and check than Page2 now depends on a missing activity (no previous activity found).
    When I am on "Course 1" course homepage
    And I delete "Page1" activity
    And I turn editing mode off
    Then I should see "Not available unless: The activity (Missing activity)" in the "region-main" "region"

  @javascript
  Scenario: Test previous activities on empty sections
    Given I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I change window size to "large"

    # Set section 2 restriction to Previous Activity with completion.
    When I edit the section "2"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save changes"
    Then I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    And I turn editing mode off
    And I should see "Not available unless: The activity Page1 is marked complete" in the "region-main" "region"

    # Set section 5 restriction to Previous Activity with completion.
    And I am on "Course 1" course homepage with editing mode on
    And I edit the section "5"
    And I expand all fieldsets
    And I click on "Add restriction..." "button"
    And I click on "Activity completion" "button" in the "Add restriction..." "dialogue"
    And I click on "Displayed if student doesn't meet this condition • Click to hide" "link"
    And I set the field "Activity or resource" to "Previous activity with completion"
    And I press "Save changes"
    And I should see "Not available unless: The previous activity with completion" in the "region-main" "region"

    And I turn editing mode off
    Then I should see "Not available unless: The activity Page3 is marked complete" in the "region-main" "region"

    # Test if I disable completion tracking on Page3 section 5 depends on Page2.
    And I am on "Course 1" course homepage with editing mode on
    And I open "Page3" actions menu
    And I click on "Edit settings" "link" in the "Page3" activity
    And I set the following fields to these values:
      | None | 1 |
    And I press "Save and return to course"

    And I turn editing mode off
    And I should see "Not available unless: The activity Page2 is marked complete" in the "region-main" "region"
