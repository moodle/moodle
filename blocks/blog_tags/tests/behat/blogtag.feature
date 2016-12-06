@block @block_blog_tag @core_blog @core_tag
Feature: Adding blog tag block
  In order to search blog post by tag
  As a user
  I need to be able to use block blog tag

  @javascript
  Scenario: Adding block blog tag to the course
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | teacher1 | Teacher | 1 | teacher1@example.com |
      | student1 | Student | 1 | student1@example.com |
    And the following "courses" exist:
      | fullname  | shortname |
      | Course 1  | c1        |
    And the following "tags" exist:
      | name         | isstandard  |
      | Neverusedtag | 1           |
    And the following "course enrolments" exist:
      | user     | course | role           |
      | teacher1 | c1     | editingteacher |
      | student1 | c1     | student        |
    When I log in as "teacher1"
    And I follow "Course 1"
    And I turn editing mode on
    And I add the "Blog tags" block
    # TODO MDL-57120 site "Blogs" link not accessible without navigation block.
    And I add the "Navigation" block if not present

    And I navigate to course participants
    And I click on "Course blogs" "link" in the "Navigation" "block"
    And I follow "Blog about this Course"
    And I set the following fields to these values:
      | Entry title                                 | Blog post from teacher    |
      | Blog entry body                             | Teacher blog post content |
      | Tags                                        | Cats, dogs                 |
    And I press "Save changes"
    And I log out
    And I log in as "student1"
    And I follow "Course 1"
    And I navigate to course participants
    And I click on "Course blogs" "link" in the "Navigation" "block"
    And I follow "Blog about this Course"
    And I set the following fields to these values:
      | Entry title                                 | Blog post from student    |
      | Blog entry body                             | Student blog post content |
      | Tags                                        | dogs, mice                 |
    And I press "Save changes"
    And I follow "c1"
    Then I should see "Cats" in the "Blog tags" "block"
    And I should see "dogs" in the "Blog tags" "block"
    And I should see "mice" in the "Blog tags" "block"
    And I click on "Cats" "link" in the "Blog tags" "block"
    And I should see "Blog post from teacher"
    And I should see "Teacher blog post content"
    And I log out
