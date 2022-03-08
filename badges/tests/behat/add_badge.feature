@core @core_badges
Feature: Add badges to the system
  In order to give badges to users for their achievements
  As an admin
  I need to manage badges in the system

  Background:
    Given I am on homepage
    And I log in as "admin"

  @javascript
  Scenario: Accessing the badges
    And I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
   # TODO MDL-57120 site "Badges" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    And I click on "Site pages" "list_item" in the "Navigation" "block"
    Given I click on "Site badges" "link" in the "Navigation" "block"
    Then I should see "There are currently no badges available for users to earn."

  @javascript @_file_upload
  Scenario: Add a badge
    Given I navigate to "Badges > Badges settings" in site administration
    And I set the field "Badge issuer name" to "Test Badge Site"
    And I set the field "Badge issuer email address" to "testuser@example.com"
    And I press "Save changes"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Test badge with 'apostrophe' and other friends (<>&@#) |
      | Version | v1 |
      | Language | English |
      | Description | Test badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    When I press "Create badge"
    Then I should see "Edit details"
    And I should see "Test badge with 'apostrophe' and other friends (&@#)"
    And I should see "Endorsement"
    And I should see "Related badges (0)"
    And I should see "Alignments (0)"
    And I should not see "Create badge"
    And I should not see "Issuer details"
    And I select "Overview" from the "jump" singleselect
    And I should see "Issuer details"
    And I should see "Test Badge Site"
    And I should see "testuser@example.com"
    And I navigate to "Badges > Manage badges" in site administration
    And I should not see "There are currently no badges available for users to earn."

  @javascript @_file_upload
  Scenario: Add a badge related
    Given I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Test Badge 1 |
      | Version | v1 |
      | Language | French |
      | Description | Test badge related description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I wait until the page is ready
    And I navigate to "Badges > Manage badges" in site administration
    And I press "Add a new badge"
    And I set the following fields to these values:
      | Name | Test Badge 2 |
      | Version | v2 |
      | Language | English |
      | Description | Test badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    And I select "Related badges (0)" from the "jump" singleselect
    And I should see "This badge does not have any related badges."
    And I press "Add related badge"
    And I follow "Related badges"
    And I wait until the page is ready
    And I follow "Related badges"
    And I set the field "relatedbadgeids[]" to "Test Badge 1 (version: v1, language: French, Site badges)"
    When I press "Save changes"
    Then I should see "Related badges (1)"

  @javascript @_file_upload
  Scenario: Endorsement for Badge
    Given I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Test Badge Enrolment |
      | Version | v1 |
      | Language | English |
      | Description | Test badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    When I press "Create badge"
    Then I should see "Edit details"
    And I should see "Endorsement"
    And I select "Endorsement" from the "jump" singleselect
    And I set the following fields to these values:
      | Endorser name | Endorser |
      | Email | endorsement@example.com |
      | Issuer URL | http://example.com  |
      | Claim URL | http://claimurl.example.com |
      | Endorsement comment | Test Endorsement comment |
    And I press "Save changes"
    Then I should see "Changes saved"

  @javascript @_file_upload
  Scenario: Alignments for Badge
    Given I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Test Badge |
      | Version | v1 |
      | Language | English |
      | Description | Test badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    When I press "Create badge"
    Then I should see "Test Badge"
    And I should see "Endorsement"
    And I select "Alignments (0)" from the "jump" singleselect
    And I should see "This badge does not have any external skills or standards specified."
    And I press "Add external skill or standard"
    And I set the following fields to these values:
      | Name | Test Badge Alignments |
      | URL | https://alignments.example.com |
      | Description | Test Badge Alignments description |
    When I press "Save changes"
    And I should see "Alignments (1)"

  @javascript @_file_upload
  Scenario: Add a badge from Site badges section
    Given I turn editing mode on
    And the following config values are set as admin:
      | unaddableblocks | | theme_boost|
    # TODO MDL-57120 site "Badges" link not accessible without navigation block.
    And I add the "Navigation" block if not present
    When I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Site badges" "link" in the "Navigation" "block"
    Then I should see "Add a new badge"
    # Add a badge.
    When I press "Add a new badge"
    And I set the following fields to these values:
      | Name | Test badge with 'apostrophe' and other friends (<>&@#) 2 |
      | Version | v1 |
      | Language | English |
      | Description | Test badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    Then I should see "Edit details"
    And I should see "Test badge with 'apostrophe' and other friends (&@#) 2"
    And I should see "Endorsement"
    And I should see "Related badges (0)"
    And I should see "Alignments (0)"
    And I should not see "Create badge"
    And I navigate to "Badges > Manage badges" in site administration
    And I should not see "There are currently no badges available for users to earn."
    # See buttons from the "Site badges" page.
    And I am on homepage
    When I click on "Site pages" "list_item" in the "Navigation" "block"
    And I click on "Site badges" "link" in the "Navigation" "block"
    Then I should see "Manage badges"
    And I should see "Add a new badge"

  @javascript @_file_upload
  Scenario: Edit a badge
    Given I navigate to "Badges > Badges settings" in site administration
    And I set the field "Badge issuer name" to "Test Badge Site"
    And I set the field "Badge issuer email address" to "testuser@example.com"
    And I press "Save changes"
    And I navigate to "Badges > Add a new badge" in site administration
    And I set the following fields to these values:
      | Name | Test badge with 'apostrophe' and other friends (<>&@#) |
      | Version | firstversion |
      | Language | English |
      | Description | Test badge description |
      | Image author | http://author.example.com |
      | Image caption | Test caption image |
    And I upload "badges/tests/behat/badge.png" file to "Image" filemanager
    And I press "Create badge"
    When I select "Edit details" from the "jump" singleselect
    And I should see "Test badge with 'apostrophe' and other friends (&@#)"
    And I should not see "Issuer details"
    And I set the following fields to these values:
      | Name | Test badge renamed |
      | Version | secondversion |
    And I press "Save changes"
    And I select "Overview" from the "jump" singleselect
    Then I should not see "Test badge with 'apostrophe' and other friends (&@#)"
    And I should not see "firstversion"
    And I should see "Test badge renamed"
    And I should see "secondversion"
