@core @qtype @qtype_gapfill @qtype_gapfill_basic @_switch_window
Feature: Test all the basic functionality of this Gapfill question type
    In order to evaluate students responses, As a teacher I need to
  create and preview gapfill questions.

  Background:
    Given the following "users" exist:
        | username | firstname | lastname | email               |
        | teacher  | Mark      | Allright | teacher@example.com |
    And the following "courses" exist:
        | fullname | shortname | category |
        | Course 1 | C1        | 0        |
    And the following "course enrolments" exist:
        | user    | course | role           |
        | teacher | C1     | editingteacher |
    Given the following "user preferences" exist:
        | user    | preference | value |
        | teacher | htmleditor | atto  |

  @javascript
  Scenario: Create, edit then preview a gapfill question.
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher
    # Create a new question.
    And I add a "item_qtype_gapfill" question filling the form with:
        | Question name                  | Gapfill-001                           |
        | Question text                  | The cat [sat] on the [mat]            |
        | General feedback               | This is general feedback              |
        | Penalty for each incorrect try | 0.3333333                             |
        | Hint 1                         | Incorrect placements will be removed. |
        | id_hintclearwrong_0            | 1                                     |
        | id_hintshownumcorrect_0        | 1                                     |
        | Hint 2                         | Incorrect placements will be removed. |
        | id_hintclearwrong_1            | 0                                     |
        | id_hintshownumcorrect_1        | 1                                     |
    Then I should see "Gapfill-001"

    When I choose "Edit question" action for "Gapfill-001" in the question bank
    And I set the field "Question name" to "Gapfill-002"

    And I press "Gap settings"
    And I click on "//span[@id='id1_0']" "xpath_element"
    And I wait "1" seconds

    And I set the field with xpath "//div[@id='id_correcteditable']" to "pergap correct feedback"
    And I set the field with xpath "//div[@id='id_incorrecteditable']" to "pergap incorrect feedback"

    And I click on "#SaveItemFeedback" "css_element"
    And I click on "Save changes" "button"
    When I am on the "Course 1" "core_question > course question bank" page logged in as teacher

    When I am on the "Gapfill-002" "core_question > preview" page logged in as teacher

 ##################################################
    # Deferred Feedback behaviour with CBM
    And I set the following fields to these values:
        | How questions behave | Deferred feedback with CBM |
        | Marked out of        | 2                          |
        | Marks                | Show mark and max          |
        | Specific feedback    | Shown                      |
        | Right answer         | Shown                      |

    # And I press "Start again with these options"
    And I press "Update display options"
    And I press "Start again"
    # And I drag "sat" into gap "1" in the gapfill question
    And I type "sat" into gap "1" in the gapfill question

    # And I drag "mat" into gap "2" in the gapfill question
    And I type "mat" into gap "2" in the gapfill question

    And I press "Submit and finish"
    And I should see "Your answer is correct."
    And I should see "CBM mark 2.00"
    And I should see "pergap correct feedback"

    And I press "Start again"
    # And I drag "sat" into gap "2" in the gapfill question
    And I type "sat" into gap "2" in the gapfill question

    # And I drag "mat" into gap "1" in the gapfill question
    And I type "mat" into gap "1" in the gapfill question

    And I press "Submit and finish"
    And I should see "pergap incorrect feedback"

    ##################################################
    # Adaptive Mode No Penalties behaviour
    And I set the following fields to these values:
        | How questions behave | Adaptive mode (no penalties) |
        | Marked out of        | 2                            |
        | Marks                | Show mark and max            |
        | Specific feedback    | Shown                        |
        | Right answer         | Shown                        |

    And I press "Save preview options"
    And I press "Start again"

    And I type "sat" into gap "1" in the gapfill question
    And I type "xxx" into gap "2" in the gapfill question

    And I press "Check"
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "1" seconds

    # And I drag "sat" into gap "1" in the gapfill question
    And I type "sat" into gap "1" in the gapfill question

    # And I drag "mat" into gap "2" in the gapfill question
    And I type "mat" into gap "2" in the gapfill question

    And I press "Check"
    And I should see "Your answer is correct."
    #full marks because this mode imposes no penalties for incorrect attempts
    And I should see "Mark 2.00 out of 2.00"
    And I wait "1" seconds

    And I press "Start again"
    And I type "sat" into gap "1" in the gapfill question
    And I type "xxx" into gap "2" in the gapfill question

    And I press "Submit and finish"
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "1" seconds

    And I press "Start again"
    And I type "xxx" into gap "1" in the gapfill question
    And I type "yyy" into gap "2" in the gapfill question

    And I press "Submit and finish"
    And I should see "Your answer is incorrect."
    And I should see "Mark 0.00 out of 2.00"
    And I wait "1" seconds

    #################################################
    #Interactive with multiple tries
    #################################################
    And I set the following fields to these values:
        | How questions behave | Interactive with multiple tries |
        | Marked out of        | 2                               |
        | Marks                | Show mark and max               |
        | Specific feedback    | Shown                           |
        | Right answer         | Shown                           |
    And I press "Save preview options"
    And I press "Start again"
    #Enter both correct responses
    # And I drag "sat" into gap "1" in the gapfill question
    And I type "sat" into gap "1" in the gapfill question

    # And I drag "mat" into gap "2" in the gapfill question
    And I type "mat" into gap "2" in the gapfill question

    And I press "Check"
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"

    #Enter one incorrect option on the first attempt
    #and all/both correct options on the second attempt
    ################################################
    #first attempt
    And I press "Start again"
    And I type "sat" into gap "1" in the gapfill question
    And I type "rugnotmat" into gap "2" in the gapfill question

    And I press "Check"
    And I should see "Your answer is partially correct."

    #This next line is shown because the box was checked in
    #the first hint for "Show the number of correct responses"
    And I should see "You completed 1 gap correctly out of 2."
    And I wait "2" seconds

    ################################################
    #second attempt
    And I press "Try again"

    #confirm that the wrong response has been cleared (set within hints)
    And I should not see "rugnotmat"
    And I wait "2" seconds

    # And I drag "sat" into gap "1" in the gapfill question
    And I type "sat" into gap "1" in the gapfill question

    # And I drag "mat" into gap "2" in the gapfill question
    And I type "mat" into gap "2" in the gapfill question

    And I press "Check"
    And I should see "Your answer is correct."
    And I should see "Mark 1.67 out of 2.00"
    And I wait "2" seconds

    ##################################################
    # Deferred Feedback behaviour
    And I set the following fields to these values:
        | How questions behave | Deferred feedback |
        | Marked out of        | 2                 |
        | Marks                | Show mark and max |
        | Specific feedback    | Shown             |
        | Right answer         | Shown             |

    And I press "Update display options"
    And I press "Start again"
    # And I drag "sat" into gap "1" in the gapfill question
    And I type "sat" into gap "1" in the gapfill question

    # And I drag "mat" into gap "2" in the gapfill question
    And I type "mat" into gap "2" in the gapfill question

    And I press "Submit and finish"
    And I should see "Your answer is correct."
    And I should see "Mark 2.00 out of 2.00"
    And I wait "1" seconds

    And I press "Start again"
    And I type "sat" into gap "1" in the gapfill question
    And I type "xxx" into gap "2" in the gapfill question

    And I press "Submit and finish"
    And I should see "Your answer is partially correct."
    And I should see "Mark 1.00 out of 2.00"
    And I wait "1" seconds

    And I press "Start again"
    And I type "xxx" into gap "1" in the gapfill question
    And I type "yyy" into gap "2" in the gapfill question

    And I press "Submit and finish"
    And I should see "Your answer is incorrect."
    And I should see "Mark 0.00 out of 2.00"
    And I wait "1" seconds
