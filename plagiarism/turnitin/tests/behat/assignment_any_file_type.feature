@plugin @plagiarism @plagiarism_turnitin @plagiarism_turnitin_sanity @plagiarism_turnitin_assignment @plagiarism_turnitin_assignment_any_file_type
Feature: Plagiarism plugin works with a Moodle Assignment for a filetype which won't generate an originality report
  In order to allow students to send assignment submissions to Turnitin
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
    And the following "course enrolments" exist:
      | user        | course | role    |
      | student1    | C1     | student |
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
      | Assignment name                     | Test assignment name |
      | use_turnitin                        | 1                    |
      | plagiarism_compare_student_papers   | 1                    |
      | plagiarism_allow_non_or_submissions | 1                    |
    Then I should see "Test assignment name"

  @javascript @_file_upload
  Scenario: Student accepts eula, submits a non-OR file and instructor opens the viewer
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
    And I upload "plagiarism/turnitin/tests/fixtures/carpet.jpg" file to "File submissions" filemanager
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "Queued"
    # Trigger cron as admin for submission
    And I log out
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    # Instructor opens assignment.
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "View all submissions"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "Turnitin ID:"
    # Trigger cron as admin for report
    And I log out
    And I log in as "admin"
    And I run the scheduled task "\plagiarism_turnitin\task\update_reports"
    # Instructor opens viewer
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test assignment name"
    Then I should see "View all submissions"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should not contain "%"
    And I wait until "[alt='GradeMark']" "css_element" exists
    And I click on "[alt='GradeMark']" "css_element"
    And I switch to "turnitin_viewer" window
    And I wait until the page is ready
    And I accept the Turnitin EULA from the EV if necessary
    And I wait until the page is ready
    Then I should see "carpet.jpg"
