@mod @mod_feedback
Feature: Feedbacks in courses with groups
  In order to collect feedbacks per group
  As an teacher
  I need to be able to filter feedback replies by groups

  Background:
    Given the following "users" exist:
      | username | firstname | lastname |
      | user1    | Username  | 1        |
      | user2    | Username  | 2        |
      | user3    | Username  | 3        |
      | user4    | Username  | 4        |
      | user5    | Username  | 5        |
      | user6    | Username  | 6        |
      | user7    | Username  | 7        |
      | teacher  | Teacher   | T        |
      | manager  | Manager   | M        |
    And the following "courses" exist:
      | fullname | shortname | groupmode |
      | Course 1 | C1        | 1 |
    And the following "course enrolments" exist:
      | user  | course | role    |
      | user1 | C1     | student |
      | user2 | C1     | student |
      | user3 | C1     | student |
      | user4 | C1     | student |
      | user5 | C1     | student |
      | user6 | C1     | student |
      | user7 | C1     | student |
      | teacher | C1   | editingteacher |
    And the following "groups" exist:
      | name | course | idnumber |
      | Group 1 | C1 | G1 |
      | Group 2 | C1 | G2 |
    And the following "group members" exist:
      | user | group |
      | user1 | G1 |
      | user2 | G1 |
      | user2 | G2 |
      | user3 | G2 |
      | user4 | G1 |
      | user5 | G1 |
      | user6 | G2 |
    And the following "system role assigns" exist:
      | user    | course               | role    |
      | manager | Acceptance test site | manager |
    And the following "activities" exist:
      | activity   | name            | course               | idnumber  | anonymous | publish_stats | groupmode | section |
      | feedback   | Site feedback   | Acceptance test site | feedback0 | 2         | 1             | 1         | 1       |
      | feedback   | Course feedback | C1                   | feedback1 | 2         | 1             | 1         | 0       |
      | feedback   | Course anon feedback | C1              | feedback2 | 1         | 1             | 1         | 0       |
    And I am on the "Site feedback" "feedback activity" page logged in as manager
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like our site?              |
      | Label                          | multichoice2                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes of course\nNot at all\nI don't know |
    And I log out

  @javascript
  Scenario: Non anonymous feedback with groups in a course
    Given I am on the "Course feedback" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like this course?           |
      | Label                          | multichoice1                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes of course\nNot at all\nI don't know |
    And I log out
    And I log in as "user1" and complete feedback "Course feedback" in course "Course 1" with:
      | Not at all | 1 |
    And I log in as "user2" and complete feedback "Course feedback" in course "Course 1" with:
      | I don't know | 1 |
    And I log in as "user3" and complete feedback "Course feedback" in course "Course 1" with:
      | Not at all | 1 |
    And I log in as "user4" and complete feedback "Course feedback" in course "Course 1" with:
      | Yes of course | 1 |
    And I log in as "user5" and complete feedback "Course feedback" in course "Course 1" with:
      | Yes of course | 1 |
    And I log in as "user6" and complete feedback "Course feedback" in course "Course 1" with:
      | Not at all | 1 |
    And I log in as "user7" and complete feedback "Course feedback" in course "Course 1" with:
      | I don't know | 1 |
    # View analysis, user1 should only see one group - group 1
    And I am on the "Course feedback" "feedback activity" page logged in as user1
    And I follow "Analysis"
    And I should see "Separate groups: Group 1"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (25.00 %)" in the "Not at all" "table_row"
    And I log out
    # View analysis, user3 should only see one group - group 2
    And I am on the "Course feedback" "feedback activity" page logged in as user3
    And I follow "Analysis"
    And I should see "Separate groups: Group 2"
    And I show chart data for the "multichoice1" feedback
    And I should see "0" in the "Yes of course" "table_row"
    And I should see "2 (66.67 %)" in the "Not at all" "table_row"
    And I log out
    # View analysis, user2 should see a group selector and be able to change the group but not view all.
    And I am on the "Course feedback" "feedback activity" page logged in as user2
    And I follow "Analysis"
    And the field "Separate groups" matches value "Group 1"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (25.00 %)" in the "Not at all" "table_row"
    And I select "Group 2" from the "Separate groups" singleselect
    And I show chart data for the "multichoice1" feedback
    And I should see "0" in the "Yes of course" "table_row"
    And I should see "2 (66.67 %)" in the "Not at all" "table_row"
    And the "Separate groups" select box should not contain "All participants"
    And I log out
    # User without group can see all participants only
    And I am on the "Course feedback" "feedback activity" page logged in as user7
    And I follow "Analysis"
    And I should see "Separate groups: All participants"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (28.57 %)" in the "Yes of course" "table_row"
    And I should see "3 (42.86 %)" in the "Not at all" "table_row"
    And I should see "2 (28.57 %)" in the "I don't know" "table_row"
    And I log out
    # Teacher can browse everybody
    And I am on the "Course feedback" "feedback activity" page logged in as teacher
    And I navigate to "Analysis" in current page administration
    And the field "Separate groups" matches value "All participants"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (28.57 %)" in the "Yes of course" "table_row"
    And I should see "3 (42.86 %)" in the "Not at all" "table_row"
    And I should see "2 (28.57 %)" in the "I don't know" "table_row"
    And I select "Group 1" from the "Separate groups" singleselect
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (25.00 %)" in the "Not at all" "table_row"
    And I select "Group 2" from the "Separate groups" singleselect
    And I show chart data for the "multichoice1" feedback
    And I should see "0" in the "Yes of course" "table_row"
    And I should see "2 (66.67 %)" in the "Not at all" "table_row"
    And I am on the "Course feedback" "feedback activity" page
    And I navigate to "Responses" in current page administration
    And the field "Separate groups" matches value "Group 2"
    And I should not see "Username 1"
    And I should see "Username 3"
    And I select "Group 1" from the "Separate groups" singleselect
    And I should see "Username 1"
    And I should not see "Username 3"
    And I select "All participants" from the "Separate groups" singleselect
    And I should see "Username 1"
    And I should see "Username 3"

  @javascript
  Scenario: Anonymous feedback with groups in a course
    Given I am on the "Course anon feedback" "feedback activity" page logged in as teacher
    And I click on "Edit questions" "link" in the "[role=main]" "css_element"
    And I add a "Multiple choice" question to the feedback with:
      | Question                       | Do you like this course?           |
      | Label                          | multichoice1                       |
      | Multiple choice type           | Multiple choice - single answer    |
      | Hide the "Not selected" option | Yes                                |
      | Multiple choice values         | Yes of course\nNot at all\nI don't know |
    And I log out
    And I log in as "user1" and complete feedback "Course anon feedback" in course "Course 1" with:
      | Not at all | 1 |
    And I am on the "Course anon feedback" "feedback activity" page logged in as user1
    And I follow "Analysis"
    And I should see "There are insufficient responses for this group"
    And I should not see "Yes of course"
    And I log out
    And I log in as "user2" and complete feedback "Course anon feedback" in course "Course 1" with:
      | I don't know | 1 |
    And I log in as "user3" and complete feedback "Course anon feedback" in course "Course 1" with:
      | Not at all | 1 |
    And I log in as "user4" and complete feedback "Course anon feedback" in course "Course 1" with:
      | Yes of course | 1 |
    And I log in as "user5" and complete feedback "Course anon feedback" in course "Course 1" with:
      | Yes of course | 1 |
    And I log in as "user6" and complete feedback "Course anon feedback" in course "Course 1" with:
      | Not at all | 1 |
    And I log in as "user7" and complete feedback "Course anon feedback" in course "Course 1" with:
      | I don't know | 1 |
    # View analysis, user1 should only see one group - group 1
    And I am on the "Course anon feedback" "feedback activity" page logged in as user1
    And I follow "Analysis"
    And I should see "Separate groups: Group 1"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (25.00 %)" in the "Not at all" "table_row"
    And I log out
    # View analysis, user3 should only see one group - group 2
    And I am on the "Course anon feedback" "feedback activity" page logged in as user3
    And I follow "Analysis"
    And I should see "Separate groups: Group 2"
    And I show chart data for the "multichoice1" feedback
    And I should see "0" in the "Yes of course" "table_row"
    And I should see "2 (66.67 %)" in the "Not at all" "table_row"
    And I log out
    # View analysis, user2 should see a group selector and be able to change the group but not view all.
    And I am on the "Course anon feedback" "feedback activity" page logged in as user2
    And I follow "Analysis"
    And the field "Separate groups" matches value "Group 1"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (25.00 %)" in the "Not at all" "table_row"
    And I select "Group 2" from the "Separate groups" singleselect
    And I show chart data for the "multichoice1" feedback
    And I should see "0" in the "Yes of course" "table_row"
    And I should see "2 (66.67 %)" in the "Not at all" "table_row"
    And the "Separate groups" select box should not contain "All participants"
    And I log out
    # User without group can see all participants only
    And I am on the "Course anon feedback" "feedback activity" page logged in as user7
    And I follow "Analysis"
    And I should see "Separate groups: All participants"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (28.57 %)" in the "Yes of course" "table_row"
    And I should see "3 (42.86 %)" in the "Not at all" "table_row"
    And I should see "2 (28.57 %)" in the "I don't know" "table_row"
    And I log out
    # Teacher can browse everybody
    And I am on the "Course anon feedback" "feedback activity" page logged in as teacher
    And I navigate to "Analysis" in current page administration
    And the field "Separate groups" matches value "All participants"
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (28.57 %)" in the "Yes of course" "table_row"
    And I should see "3 (42.86 %)" in the "Not at all" "table_row"
    And I should see "2 (28.57 %)" in the "I don't know" "table_row"
    And I select "Group 1" from the "Separate groups" singleselect
    And I show chart data for the "multichoice1" feedback
    And I should see "2 (50.00 %)" in the "Yes of course" "table_row"
    And I should see "1 (25.00 %)" in the "Not at all" "table_row"
    And I select "Group 2" from the "Separate groups" singleselect
    And I show chart data for the "multichoice1" feedback
    And I should see "0" in the "Yes of course" "table_row"
    And I should see "2 (66.67 %)" in the "Not at all" "table_row"
    And I am on the "Course anon feedback" "feedback activity" page
    And I navigate to "Responses" in current page administration
    # The response numbers were randomly allocated, we only can assert the number of visible responses here:
    And the field "Separate groups" matches value "Group 2"
    And "//tr[contains(@id,'_r2') and contains(.,'Response number')]" "xpath_element" should exist
    And "//tr[contains(@id,'_r3') and contains(@class,'emptyrow')]" "xpath_element" should exist
    And I select "Group 1" from the "Separate groups" singleselect
    And "//tr[contains(@id,'_r3') and contains(.,'Response number')]" "xpath_element" should exist
    And "//tr[contains(@id,'_r4') and contains(@class,'emptyrow')]" "xpath_element" should exist
    And I select "All participants" from the "Separate groups" singleselect
    And "//tr[contains(@id,'_r6') and contains(.,'Response number')]" "xpath_element" should exist
    And "//tr[contains(@id,'_r7') and contains(@class,'emptyrow')]" "xpath_element" should exist
