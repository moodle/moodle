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

namespace Google\Service\Baremetalsolution;

class RenameInstanceRequest extends \Google\Model
{
  /**
   * Required. The new `id` of the instance.
   *
   * @var string
   */
  public $newInstanceId;

  /**
   * Required. The new `id` of the instance.
   *
   * @param string $newInstanceId
   */
  public function setNewInstanceId($newInstanceId)
  {
    $this->newInstanceId = $newInstanceId;
  }
  /**
   * @return string
   */
  public function getNewInstanceId()
  {
    return $this->newInstanceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RenameInstanceRequest::class, 'Google_Service_Baremetalsolution_RenameInstanceRequest');
