@core @_file_upload
Feature: Profile picture access
  In order to enable precise security control and meet legal requirements
  As site administrators
  We should be able to prevent certain users from viewing profile pictures

  Background:
    Given the following "users" exist:
      | username | firstname | lastname      |
      | student1 | Alice     | in Wonderland |
      | student2 | Bob       | a Job Week    |
    And the following "courses" exist:
      | shortname |
      | C1        |
    And the following "course enrolments" exist:
      | user     | course | role    |
      | student1 | C1     | student |
      | student2 | C1     | student |
    And the following "activity" exists:
      | course      | C1        |
      | activity    | forum     |
      | name        | TestForum |
      | idnumber    | forum1    |
    And the following "mod_forum > discussions" exist:
      | user     | forum  | name  | message                 | timemodified      |
      | student1 | forum1 | Post1 | This is the first post  | ##now -1 second## |
    And the following "roles" exist:
      | shortname |
      | dangerous |
    And the following "role capability" exists:
      | role                            | dangerous  |
      | moodle/user:viewprofilepictures | prohibit   |
    And I am on the "Profile editing" page logged in as "student1"
    And I upload "/course/tests/fixtures/image.jpg" file to "New picture" filemanager
    And I set the field "Picture description" to "MyPic"
    And I press "Update profile"

  @javascript
  Scenario: Users can view pictures on forum page when permitted
    When I am on the "forum1" "forum activity" page logged in as "student2"
    # By default you can see user pics.
    And ".discussion-list img.userpicture[src*='user/icon']" "css_element" should be visible
    # Even if you don't have the capability, you can still see them...
    And the following "system role assigns" exist:
      | user     | role      | contextlevel |
      | student2 | dangerous | System       |
    And I reload the page
    And ".discussion-list img.userpicture[src*='user/icon']" "css_element" should be visible
    # ...unless forcelogin is on, when the system kicks in and hides it.
    And the following config values are set as admin:
      | forcelogin | 1 |
    And I reload the page
    Then ".discussion-list img.userpicture[src*='user/icon']" "css_element" should not exist

  @javascript
  Scenario: Users can view pictures on profile page when permitted
    When I am on the "forum1" "forum activity" page logged in as "student2"
    And I follow "Post1"
    And I follow "Alice in Wonderland"
    # By default you can see user pics.
    And ".page-header-image img.userpicture[src*='user/icon']" "css_element" should be visible
    # Even if you don't have the capability, you can still see them...
    And the following "system role assigns" exist:
      | user     | role      | contextlevel |
      | student2 | dangerous | System       |
    And I reload the page
    And ".page-header-image img.userpicture[src*='user/icon']" "css_element" should be visible
    # ...unless forcelogin is on, when the system kicks in and hides it.
    And the following config values are set as admin:
      | forcelogin | 1 |
    And I reload the page
    Then ".page-header-image img.userpicture[src*='user/icon']" "css_element" should not exist
