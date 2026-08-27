@core @core_course @course_dndupload
Feature: Provide image details when dropping an image onto a course page
  In order to make images added directly to a course page accessible
  As a teacher
  I need to set alternative text and display size before the image is added

  Background:
    Given the following "courses" exist:
      | fullname | shortname | format | numsections |
      | Course 1 | C1        | topics | 3           |
    And I am on the "C1" "Course" page logged in as "admin"
    And I turn editing mode on

  @javascript
  Scenario: Saving the image details creates the media activity with the chosen alternative text
    When I drop the image "beach.png" sized "800"x"600" onto course section "1"
    And I click on "Add media to course page" "radio"
    And I click on "Upload" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Image details" in the "Image details" "dialogue"
    And I set the field "How would you describe this image to someone who cannot see it?" to "A quiet beach at sunset"
    And I click on "Save" "button" in the "Image details" "dialogue"
    Then "//img[contains(@alt, 'A quiet beach at sunset')]" "xpath_element" should exist

  @javascript
  Scenario: The image details modal requires alternative text unless the image is decorative
    When I drop the image "photo.png" sized "800"x"600" onto course section "1"
    And I click on "Add media to course page" "radio"
    And I click on "Upload" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Save" "button" in the "Image details" "dialogue"
    Then I should see "An image must have a description, unless it is marked as decorative only."
    And "//textarea[@data-region='alt' and @aria-invalid='true']" "xpath_element" should exist

  @javascript
  Scenario: A decorative image can be saved without alternative text and is not wrapped in a link
    When I drop the image "decorative.png" sized "800"x"600" onto course section "1"
    And I click on "Add media to course page" "radio"
    And I click on "Upload" "button" in the "Add an activity or resource" "dialogue"
    And I click on "Decorative image" "checkbox" in the "Image details" "dialogue"
    And I click on "Save" "button" in the "Image details" "dialogue"
    Then "[data-for='section'][data-number='1'] img" "css_element" should exist
    And "[data-for='section'][data-number='1'] a img" "css_element" should not exist

  @javascript
  Scenario: Cancelling the image details modal creates no activity
    When I drop the image "cancelled.png" sized "800"x"600" onto course section "1"
    And I click on "Add media to course page" "radio"
    And I click on "Upload" "button" in the "Add an activity or resource" "dialogue"
    And I should see "Image details" in the "Image details" "dialogue"
    And I click on "Cancel" "button" in the "Image details" "dialogue"
    Then "[data-for='section'][data-number='1'] img" "css_element" should not exist
