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
    And the following "core_badges > Badge" exists:
      | name           | Testing system badge             |
      | status         | inactive                         |
      | version        | 1.1                              |
      | language       | ca                               |
      | description    | Testing system badge description |
      | image          | badges/tests/behat/badge.png     |
      | imageauthorurl | http://author.example.com        |
      | imagecaption   | My caption image                 |
    And the following "core_badges > Criterias" exist:
      | badge                | role           |
      | Testing system badge | editingteacher |
    And I log in as "admin"
    And I navigate to "Badges > Manage badges" in site administration

  Scenario: Display badge without expired date
    # Enable the badge.
    Given I press "Enable access" action in the "Testing system badge" report row
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Award badge to student1.
    When I press "Award badge" action in the "Testing system badge" report row
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Check badge details are displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    And I press "View issued badge" action in the "Student 1" report row
    Then I should see "Awarded to Student 1"
    And I should see "This badge has to be awarded by a user with the following role:"
    And I should not see "Expired"
    And I should not see "Expires"
    And I follow "More details"
    And I should see "Catalan"
    And I should see "1.1"

  Scenario: Display badge with ALL criteria
    # Add another criterion and enable the badge.
    Given I follow "Testing system badge"
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Profile completion"
    And I set the field "id_field_firstname" to "1"
    And I press "Save"
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Award badge to student1.
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Check badge details are displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    When I press "View issued badge" action in the "Student 1" report row
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
    Given I follow "Testing system badge"
    And I select "Criteria" from the "jump" singleselect
    And I set the field "type" to "Profile completion"
    And I set the field "id_field_firstname" to "1"
    And I press "Save"
    And I set the field "update" to "2"
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Check badge details are displayed.
    And I select "Recipients (2)" from the "jump" singleselect
    When I press "View issued badge" action in the "Student 1" report row
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
    Given I press "Edit" action in the "Testing system badge" report row
    And I expand all fieldsets
    When I click on "Relative date" "radio"
    And I set the field "expireperiod[number]" to "1"
    And I press "Save changes"
    And I should see "Changes saved"
    And I select "Overview" from the "jump" singleselect
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
    # Award badge to student1.
    And I select "Recipients (0)" from the "jump" singleselect
    And I press "Award badge"
    And I set the field "potentialrecipients[]" to "Student 1 (student1@example.com)"
    And I press "Award badge"
    # Check "Expires" date is displayed.
    And I navigate to "Badges > Manage badges" in site administration
    And I follow "Testing system badge"
    And I select "Recipients (1)" from the "jump" singleselect
    And I press "View issued badge" action in the "Student 1" report row
    Then I should see "Expires"
    And I should not see "Expired"

  Scenario: Display expired badge
    # Set expired date to badge (relative date 1 seconds after the date of issue it).
    Given I press "Edit" action in the "Testing system badge" report row
    And I expand all fieldsets
    When I click on "Relative date" "radio"
    And I set the field "expireperiod[timeunit]" to "1"
    And I set the field "expireperiod[number]" to "1"
    And I press "Save changes"
    And I should see "Changes saved"
    And I select "Overview" from the "jump" singleselect
    And I press "Enable access"
    And I click on "Enable" "button" in the "Confirm" "dialogue"
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
    And I press "View issued badge" action in the "Student 1" report row
    Then I should see "Expired"
    And I should not see "Expires"
