@core @core_badges @_file_upload @javascript
Feature: Display badges
  In order to access to badges information
  As a user
  I need to view badges data awarded to users

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | student1 | Student | 1 | student1@example.com |
    # Create system badge and define a criterion.
    And I log in as "admin"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Testing system badge |
      | Version | 1.1 |
      | Language | Catalan |
      | Description | Testing system badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I set the field "type" to "Manual issue by role"
    And I expand all fieldsets
    And I set the field "Teacher" to "1"
    And I press "Save"

  Scenario: Display badge without expired date
    # Enable the badge.
    Given I press "Enable access"
    And I press "Continue"
    # Award badge to student1.
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Check badge details are displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    When I click on "View issued badge" "link" in the "Student 1" "table_row"
    Then I should see "Awarded to Student 1"
    And I should see "This badge has to be awarded by a user with the following role:"
    And I should not see "Expired"
    And I should not see "Expires"
    And I follow "More details"
    And I should see "Catalan"
    And I should see "1.1"

  Scenario: Display badge with ALL criteria
    # Add another criterion and enable the badge.
    Given I set the field "type" to "Profile completion"
    And I set the field "id_field_firstname" to "1"
    And I press "Save"
    And I press "Enable access"
    And I press "Continue"
    # Award badge to student1.
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Check badge details are displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    When I click on "View issued badge" "link" in the "Student 1" "table_row"
    Then I should see "Awarded to Student 1"
    And I should see "Complete ALL of the listed requirements."
    And I should see "This badge has to be awarded by a user with the following role:"
    And I should see "The following user profile field has to be completed:"
    And I should not see "Expired"
    And I should not see "Expires"
    And I follow "More details"
    And I should see "Catalan"
    And I should see "1.1"

  Scenario: Display badge with ANY criteria
    # Add another criterion and enable the badge.
    Given I set the field "type" to "Profile completion"
    And I set the field "id_field_firstname" to "1"
    And I press "Save"
    And I set the field "update" to "2"
    And I press "Enable access"
    And I press "Continue"
    # Check badge details are displayed.
    And I select "Recipients (2)" from the "jump" singleselect
    When I click on "View issued badge" "link" in the "Student 1" "table_row"
    Then I should see "Awarded to Student 1"
    And I should see "Complete ANY of the listed requirements."
    And I should see "This badge has to be awarded by a user with the following role:"
    And I should see "The following user profile field has to be completed:"
    And I should not see "Expired"
    And I should not see "Expires"
    And I follow "More details"
    And I should see "Catalan"
    And I should see "1.1"

  Scenario: Display badge with expiration date but not expired yet
    # Set expired date to badge (future date).
    Given I select "Edit details" from the "jump" singleselect
    When I click on "Relative date" "radio"
    And I set the field "expireperiod[number]" to "1"
    And I press "Save changes"
    And I press "Enable access"
    And I press "Continue"
    # Award badge to student1.
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Check "Expires" date is displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    And I click on "View issued badge" "link" in the "Student 1" "table_row"
    Then I should see "Expires"
    And I should not see "Expired"

  Scenario: Display expired badge
    # Set expired date to badge (relative date 1 seconds after the date of issue it).
    Given I select "Edit details" from the "jump" singleselect
    When I click on "Relative date" "radio"
    And I set the field "expireperiod[timeunit]" to "1"
    And I set the field "expireperiod[number]" to "1"
    And I press "Save changes"
    And I press "Enable access"
    And I press "Continue"
    # Award badge to student1.
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Wait 1 second to guarantee the badge is expired.
    And I wait "1" seconds
    # Check "Expired" date is displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    And I click on "View issued badge" "link" in the "Student 1" "table_row"
    Then I should see "Expired"
    And I should not see "Expires"
