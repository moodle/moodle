@core @core_course
Feature: Display activity and resource description
  In order to display activity and resource description
  As teacher
  I should be able to enable "Display description on course page"

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email                |
      | teacher1 | Teacher   | 1        | teacher1@example.com |
    And the following "courses" exist:
      | fullname | shortname |
      | Course 1 | C1        |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | C1     | editingteacher |

  Scenario Outline: Display activity and resource descriptions
    Given I enable "chat" "mod" plugin
    And I enable "survey" "mod" plugin
    # Generate activity/resource with description
    And the following "activities" exist:
      | activity  | course | name      | intro           | showdescription |
      | <acttype> | C1     | <actname> | <actname> intro | 1               |
    When I am on the "Course 1" course page logged in as teacher1
    # Confirm that activity name and description are displayed
    Then I should see "<actname>" in the "region-main" "region"
    And I should see "<actname> intro" in the "region-main" "region"

    Examples:
      | acttype  | actname    |
      | assign   | Assign 1   |
      | book     | Book 1     |
      | chat     | Chat 1     |
      | data     | Database 1 |
      | feedback | Feedback 1 |
      | forum    | Forum 1    |
      | label    | Label 1    |
      | lti      | LTI 1      |
      | page     | Page 1     |
      | quiz     | Quiz 1     |
      | resource | Resource 1 |
      | imscp    | IMSCP 1    |
      | folder   | Folder 1   |
      | glossary | Glossary 1 |
      | scorm    | Scorm 1    |
      | lesson   | Lesson 1   |
      | survey   | Survey 1   |
      | url      | URL 1      |
      | wiki     | Wiki 1     |
      | workshop | Workshop 1 |

  Scenario: Display url activity description with pop-up display
    # Generate url activity with description and popup appearance
    Given the following "activities" exist:
      | activity | course | name  | intro       | showdescription | display | popupwidth | popupheight |
      | url      | C1     | URL 1 | URL 1 intro | 1               | 6       | 620        | 450         |
    When I am on the "Course 1" course page logged in as teacher1
    # Confirm that activity name and description are displayed
    Then I should see "URL 1" in the "region-main" "region"
    And I should see "URL 1 intro" in the "region-main" "region"

  Scenario: Display activity with image description
    # Generate page activity with image embedded in description
    Given the following "activities" exist:
      | activity | course | name   | intro                                                                             | showdescription |
      | page     | C1     | Page 1 | Page 1 intro with image: <img src="http://download.moodle.org/unittest/test.jpg"> | 1               |
    When I am on the "Course 1" course page logged in as teacher1
    # Confirm that activity name and description are displayed
    Then I should see "Page 1" in the "region-main" "region"
    And I should see "Page 1 intro with image:" in the "region-main" "region"
    # Confirm that image element exists
    And "//img[contains(@src, 'http://download.moodle.org/unittest/test.jpg')]" "xpath_element" should exist in the "region-main" "region"
