@mod @mod_forum
Feature: Forum discussions can be split
  In order to manage forum discussions in my course
  As a Teacher
  I need to be able to split threads to keep them on topic.

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
      | student2 | Student | 2 | student2@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Science 101 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
      | student1 | C1 | student |
      | student2 | C1 | student |
    And the following "activities" exist:
      | activity   | name                   | course | idnumber | type    |
      | forum      | Study discussions      | C1     | forump1  | general |
    And the following "mod_forum > discussions" exist:
      | forum   | course | user     | name                      | message                                                                  |
      | forump1 | C1     | teacher1 | Photosynthesis discussion | Lets discuss our learning about Photosynthesis this week in this thread. |
    And the following "mod_forum > posts" exist:
      | user     | parentsubject             | subject     | message                                                                   |
      | student1 | Photosynthesis discussion | Mass number | Can anyone tell me which number is the mass number in the periodic table? |
      | student2 | Mass number               |             | I would also like to know this                                            |

  Scenario: Split a forum discussion
    Given I am on the "Study discussions" "forum activity" page logged in as teacher1
    And I follow "Photosynthesis discussion"
    When I follow "Split"
    And  I set the following fields to these values:
        | Discussion name | Mass number in periodic table |
    And I press "Split"
    Then I should see "Mass number in periodic table"
    And I follow "Study discussions"
    And I should see "Teacher 1" in the "Photosynthesis" "table_row"
    # Confirm that the last post author has been updated.
    And I should not see "Student 2" in the "Photosynthesis" "table_row"
    # Confirm that the current author has been shown for the new split discussion.
    And I should see "Student 1" in the "Mass number in periodic table" "table_row"
