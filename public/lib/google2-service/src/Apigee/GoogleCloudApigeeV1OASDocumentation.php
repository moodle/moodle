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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1OASDocumentation extends \Google\Model
{
  /**
   * The format is not available.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * YAML format.
   */
  public const FORMAT_YAML = 'YAML';
  /**
   * JSON format.
   */
  public const FORMAT_JSON = 'JSON';
  /**
   * Output only. The format of the input specification file contents.
   *
   * @var string
   */
  public $format;
  protected $specType = GoogleCloudApigeeV1DocumentationFile::class;
  protected $specDataType = '';

  /**
   * Output only. The format of the input specification file contents.
   *
   * Accepted values: FORMAT_UNSPECIFIED, YAML, JSON
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
   * Required. The documentation file contents for the OpenAPI Specification.
   * JSON and YAML file formats are supported.
   *
   * @param GoogleCloudApigeeV1DocumentationFile $spec
   */
  public function setSpec(GoogleCloudApigeeV1DocumentationFile $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return GoogleCloudApigeeV1DocumentationFile
   */
  public function getSpec()
  {
    return $this->spec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1OASDocumentation::class, 'Google_Service_Apigee_GoogleCloudApigeeV1OASDocumentation');
