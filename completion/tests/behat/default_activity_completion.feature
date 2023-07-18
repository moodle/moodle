@core @core_completion @javascript
Feature: Allow teachers to edit the default activity completion rules in a course.
  In order to set the activity completion defaults for new activities
  As a teacher
  I need to be able to edit the completion rules for a group of activities.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | enablecompletion |
      | Course 1 | C1        | 0        | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | Frist | teacher1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  # Given I am a teacher in a course with completion tracking enabled and activities present.
  # When I edit activity completion defaults for activity types.
  # Then the completion rule defaults should apply only to activities created from that point onwards.
  Scenario: Edit default activity completion rules for assignment
    Given the following "activity" exists:
      | activity   | assign               |
      | course     | C1                   |
      | name       | Test assignment one  |
      | completion | 0                    |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Assignments" "checkbox"
    And I click on "Edit" "button"
    And I should see "Completion tracking"
    And I should see "The changes will affect the following 1 activities or resources:"
    And I set the following fields to these values:
      | completion         | Show activity as complete when conditions are met |
      | completionview     | 0                                                 |
      | completionusegrade | 1                                                 |
      | completionsubmit   | 1                                                 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I should see "With conditions" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should not see "Student must view this activity to complete it" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should see "Student must receive a grade to complete this activity" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should see "Student must make a submission" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I should not see "Completion expected on" in the "//div[contains(concat(' ', normalize-space(@class), ' '), ' row ')][.//*[text() = 'Assignments']]" "xpath_element"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    # Completion tracking 2 = Show activity as complete when conditions are met.
    And the field "Completion tracking" matches value "2"
    And the field "completionview" matches value "0"
    And the field "completionusegrade" matches value "1"
    And the field "completionsubmit" matches value "1"
    But I am on the "Test assignment one" Activity page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    # Completion tracking 0 = Do not indicate activity completion.
    And the field "Completion tracking" matches value "0"

  Scenario: Edit default activity completion rules for forum
    Given the following "activity" exists:
      | activity | forum                |
      | course   | C1                   |
      | name     | Test forum one       |
      | completion | 0                    |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I select "Default activity completion" from the "Course completion tertiary navigation" singleselect
    And I click on "Forums" "checkbox"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | completion                | Show activity as complete when conditions are met |
      | completionview            | 0                                                 |
      # 0 = Rating.
      | completiongradeitemnumber | 0                                                 |
      | completionpassgrade       | 1                                                 |
      | completionpostsenabled    | 1                                                 |
      | completionposts           | 2                                                 |
      | completionrepliesenabled  | 1                                                 |
      | completionreplies         | 3                                                 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Forum" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    # Completion tracking 2 = Show activity as complete when conditions are met.
    And the field "Completion tracking" matches value "2"
    And the field "completionview" matches value "0"
    # Value 0 for completiongradeitemnumber is "Rating".
    And the field "completiongradeitemnumber" matches value "0"
    And the field "completionpassgrade" matches value "1"
    And the field "completionpostsenabled" matches value "1"
    And the field "completionposts" matches value "2"
    And the field "completionrepliesenabled" matches value "1"
    And the field "completionreplies" matches value "3"
    But I am on the "Test forum one" Activity page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    # Completion tracking 0 = Do not indicate activity completion.
    And the field "Completion tracking" matches value "0"

  Scenario: Edit default activity completion rules for glossary
    Given the following "activity" exists:
      | activity   | glossary             |
      | course     | C1                   |
      | name       | Test glossary one    |
      | completion | 0                    |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I select "Default activity completion" from the "Course completion tertiary navigation" singleselect
    And I click on "Glossaries" "checkbox"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | completion                | Show activity as complete when conditions are met |
      | completionview            | 0                                                 |
      | completionusegrade        | 1                                                 |
      | completionentriesenabled  | 1                                                 |
      | completionentries         | 2                                                 |
    And I click on "Save changes" "button"
    Then I should see "Changes saved"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Glossary" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    # Completion tracking 2 = Show activity as complete when conditions are met.
    And the field "Completion tracking" matches value "2"
    And the field "completionview" matches value "0"
    And the field "completionusegrade" matches value "1"
    And the field "completionentriesenabled" matches value "1"
    And the field "completionentries" matches value "2"
    But I am on the "Test glossary one" Activity page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    # Completion tracking 0 = Do not indicate activity completion.
    And the field "Completion tracking" matches value "0"

  Scenario: Edit default activity completion without rules for automatic completion
    Given I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I select "Default activity completion" from the "Course completion tertiary navigation" singleselect
    And I click on "Assignments" "checkbox"
    And I click on "Edit" "button"
    And I set the following fields to these values:
      | completion         | Show activity as complete when conditions are met |
      | completionview     | 0                                                 |
      | completionusegrade | 0                                                 |
      | completionsubmit   | 0                                                 |
    And I click on "Save changes" "button"
    Then I should see "When you select automatic completion, you must also enable at least one requirement (below)."
    And I should not see "Changes saved"
