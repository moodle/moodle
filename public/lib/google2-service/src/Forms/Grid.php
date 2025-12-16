<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Forms;

class Grid extends \Google\Model
{
  protected $columnsType = ChoiceQuestion::class;
  protected $columnsDataType = '';
  /**
   * If `true`, the questions are randomly ordered. In other words, the rows
   * appear in a different order for every respondent.
   *
   * @var bool
   */
  public $shuffleQuestions;

  /**
   * Required. The choices shared by each question in the grid. In other words,
   * the values of the columns. Only `CHECK_BOX` and `RADIO` choices are
   * allowed.
   *
   * @param ChoiceQuestion $columns
   */
  public function setColumns(ChoiceQuestion $columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return ChoiceQuestion
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * If `true`, the questions are randomly ordered. In other words, the rows
   * appear in a different order for every respondent.
   *
   * @param bool $shuffleQuestions
   */
  public function setShuffleQuestions($shuffleQuestions)
  {
    $this->shuffleQuestions = $shuffleQuestions;
  }
  /**
   * @return bool
   */
  public function getShuffleQuestions()
  {
    return $this->shuffleQuestions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Grid::class, 'Google_Service_Forms_Grid');
