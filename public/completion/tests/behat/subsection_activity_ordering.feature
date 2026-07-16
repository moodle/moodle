@core @core_completion @mod_subsection
Feature: Subsection activities appear in course-page order in completion contexts.
  In order to ensure subsection activities are correctly ordered
  As a teacher
  I need to see that activities inside a subsection are interleaved with
  main section activities in the correct course-page order on the bulk edit completion page.

  Background:
    Given the following "courses" exist:
      | fullname | shortname | category | numsections | initsections | enablecompletion |
      | Course 1 | C1        | 0        | 2           | 1            | 1                |
    And the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
      | student1 | Student   | 1        | student1@example.com |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |
      | student1 | C1     | student        |
    And the following "activities" exist:
      | activity   | name         | course | section | completion | completionview |
      | page       | Activity A   | C1     | 1       | 1          | 1              |
      | subsection | Subsection 1 | C1     | 1       | 0          | 0              |
      | page       | Activity D   | C1     | 2       | 1          | 1              |
      | page       | Activity B   | C1     | 3       | 1          | 1              |
      | page       | Activity C   | C1     | 3       | 1          | 1              |

  @javascript
  Scenario: Bulk edit completion shows subsection activities in course-page order
    When I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Bulk edit activity completion"
    Then I should see "Bulk edit activity completion"
    And "Activity A" "text" should appear before "Activity B" "text" in the "region-main" "region"
    And "Activity B" "text" should appear before "Activity C" "text" in the "region-main" "region"
    And "Activity C" "text" should appear before "Activity D" "text" in the "region-main" "region"
    And I set the field "Course completion tertiary navigation" to "Course completion settings"
    And I expand all fieldsets
    And "Activity A" "text" should appear before "Activity B" "text" in the "region-main" "region"
    And "Activity B" "text" should appear before "Activity C" "text" in the "region-main" "region"
    And "Activity C" "text" should appear before "Activity D" "text" in the "region-main" "region"
    And I click on "Select all/none" "link" in the "#id_activitiescompleted" "css_element"
    And I press "Save changes"
    And I navigate to "Reports" in current page administration
    And I click on "Course completion" "link" in the "region-main" "region"
    And "Activity A" "text" should appear before "Activity B" "text" in the "region-main" "region"
    And "Activity B" "text" should appear before "Activity C" "text" in the "region-main" "region"
    And "Activity C" "text" should appear before "Activity D" "text" in the "region-main" "region"
