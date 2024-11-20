@enrol @enrol_lti
Feature: Publish activities and resources over LTI Advantage
  In order to make content available to external platforms
  As a teacher
  I need to be able to publish and manage activities and resources using LTI Advantage

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | format |
      | Course 1 | C1 | topics |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And the following "activities" exist:
      | activity | name                 | intro                    | course | idnumber  | section |
      | assign   | Test assignment name | Submit your online text  | C1     | assign1   | 1       |
    And I enable "lti" "enrol" plugin

  Scenario: A teacher can publish an activity
    Given I log in as "teacher1"
    And I am on the "Course 1" "enrolment methods" page
    When I select "Publish as LTI tool" from the "Add method" singleselect
    And the following fields match these values:
    | LTI version | LTI Advantage |
    And I set the following fields to these values:
    | Custom instance name | Published assignment |
    | Tool to be published | Test assignment name |
    And I press "Add method"
    And I am on "Course 1" course homepage
    And I navigate to "Published as LTI tools" in current page administration
    Then I should see "Launch URL" in the "Published assignment" "table_row"
    And I should see "Custom properties" in the "Published assignment" "table_row"
    And "Edit" "link" should exist in the "Published assignment" "table_row"
    And "Delete" "link" should exist in the "Published assignment" "table_row"
    And "Disable" "link" should exist in the "Published assignment" "table_row"

  Scenario: A teacher can edit a published resource/activity
    Given the following "enrol_lti > published resources" exist:
      | name             | uuid        | activity | course |
      | Published assignment | my-uuid-123 | assign1  | C1     |
    And I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "Published as LTI tools" in current page administration
    And the "value" attribute of "Custom properties" "field" should contain "id=my-uuid-123"
    When I click on "Edit" "link" in the "Published assignment" "table_row"
    And I set the following fields to these values:
      | Custom instance name | New instance name |
    And I press "Cancel"
    And I should see "Published assignment" in the "Published assignment" "table_row"
    And the "value" attribute of "Custom properties" "field" should contain "id=my-uuid-123"
    And I click on "Edit" "link" in the "Published assignment" "table_row"
    And I set the following fields to these values:
      | Custom instance name | New instance name |
    And I press "Save changes"
    Then I should see "New instance name"
    And the "value" attribute of "Custom properties" "field" should contain "id=my-uuid-123"

  Scenario: A teacher can disable and enable a published resource/activity
    Given the following "enrol_lti > published resources" exist:
      | name             | activity | course |
      | Published assignment | assign1  | C1     |
    And I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "Published as LTI tools" in current page administration
    When I click on "Disable" "link" in the "Published assignment" "table_row"
    Then ".dimmed_text" "css_element" should exist in the "Published assignment" "table_row"
    And I click on "Enable" "link" in the "Published assignment" "table_row"
    And ".dimmed_text" "css_element" should not exist in the "Published assignment" "table_row"

  Scenario: A teacher can delete a published tool
    Given the following "enrol_lti > published resources" exist:
      | name                 | activity | course |
      | Published assignment | assign1  | C1     |
    And I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "Published as LTI tools" in current page administration
    When I click on "Delete" "link" in the "Published assignment" "table_row"
    And I press "Cancel"
    And I should see "Published assignment" in the "Published assignment" "table_row"
    And I click on "Delete" "link" in the "Published assignment" "table_row"
    And I press "Continue"
    And I should see "No resources or activities are published yet"
    And I should not see "Published assignment"

  Scenario: A teacher can switch the version of a published resource from LTI 1.1 to LTI Advantage
    Given the following "enrol_lti > published resources" exist:
      | name             | activity | course | ltiversion      |
      | Published assignment | assign1  | C1     | LTI-1p0/LTI-2p0 |
    And I am on the "Course 1" "Course" page logged in as "teacher1"
    And I navigate to "Published as LTI tools" in current page administration
    And I should see "No resources or activities are published yet"
    And I click on "Legacy LTI (1.1/2.0)" "link"
    And I should see "Published assignment"
    When I click on "Edit" "link" in the "Published assignment" "table_row"
    And the following fields match these values:
      | LTI version | Legacy LTI (1.1/2.0) |
    And I set the following fields to these values:
      | LTI version | LTI Advantage |
      | Custom instance name | New instance name |
    And I press "Save changes"
    And I click on "LTI Advantage" "link"
    Then I should see "New instance name"
    And "LTI Advantage" "link" should not exist
    And "Legacy LTI (1.1/2.0)" "link" should exist
    And the "value" attribute of "Custom properties" "field" should contain "id="
