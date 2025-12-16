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

namespace Google\Service\AccessContextManager;

class EgressPolicy extends \Google\Model
{
  protected $egressFromType = EgressFrom::class;
  protected $egressFromDataType = '';
  protected $egressToType = EgressTo::class;
  protected $egressToDataType = '';
  /**
   * Optional. Human-readable title for the egress rule. The title must be
   * unique within the perimeter and can not exceed 100 characters. Within the
   * access policy, the combined length of all rule titles must not exceed
   * 240,000 characters.
   *
   * @var string
   */
  public $title;

  /**
   * Defines conditions on the source of a request causing this EgressPolicy to
   * apply.
   *
   * @param EgressFrom $egressFrom
   */
  public function setEgressFrom(EgressFrom $egressFrom)
  {
    $this->egressFrom = $egressFrom;
  }
  /**
   * @return EgressFrom
   */
  public function getEgressFrom()
  {
    return $this->egressFrom;
  }
  /**
   * Defines the conditions on the ApiOperation and destination resources that
   * cause this EgressPolicy to apply.
   *
   * @param EgressTo $egressTo
   */
  public function setEgressTo(EgressTo $egressTo)
  {
    $this->egressTo = $egressTo;
  }
  /**
   * @return EgressTo
   */
  public function getEgressTo()
  {
    return $this->egressTo;
  }
  /**
   * Optional. Human-readable title for the egress rule. The title must be
   * unique within the perimeter and can not exceed 100 characters. Within the
   * access policy, the combined length of all rule titles must not exceed
   * 240,000 characters.
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
class_alias(EgressPolicy::class, 'Google_Service_AccessContextManager_EgressPolicy');
