@plugin @plagiarism @plagiarism_turnitin @plagiarism_turnitin_smoke @plagiarism_turnitin_eula
Feature: Plagiarism plugin works with a Moodle Assignment allowing EULA acceptance
  In order to allow students to submit to Moodle, they must accept the EULA.
  As a user
  I need to create an assignment with the plugin enabled and the assignment to launch successfully.

  Background: Set up the plugin
    Given the following "courses" exist:
      | fullname | shortname | category | groupmode |
      | Turnitin Behat EULA Test Course | C1        | 0        | 0         |
    And the following users will be created if they do not already exist:
      | username    | firstname   | lastname    | email                                   |
      | instructor1 | instructor1 | instructor1 | instructor1_$account_tiibehattesting@example.com |
    And I create a unique user with username "student1"
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
    And I add an "assign" activity to course "Turnitin Behat EULA Test Course" section "1" and I fill the form with:
      | Assignment name                   | Test assignment name |
      | use_turnitin                      | 1                    |
      | plagiarism_compare_student_papers | 1                    |
    Then I should see "Test assignment name"

  @javascript @_file_upload
  Scenario: Student can still submit to Moodle even if declining the EULA. The student can then accept the EULA and the admin can resubmit the file.
    Given I log out
    # Student declines the EULA and submits.
    And I log in as "student1"
    And I am on "Turnitin Behat EULA Test Course" course homepage
    And I follow "Test assignment name"
    And I press "Add submission"
    Then I should see "To submit a file to Turnitin you must first accept our EULA. Choosing to not accept our EULA will submit your file to Moodle only. Click here to accept."
    And I click on ".pp_turnitin_eula_link" "css_element"
    And I wait until ".iframe-ltilaunch-eula" "css_element" exists
    And I switch to iframe with locator ".iframe-ltilaunch-eula"
    And I wait until the page is ready
    And I click on ".disagree-button" "css_element"
    And I wait "10" seconds
    And I wait until the page is ready
    And I upload "plagiarism/turnitin/tests/fixtures/testfile.txt" file to "File submissions" filemanager
    And I wait "10" seconds
    And I click save changes button "css_element" "#id_submitbutton"
    Then I should see "Submitted for grading"
    And I should see "Your file has not been submitted to Turnitin. Please click here to accept our EULA."
    # Trigger cron as admin for submission
    And I log out
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    # Instructor opens assignment.
    And I log out
    And I log in as "instructor1"
    And I am on "Turnitin Behat EULA Test Course" course homepage
    And I follow "Test assignment name"
    Then I should see "View all submissions"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should not contain "Turnitin ID:"
    Given I log out
    # Student accepts the EULA.
    And I log in as "student1"
    And I am on "Turnitin Behat EULA Test Course" course homepage
    And I follow "Test assignment name"
    And I should see "Your file has not been submitted to Turnitin. Please click here to accept our EULA."
    And I accept the Turnitin EULA if necessary
    And I press "Edit submission"
    # Resubmitting same paper
    And I delete "testfile.txt" from "File submissions" filemanager
    And I upload "plagiarism/turnitin/tests/fixtures/testfile.txt" file to "File submissions" filemanager
    And I press "Save changes"
    Then I should see "Submitted for grading"
    And I should see "Queued"
    # Admin can trigger a resubmission
    And I log out
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    # Instructor opens assignment.
    And I log out
    And I log in as "instructor1"
    And I am on "Turnitin Behat EULA Test Course" course homepage
    And I follow "Test assignment name"
    Then I should see "View all submissions"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "Turnitin ID:"
    # Trigger cron as admin for report
    And I log out
    And I log in as "admin"
    And I obtain an originality report for "student1 student1" on "assignment" "Test assignment name" on course "Turnitin Behat EULA Test Course"
    # Instructor opens viewer
    And I log out
    And I log in as "instructor1"
    And I am on "Turnitin Behat EULA Test Course" course homepage
    And I follow "Test assignment name"
    Then I should see "View all submissions"
    When I navigate to "View all submissions" in current page administration
    Then "student1 student1" row "File submissions" column of "generaltable" table should contain "%"
    And I wait "30" seconds
    And I wait until "[alt='GradeMark']" "css_element" exists
    And I click on "[alt='GradeMark']" "css_element"
    And I switch to "turnitin_viewer" window
