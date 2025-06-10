@plugin @plagiarism @plagiarism_turnitin @plagiarism_turnitin_assignment @plagiarism_turnitin_assignment_drafts
Feature: Plagiarism plugin works with a Moodle Assignment utilising the draft submission feature
  In order to allow students to send draft assignment submissions to Turnitin
  As a user
  I need to create an assignment with the plugin enabled and the assignment to launch successfully.

  Background: Set up the plugin
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Course 1 | C1        | 0        | 0         |
    And the following users will be created if they do not already exist:
      | username    | firstname   | lastname    | email                                   |
      | instructor1 | instructor1 | instructor1 | instructor1_$account_tiibehattesting@example.com |
      | student1    | student1    | student1    | student1_$account_tiibehattesting@example.com    |
      | student2    | student2    | student2    | student2_$account_tiibehattesting@example.com    |
    And the following "course enrolments" exist:
      | user        | course | role    |
      | student1    | C1     | student |
      | student2    | C1     | student        |
      | instructor1 | C1     | editingteacher |
    And I log in as "admin"
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
    Then the following should exist in the "plugins-control-panel" table:
      | Plugin name         |
      | plagiarism_turnitin |
    # Create Assignment.
    And I add an "assign" activity to course "Course 1" section "1" and I fill the form with:
      | Assignment name                   | Test assignment name |
      | use_turnitin                      | 1                    |
      | plagiarism_compare_student_papers | 1                    |
      | submissiondrafts                  | 1                    |
      | plagiarism_draft_submit           | 1                    |
    And I follow "Test assignment name"
    Then I should see "Grading summary"

  @javascript @_file_upload
  Scenario: A student can submit a draft and it is not sent to Turnitin until it is submitted.
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
    Then I should see "Not graded"
    And I should see "Draft (not submitted)"
    And I log out
    # Admin runs scheduled task to send submission to Turnitin.
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    # Instructor opens assignment.
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should not contain "Queued"
    # Student finalises submission.
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    And I press "Submit assignment"
    And I press "Continue"
    Then I should see "Queued"
    # Admin runs scheduled task to send submission to Turnitin.
    And I log out
    And I log in as "admin"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "Queued"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    # Check that the Turnitin ID is there.
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "View all submissions"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "Turnitin ID:"
    # Trigger cron as admin for report
    And I log out
    And I log in as "admin"
    And I obtain an originality report for "student1 student1" on "assignment" "Test assignment name" on course "Course 1"
    # Instructor opens viewer
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "%"
    And I wait until "[alt='GradeMark']" "css_element" exists
    And I click on "[alt='GradeMark']" "css_element"
    And I switch to "turnitin_viewer" window
    And I wait until the page is ready
    And I accept the Turnitin EULA from the EV if necessary
    And I wait until the page is ready
    Then I should see "testfile.txt"

  @javascript @_file_upload
  Scenario: A student can submit a draft and it is sent to Turnitin.
    Given I navigate to "Edit settings" in current page administration
    And I set the following fields to these values:
      | plagiarism_draft_submit | 0 |
    And I press "Save and display"
    Given I log out
    # Student accepts eula.
    And I log in as "student2"
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
    Then I should see "Not graded"
    And I should see "Draft (not submitted)"
    And I log out
    # Admin runs scheduled task to send submission to Turnitin.
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    # Instructor opens assignment and checks that the Turnitin ID is there.
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student2 student2" row "File submissions" column of "generaltable" table should contain "Turnitin ID:"
    # Trigger cron as admin for report
    And I log out
    And I log in as "admin"
    And I obtain an originality report for "student2 student2" on "assignment" "Test assignment name" on course "Course 1"
    # Instructor opens viewer
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    When I navigate to "View all submissions" in current page administration
    Then "student2 student2" row "File submissions" column of "generaltable" table should contain "%"
    And I wait until "[alt='GradeMark']" "css_element" exists
    And I click on "[alt='GradeMark']" "css_element"
    And I switch to "turnitin_viewer" window
    And I wait until the page is ready
    And I accept the Turnitin EULA from the EV if necessary
    And I wait until the page is ready
    And I wait "20" seconds
    Then I should see "testfile.txt"