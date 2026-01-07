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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1Content extends \Google\Collection
{
  protected $collection_key = 'parts';
  protected $partsType = GoogleCloudAiplatformV1Part::class;
  protected $partsDataType = 'array';
  /**
   * Optional. The producer of the content. Must be either 'user' or 'model'. If
   * not set, the service will default to 'user'.
   *
   * @var string
   */
  public $role;

  /**
   * Required. A list of Part objects that make up a single message. Parts of a
   * message can have different MIME types. A Content message must have at least
   * one Part.
   *
   * @param GoogleCloudAiplatformV1Part[] $parts
   */
  public function setParts($parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return GoogleCloudAiplatformV1Part[]
   */
  public function getParts()
  {
    return $this->parts;
  }
  /**
   * Optional. The producer of the content. Must be either 'user' or 'model'. If
   * not set, the service will default to 'user'.
   *
   * @param string $role
   */
  public function setRole($role)
  {
    $this->role = $role;
  }
  /**
   * @return string
   */
  public function getRole()
  {
    return $this->role;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Content::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Content');
