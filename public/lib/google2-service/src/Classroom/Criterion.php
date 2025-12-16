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

namespace Google\Service\Classroom;

class Criterion extends \Google\Collection
{
  protected $collection_key = 'levels';
  /**
   * The description of the criterion.
   *
   * @var string
   */
  public $description;
  /**
   * The criterion ID. On creation, an ID is assigned.
   *
   * @var string
   */
  public $id;
  protected $levelsType = Level::class;
  protected $levelsDataType = 'array';
  /**
   * The title of the criterion.
   *
   * @var string
   */
  public $title;

  /**
   * The description of the criterion.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The criterion ID. On creation, an ID is assigned.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The list of levels within this criterion.
   *
   * @param Level[] $levels
   */
  public function setLevels($levels)
  {
    $this->levels = $levels;
  }
  /**
   * @return Level[]
   */
  public function getLevels()
  {
    return $this->levels;
  }
  /**
   * The title of the criterion.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Criterion::class, 'Google_Service_Classroom_Criterion');
