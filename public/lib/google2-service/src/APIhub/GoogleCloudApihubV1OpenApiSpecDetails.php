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

class GoogleCloudApihubV1OpenApiSpecDetails extends \Google\Model
{
  /**
   * SpecFile type unspecified.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * OpenAPI Spec v2.0.
   */
  public const FORMAT_OPEN_API_SPEC_2_0 = 'OPEN_API_SPEC_2_0';
  /**
   * OpenAPI Spec v3.0.
   */
  public const FORMAT_OPEN_API_SPEC_3_0 = 'OPEN_API_SPEC_3_0';
  /**
   * OpenAPI Spec v3.1.
   */
  public const FORMAT_OPEN_API_SPEC_3_1 = 'OPEN_API_SPEC_3_1';
  /**
   * Output only. The format of the spec.
   *
   * @var string
   */
  public $format;
  protected $ownerType = GoogleCloudApihubV1Owner::class;
  protected $ownerDataType = '';
  /**
   * Output only. The version in the spec. This maps to `info.version` in
   * OpenAPI spec.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. The format of the spec.
   *
   * Accepted values: FORMAT_UNSPECIFIED, OPEN_API_SPEC_2_0, OPEN_API_SPEC_3_0,
   * OPEN_API_SPEC_3_1
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Output only. Owner details for the spec. This maps to `info.contact` in
   * OpenAPI spec.
   *
   * @param GoogleCloudApihubV1Owner $owner
   */
  public function setOwner(GoogleCloudApihubV1Owner $owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return GoogleCloudApihubV1Owner
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * Output only. The version in the spec. This maps to `info.version` in
   * OpenAPI spec.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1OpenApiSpecDetails::class, 'Google_Service_APIhub_GoogleCloudApihubV1OpenApiSpecDetails');
