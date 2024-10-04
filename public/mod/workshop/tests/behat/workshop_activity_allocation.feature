@mod @mod_workshop
Feature: Teacher can allocate submissions randomly or manually
  In order to manage submissions
  As a teacher
  I should be able to allocate workshop submissions randomly or manually

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | student1 | Student   | One      | student1@example.com |
      | student2 | Student   | Two      | student2@example.com |
      | student3 | Student   | Three    | student3@example.com |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity | name       | course | submissiontypetext |
      | workshop | Workshop 1 | C1     | 2                  |
    And I am on the "Course 1" course page logged in as teacher1
    And I edit assessment form in workshop "Workshop 1" as:
      | id_description__idx_0_editor | Aspect 1 |
      | id_description__idx_1_editor | Aspect 2 |
      | id_description__idx_2_editor | Aspect 3 |
    And I change phase in workshop "Workshop 1" to "Submission phase"
    And I am on the "Workshop 1" "workshop activity" page logged in as student1
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Submitted by s1    |
      | Submission content | Some content by s1 |
    And I am on the "Workshop 1" "workshop activity" page logged in as student2
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Submitted by s2    |
      | Submission content | Some content by s2 |
    And I am on the "Workshop 1" "workshop activity" page logged in as student3
    And I add a submission in workshop "Workshop 1" as:
      | Title              | Submitted by s3    |
      | Submission content | Some content by s3 |

  Scenario: Teacher can allocate workshop submissions randomly
    Given I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    And I click on "Submissions allocation" "link"
    # Allocate submissions randomly.
    When I select "Random allocation" from the "jump" singleselect
    And I set the following fields to these values:
      | numofreviews | 3 |
    And I press "Save changes"
    # Confirm that random allocations was successful.
    Then I should see "Trying to allocate 3 review(s) per author"
    And I should see "Randomly assigning 6 allocations"
    And I should see "New assessment to be done: Student Three is reviewer of Student One"
    And I should see "New assessment to be done: Student Two is reviewer of Student One"
    And I should see "New assessment to be done: Student Two is reviewer of Student Three"
    And I should see "New assessment to be done: Student One is reviewer of Student Three"
    And I should see "New assessment to be done: Student One is reviewer of Student Two"
    And I should see "New assessment to be done: Student Three is reviewer of Student Two"

  Scenario: Teacher can allocate workshop submissions manually
    # Allocate submissions manually.
    Given I am on the "Workshop 1" "workshop activity" page logged in as teacher1
    When I allocate submissions in workshop "Workshop 1" as:
      | Participant | Reviewer    |
      | Student Two | Student One |
    # Remove submission allocation.
    And I click on "//li[contains(text(), 'Student One')]/a[contains(@class, 'action-icon')]" "xpath_element"
    Then I should see "Are you sure you want to deallocate the selected assessment?"
    And I press "Yes, I am sure"
    # Confirm submission allocation was removed.
    And I should see "Assessment deallocated"
