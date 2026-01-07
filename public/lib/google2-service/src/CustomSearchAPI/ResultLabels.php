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

namespace Google\Service\CustomSearchAPI;

class ResultLabels extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "labelWithOp" => "label_with_op",
  ];
  /**
   * The display name of a refinement label. This is the name you should display
   * in your user interface.
   *
   * @var string
   */
  public $displayName;
  /**
   * Refinement label and the associated refinement operation.
   *
   * @var string
   */
  public $labelWithOp;
  /**
   * The name of a refinement label, which you can use to refine searches. Don't
   * display this in your user interface; instead, use displayName.
   *
   * @var string
   */
  public $name;

  /**
   * The display name of a refinement label. This is the name you should display
   * in your user interface.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Refinement label and the associated refinement operation.
   *
   * @param string $labelWithOp
   */
  public function setLabelWithOp($labelWithOp)
  {
    $this->labelWithOp = $labelWithOp;
  }
  /**
   * @return string
   */
  public function getLabelWithOp()
  {
    return $this->labelWithOp;
  }
  /**
   * The name of a refinement label, which you can use to refine searches. Don't
   * display this in your user interface; instead, use displayName.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResultLabels::class, 'Google_Service_CustomSearchAPI_ResultLabels');
