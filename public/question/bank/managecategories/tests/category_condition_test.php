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

namespace qbank_managecategories;

use core\context\module;
use core_question\category_manager;
use core_question\test\mock_restore_test_trait;

/**
 * Unit tests for category_condition
 *
 * @package   qbank_managecategories
 * @copyright 2025 onwards Catalyst IT EU {@link https://catalyst-eu.net}
 * @author    Mark Johnson <mark.johnson@catalyst-eu.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers \qbank_managecategories\category_condition
 */
final class category_condition_test extends \advanced_testcase {
    use mock_restore_test_trait;

    /**
     * Restore a filter where we should keep the reference to the original context.
     *
     * - We are restoring to the same site.
     * - The question context exists.
     * - The question category exists.
     * - The using context and questions context are different (e.g. the quiz is referencing a category in a qbank).
     * - The current user is allowed to use the questions.
     */
    public function test_restore_filtercondition(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid + 1,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $this->setAdminUser();

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep);

        $this->assertEquals($category->id, $filtercondition['filter']['category']['values'][0]);
    }

    /**
     * As {@see test_restore_filtercondition}, but the user does not have permission to use questions in the original category.
     */
    public function test_restore_filtercondition_no_permissions(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid + 1,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep);

        $this->assertEquals($mappedid, $filtercondition['filter']['category']['values'][0]);
    }

    /**
     * As {@see test_restore_filtercondition}, but the questions context and using context are the same.
     */
    public function test_restore_filtercondition_same_context(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $this->setAdminUser();

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep);

        $this->assertEquals($mappedid, $filtercondition['filter']['category']['values'][0]);
    }

    /**
     * As {@see test_restore_filtercondition}, but the original category has been deleted.
     */
    public function test_restore_filtercondition_no_category(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $this->setAdminUser();

        $manager = new category_manager();
        $manager->delete_category($category->id);

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep);

        $this->assertEquals($mappedid, $filtercondition['filter']['category']['values'][0]);
    }

    /**
     * As {@see test_restore_filtercondition}, but the original context has been deleted.
     */
    public function test_restore_filtercondition_no_context(): void {
        $this->resetAfterTest();
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $qbank1 = $this->getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbank1context = module::instance($qbank1->cmid);
        $oldparent = question_get_default_category($qbank1context->id);
        $category = $questiongenerator->create_question_category(['parent' => $oldparent->id]);
        // Move the category to a different context.
        $qbank2 = $this->getDataGenerator()->create_module('qbank', ['course' => $course->id]);
        $qbank2context = module::instance($qbank2->cmid);
        $newparent = question_get_default_category($qbank2context->id);
        $manager = new category_manager();
        $manager->update_category($category->id, "{$newparent->id},{$qbank2context->id}", $category->name, $category->info);
        // Delete the original context.
        course_delete_module($qbank1->cmid);
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid + 1,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep);

        $this->assertEquals($mappedid, $filtercondition['filter']['category']['values'][0]);
    }

    /**
     * As {@see test_restore_filtercondition}, but restoring to a different site.
     */
    public function test_restore_filtercondition_different_site(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid + 1,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_not_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $this->setAdminUser();

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep);

        $this->assertEquals($mappedid, $filtercondition['filter']['category']['values'][0]);
    }

    /**
     * As {@see test_restore_filtercondition}, but the original question bank was present in the backup.
     */
    public function test_restore_filtercondition_questionbankinbackup(): void {
        $this->resetAfterTest();
        $questiongenerator = $this->getDataGenerator()->get_plugin_generator('core_question');
        $category = $questiongenerator->create_question_category();
        $filtercondition = [
            'filter' => [
                'category' => [
                    'values' => [
                        $category->id,
                    ],
                ],
            ],
        ];
        $setreference = (object) [
            'questionscontextid' => $category->contextid,
            'usingcontextid' => $category->contextid + 1,
        ];
        $mappedid = $category->id + 1;
        $mockstep = $this->get_mock_step($this->get_samesite_task());
        $mockstep->method('get_mappingid')->willReturn($mappedid);

        $this->setAdminUser();

        $condition = new category_condition();

        $filtercondition = $condition->restore_filtercondition($filtercondition, $setreference, $mockstep, true);

        $this->assertEquals($mappedid, $filtercondition['filter']['category']['values'][0]);
    }
}
