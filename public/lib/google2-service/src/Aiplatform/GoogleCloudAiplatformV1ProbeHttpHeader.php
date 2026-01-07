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

class GoogleCloudAiplatformV1ProbeHttpHeader extends \Google\Model
{
  /**
   * The header field name. This will be canonicalized upon output, so case-
   * variant names will be understood as the same header.
   *
   * @var string
   */
  public $name;
  /**
   * The header field value
   *
   * @var string
   */
  public $value;

  /**
   * The header field name. This will be canonicalized upon output, so case-
   * variant names will be understood as the same header.
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
  /**
   * The header field value
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ProbeHttpHeader::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ProbeHttpHeader');
