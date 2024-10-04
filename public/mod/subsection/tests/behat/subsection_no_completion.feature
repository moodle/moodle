@mod @mod_subsection
Feature: Subsection does not have completion.
  In order to use subsections as normal sections
  As an teacher
  I need to use subsection activity without completion

  Background:
    Given the following "users" exist:
      | username | firstname  | lastname  | email                 |
      | teacher1 | Teacher    | 1         | teacher1@example.com  |
    And the following "courses" exist:
      | fullname | shortname  | category  | numsections | initsections | enablecompletion |
      | Course 1 | C1         | 0         | 1           | 1            | 1                |
    And the following "course enrolments" exist:
      | user      | course  | role            |
      | teacher1  | C1      | editingteacher  |
    And the following "activities" exist:
      | activity   | name               | course | idnumber     | section |
      | wiki       | Wiki activity      | C1     | subsection1  | 1       |
      | subsection | Subsection 1       | C1     | subsection1  | 1       |
      | page       | Page in Subsection | C1     | page1        | 2       |

  Scenario: Subsection does not appear in the site default completion form
    Given I log in as "admin"
    When I navigate to "Courses > Default settings > Default activity completion" in site administration
    And I should see "Default activity completion"
    Then I should see "Forum" in the "region-main" "region"
    And I should not see "Subsection" in the "region-main" "region"

  @javascript
  Scenario: Subsection does not appear in the course default completion form
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Default activity completion"
    And I should see "Default activity completion"
    Then I should see "Forum" in the "region-main" "region"
    And I should not see "Subsection" in the "region-main" "region"

  @javascript
  Scenario: Subsection completion should not be editable in the completion bulk edit
    Given I am on the "C1" "Course" page logged in as "teacher1"
    When I navigate to "Course completion" in current page administration
    And I set the field "Course completion tertiary navigation" to "Bulk edit activity completion"
    And I should see "Bulk edit activity completion"
    Then I should see "Wiki activity" in the "region-main" "region"
    Then I should see "Page in Subsection" in the "region-main" "region"
    # It appears as a subsection but not as an editable element.
    Then I should see "Subsection 1" in the "region-main" "region"
    And "Subsection 1" "link" in the "region-main" "region" should not be visible
