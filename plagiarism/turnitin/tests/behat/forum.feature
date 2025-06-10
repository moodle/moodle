@plugin @plagiarism @plagiarism_turnitin @plagiarism_turnitin_smoke @plagiarism_turnitin_forum
Feature: Plagiarism plugin works with a Moodle forum
  In order to allow students to send forum posts to Turnitin
  As a user
  I need to create a forum and discussion with the plugin enabled.

  Background: Set up the users, course, forum and discussion with plugin enabled
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
      | Enable Turnitin for Forum  | 1 |
    And I configure Turnitin URL
    And I configure Turnitin credentials
    And I set the following fields to these values:
      | Enable Diagnostic Mode | Standard |
    And I press "Save changes"
    Then the following should exist in the "plugins-control-panel" table:
      | Plugin name         |
      | plagiarism_turnitin |
    # Create Forum.
    And I add a "forum" activity to course "Course 1" section "1" and I fill the form with:
      | Forum name                        | Test forum name                |
      | Forum type                        | Standard forum for general use |
      | Description                       | Test forum description         |
      | groupmode                         | 0                              |
      | use_turnitin                      | 1                              |
      | plagiarism_compare_student_papers | 1                              |
      | plagiarism_show_student_report    | 1                              |
    And I add a new discussion to "Test forum name" forum with:
      | Subject | Forum post 1 |
      | Message | This is the body of the forum post that will be submitted to Turnitin. It will be sent to Turnitin for Originality Checking |
    And I wait until the page is ready
    Then I should see "Test forum name"
    And I log out

  @javascript @_file_upload
  Scenario: Add a post to a discussion with a file attached and retrieve the originality score
    # Student creates a forum discussion and replies to original post.
    Given I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I accept the Turnitin EULA if necessary
    And I add a new discussion to "Test forum name" forum with:
       | Subject | Forum post 2 |
       | Message | This is the body of the forum post that will be submitted to Turnitin. It will be sent to Turnitin for Originality Checking |
    And I reply "Forum post 1" post from "Test forum name" forum with:
      | Subject    | Reply with attachment                                                                                                          |
      | Message    | This is the body of the forum reply that will be submitted to Turnitin. It will be sent to Turnitin for Originality Checking   |
      | Attachment | plagiarism/turnitin/tests/fixtures/testfile.txt                                                                                |
    Then I should see "Reply with attachment"
    And I should see "testfile.txt"
    And I should see "Queued" in the "div.turnitin_status" "css_element"
    And I log out
    # Trigger cron as admin for forum and check results.
    And I log in as "admin"
    And I run the scheduled task "plagiarism_turnitin\task\send_submissions"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Forum post 1"
    Then I should see "Turnitin ID:" in the "div.turnitin_status" "css_element"
    And I log out
    # Student can see post has been sent to Turnitin.
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Forum post 1"
    Then I should see "Turnitin ID:" in the "div.turnitin_status" "css_element"
    # Trigger cron as admin for report
    And I log out
    And I log in as "admin"
    And I obtain an originality report for "student1 student1" on "forum" "Test forum name" on course "Course 1"
    # Login as student and a score should be visible.
    And I log out
    And I log in as "student1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Forum post 1"
    Then I should see "%" in the "div.origreport_score" "css_element"
    # Instructor opens viewer
    And I log out
    And I log in as "instructor1"
    And I am on "Course 1" course homepage
    And I follow "Test forum name"
    And I follow "Forum post 1"
    And I wait until "div.pp_origreport_open" "css_element" exists
    And I click on "div.pp_origreport_open" "css_element"
    And I switch to "turnitin_viewer" window
    And I wait until the page is ready
    And I accept the Turnitin EULA from the EV if necessary
    And I wait until the page is ready
    Then I should see "forumpost_"