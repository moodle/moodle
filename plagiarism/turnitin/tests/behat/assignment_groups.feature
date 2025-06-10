@plugin @plagiarism @plagiarism_turnitin @plagiarism_turnitin_assignment @plagiarism_turnitin_assignment_groups
Feature: Plagiarism plugin works with a Moodle Assignment for group submissions
  In order to allow students to work collaboratively on an assignment
  As a teacher
  I need to group submissions in groups

  Background: Set up the users, course and assignment with plugin enabled
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 1         |
    And the following users will be created if they do not already exist:
      | username    | firstname   | lastname    | email                                   |
      | instructor1 | instructor1 | instructor1 | instructor1_$account_tiibehattesting@example.com |
      | student1    | student1    | student1    | student1_$account_tiibehattesting@example.com    |
      | student2    | student2    | student2    | student2_$account_tiibehattesting@example.com    |
    And the following "course enrolments" exist:
      | user        | course | role           |
      | student1    | C1     | student        |
      | student2    | C1     | student        |
      | instructor1 | C1     | editingteacher |
    And the following "groups" exist:
      | name    | course | idnumber |
      | Group 1 | C1     | G1       |
    # Enable and configure plugin.
    When I log in as "admin"
    And I navigate to "Advanced features" in site administration
    And I set the field "Enable plagiarism plugins" to "1"
    And I press "Save changes"
    And I navigate to "Plugins > Plagiarism > Turnitin plagiarism plugin" in site administration
    And I set the following fields to these values:
      | Enable Turnitin            | 1 |
      | Enable Turnitin for Assign | 1 |
    And I configure Turnitin URL
    And I configure Turnitin credentials
    And I set the following fields to these values:
      | Enable Diagnostic Mode | Standard |
    And I press "Save changes"
    # Create Assignment.
    And I add an "assign" activity to course "Course 1" section "1" and I fill the form with:
      | Assignment name                   | Test assignment name |
      | use_turnitin                      | 1                    |
      | plagiarism_show_student_report    | 1                    |
      | plagiarism_compare_student_papers | 1                    |
      | Students submit in groups         | Yes                  |
      | Group mode                        | Separate groups      |
    And I navigate to "Users > Groups" in current page administration
    And I add "student1 student1" user to "Group 1" group members
    And I add "student2 student2" user to "Group 1" group members
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I navigate to "View all submissions" in current page administration
    And I follow "Test assignment name"
    Then I should see "Grading summary"

  @javascript @_file_upload
  Scenario: Confirm that all students in a group can see the similarity report even if they didn't submit.
    Given I log out
    # Student accepts eula.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I accept the Turnitin EULA if necessary
    And I wait until the page is ready
    Then I should see "Test assignment name"
    # Student submits.
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    And I upload "plagiarism/turnitin/tests/fixtures/testfile.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "Queued"
    And I log out
    # Admin runs scheduled task to send submission to Turnitin.
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "Turnitin ID:"
    And "student2 student2" row "File submissions" column of "generaltable" table should contain "Turnitin ID:"
    # Admin runs scheduled task to request an originality report.
    And I obtain an originality report for "student1 student1" on "assignment" "Test assignment name" on course "Course 1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "%"
    And "student2 student2" row "File submissions" column of "generaltable" table should contain "%"
    And I log out
    # Check student2 can see the report.
    And I log in as "student2"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "%"