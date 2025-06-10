<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * fetch question PHPUnit tests
 *
 * @copyright  2013 Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_adaptivequiz\local;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/mod/adaptivequiz/locallib.php');

use advanced_testcase;
use coding_exception;
use mod_adaptivequiz\local\repository\questions_number_per_difficulty;
use stdClass;

/**
 * @group mod_adaptivequiz
 * @covers \mod_adaptivequiz\local\fetchquestion
 */
class fetchquestion_test extends advanced_testcase {
    /** @var stdClass $activityinstance adaptivequiz activity instance object */
    protected $activityinstance = null;

    /** @var stdClass $cm a partially completed course module object */
    protected $cm = null;

    /** @var stdClass $user a user object */
    protected $user = null;

    /**
     * This function loads data into the PHPUnit tables for testing
     *
     * @return void
     * @throws coding_exception
     */
    protected function setup_test_data_xml() {
        $this->dataset_from_files(
            [__DIR__.'/../fixtures/mod_adaptivequiz_findquestion.xml']
        )->to_database();
    }

    /**
     * This function creates a default user and activity instance using generator classes
     * The activity parameters created are are follows:
     * lowest difficulty level: 1
     * highest difficulty level: 10
     * minimum question attempts: 2
     * maximum question attempts: 10
     * standard error: 1.1
     * starting level: 5
     * question category ids: 1
     * course id: 2
     * @return void
     */
    protected function setup_generator_data() {
        // Create test user.
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);
        $this->setAdminUser();

        // Create activity.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_adaptivequiz');
        $options = array(
                'highestlevel' => 10,
                'lowestlevel' => 1,
                'minimumquestions' => 2,
                'maximumquestions' => 10,
                'standarderror' => 1.1,
                'startinglevel' => 5,
                'questionpool' => array(1),
                'course' => 2
        );
        $this->activityinstance = $generator->create_instance($options);

        $this->cm = new stdClass();
        $this->cm->id = $this->activityinstance->cmid;
    }

    /**
     * This function creates a default user and activity instance using generator classes (using a different question category)
     * The activity parameters created are are follows:
     * lowest difficulty level: 1
     * highest difficulty level: 10
     * minimum question attempts: 2
     * maximum question attempts: 10
     * standard error: 1.1
     * starting level: 5
     * question category ids: 1
     * course id: 2
     * @return void
     */
    protected function setup_generator_data_two() {
        // Create test user.
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);
        $this->setAdminUser();

        // Create activity.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_adaptivequiz');
        $options = array(
                'highestlevel' => 10,
                'lowestlevel' => 1,
                'minimumquestions' => 2,
                'maximumquestions' => 10,
                'standarderror' => 1.1,
                'startinglevel' => 5,
                'questionpool' => array(4),
                'course' => 2
        );
        $this->activityinstance = $generator->create_instance($options);

        $this->cm = new stdClass();
        $this->cm->id = $this->activityinstance->cmid;
    }

    /**
     * This function tests the retrieval of using illegible tag ids.
     * @see setup_generator_data() for detail of activity instance.
     */
    public function test_find_questions_fail_tag_ids() {
        $this->resetAfterTest(true);
        $this->setup_test_data_xml();
        $this->setup_generator_data();

        $attempt = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['retrieve_question_categories']
            )
            ->setConstructorArgs(
                [$this->activityinstance, 1, 1, 100]
            )
            ->getMock();
        $attempt->expects($this->exactly(2))
            ->method('retrieve_question_categories')
            ->willReturn(
                [1 => 1, 2 => 2, 3 => 3]
            );

        $data = $attempt->find_questions_with_tags(
            [99]
        );
        $this->assertEquals(0, count($data));

        $data = $attempt->find_questions_with_tags([]);
        $this->assertEquals(0, count($data));
    }

    /**
     * This function tests the retrieval of questions using an empty set of question categories.
     * @see setup_generator_data() for detail of activity instance.
     */
    public function test_find_questions_fail_question_cat() {
        $this->resetAfterTest(true);
        $this->setup_test_data_xml();
        $this->setup_generator_data();

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['retrieve_question_categories']
            )
            ->setConstructorArgs(
                [$this->activityinstance, 1, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->exactly(2))
            ->method('retrieve_question_categories')
            ->willReturn([]);

        // Call class method with illegible tag id.
        $data = $mockclass->find_questions_with_tags(
            [99]
        );
        $this->assertEquals(0, count($data));

        // Call class method with legit tag id.
        $data = $mockclass->find_questions_with_tags(
            [1]
        );
        $this->assertEquals(0, count($data));
    }

    /**
     * This function tests the retrieval of questions using the exclude parameter
     * @see setup_generator_data() for detail of activity instance
     * @return void
     */
    public function test_find_questions_exclude() {
        $this->resetAfterTest(true);
        $this->setup_test_data_xml();
        $this->setup_generator_data();

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['retrieve_question_categories']
            )
            ->setConstructorArgs(
                [$this->activityinstance, 1, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('retrieve_question_categories')
            ->willReturn(
                [1 => 1, 2 => 2, 3 => 3]
            );

        $data = $mockclass->find_questions_with_tags([1], [1]);
        $this->assertEquals(0, count($data));
    }

    /**
     * This functions tests the accessor methods for the $level class variable.
     */
    public function test_get_set_level() {
        $this->resetAfterTest(true);
        $dummyclass = new stdClass();

        $fetchquestion = new fetchquestion($dummyclass, 99, 1, 100);
        $this->assertEquals(99, $fetchquestion->get_level());

        $fetchquestion->set_level(22);
        $this->assertEquals(22, $fetchquestion->get_level());

        $this->expectException('coding_exception');
        $fetchquestion->set_level(-22);
    }

    /**
     * @test
     */
    public function it_fails_when_instantiated_with_a_zero_difficulty_level(): void {
        $this->resetAfterTest(true);

        $this->expectException('coding_exception');
        (new fetchquestion(new stdClass(), 0, 1, 100, ['phpunittag_']));
    }

    /**
     * @test
     */
    public function it_fails_when_instantiated_with_a_negative_difficulty_level(): void {
        $this->resetAfterTest(true);

        $this->expectException('coding_exception');
        (new fetchquestion(new stdClass(), -11, 1, 100, ['phpunittag_']));
    }

    /**
     * @test
     */
    public function it_fails_when_instantiated_with_a_difficulty_level_as_a_string(): void {
        $this->resetAfterTest(true);

        $this->expectException('coding_exception');
        (new fetchquestion(new stdClass(), 'asdf', 1, 100, ['phpunittag_']));
    }

    /**
     * This functions tests the retrevial of tag ids with an associated difficulty level
     * but using legit data.
     */
    public function test_retrieve_tag() {
        $this->resetAfterTest();
        $this->setup_test_data_xml();

        $dummyclass = new stdClass();

        $fetchquestion = new fetchquestion($dummyclass, 5, 1, 100, ['phpunittag_']);
        $data = $fetchquestion->retrieve_tag(5);

        $this->assertEquals(2, count($data));
        $this->assertEquals([0 => 1, 1 => 2], $data);

        $fetchquestion2 = new fetchquestion($dummyclass, 888, 1, 100, ['phpunittag_']);
        $data = $fetchquestion2->retrieve_tag(888);

        $this->assertEquals(0, count($data));
    }

    /**
     * This function test output from fetch_question() where initalize_tags_with_quest_count() returns an empty array
     */
    public function test_fetch_question_initalize_tags_with_quest_count_return_empty_array() {
        $this->resetAfterTest(true);

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['initalize_tags_with_quest_count', 'retrieve_tag', 'find_questions_with_tags']
            )
            ->setConstructorArgs(
                [new stdClass(), 5, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('initalize_tags_with_quest_count')
            ->willReturn([]);
        $mockclass->expects($this->never())
            ->method('retrieve_tag');
        $mockclass->expects($this->never())
            ->method('find_questions_with_tags');

        $result = $mockclass->fetch_questions();
        $this->assertEquals([], $result);
    }

    /**
     * This function test output from fetch_question() where the initial requested level has available questions
     */
    public function test_fetch_question_requested_level_has_questions() {
        $this->resetAfterTest(true);

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['initalize_tags_with_quest_count', 'retrieve_tag', 'find_questions_with_tags']
            )
            ->setConstructorArgs(
                [new stdClass(), 5, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('initalize_tags_with_quest_count')
            ->with([], ['adpq_'], '1', '100')
            ->willReturn(
                    [5 => 2]
            );
        $mockclass->expects($this->once())
            ->method('retrieve_tag')
            ->with(5)
            ->willReturn(
                [11]
            );
        $mockclass->expects($this->once())
            ->method('find_questions_with_tags')
            ->with(array(11), array())
            ->willReturn(
                [22]
            );

        $result = $mockclass->fetch_questions();
        $this->assertEquals([22], $result);
    }

    /**
     * This function test output from fetch_question() where one level higher than requested level has available
     * questions.
     */
    public function test_fetch_question_one_level_higher_has_questions() {
        $this->resetAfterTest(true);

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['initalize_tags_with_quest_count', 'retrieve_tag', 'find_questions_with_tags']
            )
            ->setConstructorArgs(
                [new stdClass(), 5, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('initalize_tags_with_quest_count')
            ->with([], ['adpq_'], '1', '100')
            ->willReturn(
                [5 => 0, 6 => 1]
            );
        $mockclass->expects($this->once())
            ->method('retrieve_tag')
            ->with(6)
            ->willReturn(
                [11]
            );
        $mockclass->expects($this->once())
            ->method('find_questions_with_tags')
            ->with([11], [])
            ->willReturn(
                [22]
            );

        $result = $mockclass->fetch_questions();
        $this->assertEquals([22], $result);
    }

    /**
     * This function test output from fetch_question() where five levels higher than requested level has available
     * questions.
     */
    public function test_fetch_question_five_levels_higher_has_questions() {
        $this->resetAfterTest(true);

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['initalize_tags_with_quest_count', 'retrieve_tag', 'find_questions_with_tags']
            )
            ->setConstructorArgs(
                [new stdClass(), 5, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('initalize_tags_with_quest_count')
            ->with([], ['adpq_'], '1', '100')
            ->willReturn(
                [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 2]
            );
        $mockclass->expects($this->once())
            ->method('retrieve_tag')
            ->with(10)
            ->willReturn(
                [11]
            );
        $mockclass->expects($this->once())
            ->method('find_questions_with_tags')
            ->with([11], [])
            ->willReturn(
                [22]
            );

        $result = $mockclass->fetch_questions();
        $this->assertEquals([22], $result);
    }

    /**
     * This function test output from fetch_question() where four levels lower than requested level has available
     * questions.
     */
    public function test_fetch_question_four_levels_lower_has_questions() {
        $this->resetAfterTest(true);

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['initalize_tags_with_quest_count', 'retrieve_tag', 'find_questions_with_tags']
            )
            ->setConstructorArgs(
                [new stdClass(), 5, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('initalize_tags_with_quest_count')
            ->with([], ['adpq_'], '1', '100')
            ->willReturn(
                [1 => 1, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0]
            );
        $mockclass->expects($this->once())
            ->method('retrieve_tag')
            ->with(1)
            ->willReturn(
                [11]
            );
        $mockclass->expects($this->once())
            ->method('find_questions_with_tags')
            ->with([11], [])
            ->willReturn([22]);

        $result = $mockclass->fetch_questions();
        $this->assertEquals([22], $result);
    }

    /**
     * This function test output from fetch_question() where searching for a question goes outside
     * the min and max boundaries and stops the searching.
     */
    public function test_fetch_question_search_outside_min_max_bounds() {
        $this->resetAfterTest(true);

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['initalize_tags_with_quest_count', 'retrieve_tag', 'find_questions_with_tags']
            )
            ->setConstructorArgs(
                [new stdClass(),  50, 49, 51]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('initalize_tags_with_quest_count')
            ->with([], ['adpq_'], 49, 51)
            ->willReturn(
                [48 => 1, 52 => 1]
            );
        $mockclass->expects($this->never())
            ->method('retrieve_tag');
        $mockclass->expects($this->never())
            ->method('find_questions_with_tags');

        $result = $mockclass->fetch_questions();
        $this->assertEquals([], $result);
    }

    /**
     * @test
     */
    public function it_retrieves_all_tag_ids(): void {
        $this->resetAfterTest();
        $this->setup_test_data_xml();

        $fetchquestion = new fetchquestion(new stdClass(), 5, 1, 100);
        $result = $fetchquestion->retrieve_all_tag_ids(1, 100, ADAPTIVEQUIZ_QUESTION_TAG);

        $this->assertEquals(
            [5 => '1', 6 => '4', 7 => '5', 8 => '6', 9 => '7', 10 => '8'],
            $result
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_retrieves_all_tag_ids_for_an_empty_tag_prefix(): void {
        $fetchquestion = new fetchquestion(new stdClass(), 5, 1, 100);

        $this->expectException('invalid_parameter_exception');
        $fetchquestion->retrieve_all_tag_ids(1, 5, '');
    }

    /**
     * This is a data provider for
     * @return $data - an array with arrays of data
     */
    public function constructor_throw_coding_exception_provider() {
        $data = array(
            array(0, 1, 100),
            array(1, 100, 100),
            array(1, 100, 99)
        );

        return $data;
    }

    /**
     * This function tests throwing an exception by passing incorrect parameters
     *
     * @param int $level the difficulty level
     * @param int $min the minimum level of the attempt
     * @param int $max the maximum level of the attempt
     * @dataProvider constructor_throw_coding_exception_provider
     */
    public function test_constructor_throw_coding_exception($level, $min, $max) {
        $this->resetAfterTest(true);

        $this->expectException('coding_exception');
        (new fetchquestion(new stdClass(), $level, $min, $max));
    }

    /**
     * This function tests the output from initalize_tags_with_quest_count()
     */
    public function test_initalize_tags_with_quest_count() {
        $this->resetAfterTest();

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['retrieve_question_categories', 'retrieve_all_tag_ids', 'retrieve_tags_with_question_count']
            )
            ->setConstructorArgs(
                [new stdClass(), 1, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('retrieve_question_categories')
            ->willReturn(
                [1 => 1, 2 => 2, 3 => 3]
            );
        $mockclass->expects($this->exactly(2))
            ->method('retrieve_all_tag_ids')
            ->withAnyParameters()
            ->willReturn(
                [4 => 4, 5 => 5, 6 => 6]
            );
        $mockclass->expects($this->exactly(2))
            ->method('retrieve_tags_with_question_count')
            ->withAnyParameters()
            ->willReturn(
                [
                    new questions_number_per_difficulty(1, 8),
                    new questions_number_per_difficulty(2, 3),
                    new questions_number_per_difficulty(5, 10),
                ]
            );

        $result = $mockclass->initalize_tags_with_quest_count([], ['test1_', 'test2_'], 1, 100);
        $this->assertEquals([1 => 16, 2 => 6, 5 => 20], $result);
    }

    /**
     * This function tests the output from initalize_tags_with_quest_count(), passing an already built difficulty question
     * sum structure, forcing a rebuild.
     */
    public function test_initalize_tags_with_quest_count_pre_built_quest_sum_struct_rebuild_true() {
        $this->resetAfterTest();

        $mockclass = $this
            ->getMockBuilder(fetchquestion::class)
            ->onlyMethods(
                ['retrieve_question_categories', 'retrieve_all_tag_ids', 'retrieve_tags_with_question_count']
            )
            ->setConstructorArgs(
                [new stdClass(), 1, 1, 100]
            )
            ->getMock();
        $mockclass->expects($this->once())
            ->method('retrieve_question_categories')
            ->willReturn(
                [1 => 1, 2 => 2, 3 => 3]
            );
        $mockclass->expects($this->exactly(2))
            ->method('retrieve_all_tag_ids')
            ->withAnyParameters()
            ->willReturn(
                [4 => 4, 5 => 5, 6 => 6]
            );
        $mockclass->expects($this->exactly(2))
            ->method('retrieve_tags_with_question_count')
            ->withAnyParameters()
            ->willReturn(
                [
                    new questions_number_per_difficulty(1, 8),
                    new questions_number_per_difficulty(2, 3),
                    new questions_number_per_difficulty(5, 10),
                ]
            );

        $result = $mockclass->initalize_tags_with_quest_count([1, 2, 3, 4], ['test1_', 'test2_'], 1, 100, true);
        $this->assertEquals([1 => 16, 2 => 6, 5 => 20], $result);
    }

    /**
     * This function tests the output from decrement_question_sum_from_difficulty().
     */
    public function test_decrement_question_sum_from_difficulty() {
        $this->resetAfterTest(true);

        $dummyclass = new stdClass();
        $result = array(1 => 12);
        $expected = array(1 => 11);

        $fetchquestion = new fetchquestion($dummyclass, 1, 1, 2);
        $result = $fetchquestion->decrement_question_sum_from_difficulty($result, 1);
        $this->assertEquals($expected, $result);
    }

    /**
     * This function tests the output from decrement_question_sum_from_difficulty(), using a key that doesn't exist.
     */
    public function test_decrement_question_sum_from_difficulty_user_missing_key() {
        $this->resetAfterTest(true);

        $dummyclass = new stdClass();
        $result = array(1 => 12);
        $expected = array(1 => 12);

        $fetchquestion = new fetchquestion($dummyclass, 1, 1, 2);
        $result = $fetchquestion->decrement_question_sum_from_difficulty($result, 2);
        $this->assertEquals($expected, $result);
    }
}
