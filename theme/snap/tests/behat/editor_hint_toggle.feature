@theme @theme_snap
Feature: the editor hint toggle should be ignored by Snap in Open LMS 2.9
  In order for users that have previously used this toggle to be put back to core experience
  As a teacher
  I need to see the editor hint messages in empty course sections

  Background:
    Given the following config values are set as admin:
      | theme_snap_disableeditorhints | true |
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname | category | initsections |
      | Course 1 | C1        | 0        |      1       |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  @javascript
  Scenario: Create a URL resource in Snap theme
    Given I log in as "teacher1"
    And I am on the course main page for "C1"
    Then I should see "Welcome to your new course Teacher 1."
    Then I should see "Start by describing what your course is about using text, images, audio & video."
    And I follow "Section 1"
    Then I should see "Use this area to describe what this topic is about - with text, images, audio & video."
