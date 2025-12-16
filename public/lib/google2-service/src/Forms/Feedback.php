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

class Feedback extends \Google\Collection
{
  protected $collection_key = 'material';
  protected $materialType = ExtraMaterial::class;
  protected $materialDataType = 'array';
  /**
   * Required. The main text of the feedback.
   *
   * @var string
   */
  public $text;

  /**
   * Additional information provided as part of the feedback, often used to
   * point the respondent to more reading and resources.
   *
   * @param ExtraMaterial[] $material
   */
  public function setMaterial($material)
  {
    $this->material = $material;
  }
  /**
   * @return ExtraMaterial[]
   */
  public function getMaterial()
  {
    return $this->material;
  }
  /**
   * Required. The main text of the feedback.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Feedback::class, 'Google_Service_Forms_Feedback');
