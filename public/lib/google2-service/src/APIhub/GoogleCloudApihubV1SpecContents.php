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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1SpecContents extends \Google\Model
{
  /**
   * Required. The contents of the spec.
   *
   * @var string
   */
  public $contents;
  /**
   * Required. The mime type of the content for example application/json,
   * application/yaml, application/wsdl etc.
   *
   * @var string
   */
  public $mimeType;

  /**
   * Required. The contents of the spec.
   *
   * @param string $contents
   */
  public function setContents($contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return string
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Required. The mime type of the content for example application/json,
   * application/yaml, application/wsdl etc.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1SpecContents::class, 'Google_Service_APIhub_GoogleCloudApihubV1SpecContents');
