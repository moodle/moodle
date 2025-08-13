@core @core_course @core_courseformat @mod_book @mod_imscp @mod_folder @mod_page @mod_url @mod_resource
Feature: Testing overview_report with resources
  In order to list all resources in a course
  As a user
  I need to be able to see the resource overview

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | student1 | Username  | 1        |
      | student2 | Username  | 2        |
      | student3 | Username  | 3        |
      | teacher1 | Teacher   | T        |
    And the following "courses" exist:
      | fullname | shortname | enablecompletion |
      | Course 1 | C1        | 1                |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | student1 | C1     | student        |
      | student2 | C1     | student        |
      | student3 | C1     | student        |
      | teacher1 | C1     | editingteacher |
    And the following "activities" exist:
      | activity   | name           | intro            | course | idnumber | completion | completionview |
      | book       | Book name 1    | Test book1       | C1     | book1    | 2          | 1              |
      | page       | Page name 1    | Test page1       | C1     | page1    | 2          | 1              |
      | url        | URL name 1     | Test url1        | C1     | url1     | 1          |                |
      | folder     | Folder name 1  | Test folder1     | C1     | folder1  |            |                |
      | label      | Label name 1   | Test label1      | C1     | label1   |            |                |
    And the following "activities" exist:
      | activity   | name        | intro        | course | defaultfilename                            | uploaded |
      | resource   | File name 1 | Test file1   | C1     | mod/resource/tests/fixtures/samplefile.txt | 1        |
    And the following "activities" exist:
      | activity | name         | intro       | course | packagefilepath                             |
      | imscp    | IMSCP name 1 | Test imscp1 | C1     | mod/imscp/tests/packages/singelscobasic.zip |

  Scenario: Teacher can see the relevant information in the resources overview
    When I am on the "Course 1" "course > activities > resource" page logged in as "teacher1"
    Then the following should exist in the "Table listing all Resource activities" table:
      | Name          | Resource type       | Actions |
      | Book name 1   | Book                | View    |
      | Page name 1   | Page                | View    |
      | URL name 1    | URL                 | View    |
      | Folder name 1 | Folder              | View    |
      | File name 1   | File                | View    |
      | IMSCP name 1  | IMS content package | View    |
    And I should not see "Status" in the "resource_overview_collapsible" "region"
    # Labels are not displayed in the resource overview report.
    And I should not see "Label name1" in the "resource_overview_collapsible" "region"

  Scenario: Students can see the relevant information in the resources overview
    When I am on the "Course 1" "course > activities > resource" page logged in as "student1"
    Then the following should exist in the "Table listing all Resource activities" table:
      | Name          | Status       | Resource type       |
      | Book name 1   | To do        | Book                |
      | Page name 1   | To do        | Page                |
      | URL name 1    | Mark as done | URL                 |
      | Folder name 1 | -            | Folder              |
      | File name 1   | -            | File                |
      | IMSCP name 1  | -            | IMS content package |
    And I should not see "Actions" in the "resource_overview_collapsible" "region"
    # Labels are not displayed in the resource overview report.
    And I should not see "Label name1" in the "resource_overview_collapsible" "region"

  Scenario: The resource overview report should generate log events
    Given I am on the "Course 1" "course > activities > resource" page logged in as "teacher1"
    When I am on the "Course 1" "course" page logged in as "teacher1"
    And I navigate to "Reports" in current page administration
    And I click on "Logs" "link"
    And I click on "Get these logs" "button"
    Then I should see "Course activities overview page viewed"
    And I should see "viewed the list of resources in the course"
