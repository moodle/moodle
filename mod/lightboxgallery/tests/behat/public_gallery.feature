@mod @mod_lightboxgallery
Feature: Set a lightboxgallery as public
  In order to let non-loggedin users view a gallery
  As a teacher
  I need to add a lightboxgallery with the ispublic flag enabled

  Scenario: Add a lightboxgallery and a standard gallery
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category |
      | Course 1 | C1 | 0 |
    And the following "course enrolments" exist:
      | user | course | role |
      | teacher1 | C1 | editingteacher |
    And I log in as "teacher1"
    And I am on "Course 1" course homepage with editing mode on
    And I add a "Lightbox Gallery" to section "1" and I fill the form with:
      | Name        | LBG          |
      | Description | Test gallery |
      | ID number   | lbg1         |
      | Make public | No           |
    And I follow "LBG"
    Then I should see "Test gallery"
    When I log out
    And I view the lightboxgallery with idnumber "lbg1"
    Then I should not see "Test gallery"
    When I log in as "teacher1"
    And I view the lightboxgallery with idnumber "lbg1"
    And I follow "Edit settings"
    And I set the field "Make public" to "Yes"
    And I press "Save and display"
    And I log out
    And I view the lightboxgallery with idnumber "lbg1"
    Then I should see "Test gallery"
