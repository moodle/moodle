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

namespace Google\Service\Storage;

class ComposeRequestSourceObjectsObjectPreconditions extends \Google\Model
{
  /**
   * Only perform the composition if the generation of the source object that
   * would be used matches this value. If this value and a generation are both
   * specified, they must be the same value or the call will fail.
   *
   * @var string
   */
  public $ifGenerationMatch;

  /**
   * Only perform the composition if the generation of the source object that
   * would be used matches this value. If this value and a generation are both
   * specified, they must be the same value or the call will fail.
   *
   * @param string $ifGenerationMatch
   */
  public function setIfGenerationMatch($ifGenerationMatch)
  {
    $this->ifGenerationMatch = $ifGenerationMatch;
  }
  /**
   * @return string
   */
  public function getIfGenerationMatch()
  {
    return $this->ifGenerationMatch;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ComposeRequestSourceObjectsObjectPreconditions::class, 'Google_Service_Storage_ComposeRequestSourceObjectsObjectPreconditions');
