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
      | teacher1 | Teacher | First | teacher1@example.com |
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
    And I click on "Expand Assignment" "button"
    And I set the following fields to these values:
      | id_completion_assign_2    | 1 |
      | completionview_assign     | 1 |
      | completionusegrade_assign | 1 |
      | completionsubmit_assign   | 1 |
    And I should not see "Cancel" in the "[data-region='activitycompletion-forum']" "css_element"
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-assign']" "css_element"
    Then I should see "Changes saved"
    And I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Expand Assignment" "button"
    And I set the following fields to these values:
      | completionview_assign     | 0 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-assign']" "css_element"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Assignment" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    # Completion tracking 2 = Add requirements.
    And the field "Add requirements" matches value "1"
    And the field "completionview" matches value "0"
    And the field "completionusegrade" matches value "1"
    And the field "completionsubmit" matches value "1"
    But I am on the "Test assignment one" Activity page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    # Completion tracking 0 = Do not indicate activity completion.
    And the field "None" matches value "1"

  Scenario: Edit default activity completion rules for forum
    Given the following "activity" exists:
      | activity | forum                |
      | course   | C1                   |
      | name     | Test forum one       |
      | completion | 0                    |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Expand Forum" "button"
    And I set the following fields to these values:
      # 2 = Add requeriments.
      | id_completion_forum_2           | 1 |
      | completionview_forum            | 0 |
      # 0 = Rating.
      | completionusegrade_forum        | 1 |
      | completiongradeitemnumber_forum | 0 |
      # 1 = Passing grade.
      | completionpassgrade_forum       | 1 |
      | completionpostsenabled_forum    | 1 |
      | completionposts_forum           | 2 |
      | completionrepliesenabled_forum  | 1 |
      | completionreplies_forum         | 3 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-forum']" "css_element"
    Then I should see "Changes saved"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Forum" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    # Completion tracking 2 = Add requirements.
    And the field "Add requirements" matches value "1"
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
    # None checked = Do not indicate activity completion.
    And the field "None" matches value "1"

  Scenario: Edit default activity completion rules for glossary
    Given the following "activity" exists:
      | activity   | glossary             |
      | course     | C1                   |
      | name       | Test glossary one    |
      | completion | 0                    |
    And I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Expand Glossary" "button"
    And I set the following fields to these values:
      # Add requirements = 2.
      | id_completion_glossary_2           | 1 |
      | completionview_glossary            | 0 |
      | completionusegrade_glossary        | 1 |
      | completionentriesenabled_glossary  | 1 |
      | completionentries_glossary         | 2 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-glossary']" "css_element"
    Then I should see "Changes saved"
    And I am on "Course 1" course homepage with editing mode on
    And I press "Add an activity or resource"
    And I click on "Add a new Glossary" "link" in the "Add an activity or resource" "dialogue"
    And I expand all fieldsets
    # Completion tracking 2 = Add requirements.
    And the field "Add requirements" matches value "1"
    And the field "completionview" matches value "0"
    And the field "completionusegrade" matches value "1"
    And the field "completionentriesenabled" matches value "1"
    And the field "completionentries" matches value "2"
    But I am on the "Test glossary one" Activity page
    And I navigate to "Settings" in current page administration
    And I expand all fieldsets
    # Completion tracking 0 = Do not indicate activity completion.
    And the field "None" matches value "1"

  Scenario: Edit default activity completion rules for several activities
    Given I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Expand Assignment" "button"
    And I set the following fields to these values:
      | id_completion_assign_2    | 1 |
      | completionview_assign     | 0 |
      | completionusegrade_assign | 0 |
      | completionsubmit_assign   | 1 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-assign']" "css_element"
    And I should see "Changes saved"
    And I click on "Expand Forum" "button"
    And I set the following fields to these values:
      | id_completion_forum_2              | 1 |
      | completionview_forum               | 0 |
      | completionpostsenabled_forum       | 1 |
      | completionposts_forum              | 3 |
      | completiondiscussionsenabled_forum | 0 |
      | completionrepliesenabled_forum     | 0 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-forum']" "css_element"
    And I should see "Changes saved"
    And I click on "Expand SCORM package" "button"
    And I set the following fields to these values:
      | id_completion_scorm_2              | 1 |
      | completionview_scorm               | 0 |
      | completionscoreenabled_scorm       | 1 |
      | completionscorerequired_scorm      | 3 |
      | completionstatusrequired_scorm[2]  | 1 |
      | completionstatusrequired_scorm[4]  | 0 |
      | completionstatusallscos_scorm      | 1 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-scorm']" "css_element"
    And I should see "Changes saved"
    And I click on "Expand Book" "button"
    And I set the following fields to these values:
      | completion_book         | Do not indicate activity completion                 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-book']" "css_element"
    And I should see "Changes saved"
    And I click on "Expand Chat" "button"
    And I set the following fields to these values:
    # Students must manually mark the activity as done = 1.
      | id_completion_chat_1         | 1  |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-chat']" "css_element"
    And I should see "Changes saved"
    # Change current page and go back to "Default activity completion", to confirm the form values have been saved properly.
    And I set the field "Course completion tertiary navigation" to "Course completion settings"
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    Then the field "id_completion_chat_1" matches value "1"
    # Check that the rules for book, assignment and forum are still the same.
    And I click on "Expand Book" "button"
    And the field "id_completion_book_0" matches value "1"
    And I click on "Expand Assignment" "button"
    And the field "id_completion_assign_2" matches value "1"
    And the field "completionview_assign" matches value "0"
    And the field "completionusegrade_assign" matches value "0"
    And the field "completionsubmit_assign" matches value "1"
    And I click on "Expand Forum" "button"
    And the field "id_completion_forum_2" matches value "1"
    And the field "completionview_forum" matches value "0"
    And the field "completionpostsenabled_forum" matches value "1"
    And the field "completionposts_forum" matches value "3"
    And the field "completiondiscussionsenabled_forum" matches value "0"
    And the field "completionrepliesenabled_forum" matches value "0"
    And I click on "Expand SCORM package" "button"
    And the field "id_completion_scorm_2" matches value "1"
    And the field "completionview_scorm" matches value "0"
    And the field "completionscoreenabled_scorm" matches value "1"
    And the field "completionscorerequired_scorm" matches value "3"
    And the field "completionstatusrequired_scorm[2]" matches value "1"
    And the field "completionstatusrequired_scorm[4]" matches value "0"
    And the field "completionstatusallscos_scorm" matches value "1"

  Scenario: Edit default activity completion without rules for automatic completion
    Given I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I click on "Expand Assignment" "button"
    And I set the following fields to these values:
      | id_completion_assign_2    | 1 |
      | completionview_assign     | 0 |
      | completionusegrade_assign | 0 |
      | completionsubmit_assign   | 0 |
    And I click on "Save changes" "button" in the "[data-region='activitycompletion-assign']" "css_element"
    Then I should see "You must select at least one condition"
    And I should not see "Changes saved"

  Scenario: Activities in Default activity completion are ordered alphabetically
    Given I am on the "Course 1" course page logged in as teacher1
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    Then "Survey" "text" should appear before "Text and media area" "text"
