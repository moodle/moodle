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

class GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataGcsSource extends \Google\Collection
{
  protected $collection_key = 'uri';
  /**
   * Cloud Storage URI of one or more files. Only CSV files are supported. The
   * first line of the CSV file is used as the header. If there are multiple
   * files, the header is the first line of the lexicographically first file,
   * the other files must either contain the exact same header or omit the
   * header.
   *
   * @var string[]
   */
  public $uri;

  /**
   * Cloud Storage URI of one or more files. Only CSV files are supported. The
   * first line of the CSV file is used as the header. If there are multiple
   * files, the header is the first line of the lexicographically first file,
   * the other files must either contain the exact same header or omit the
   * header.
   *
   * @param string[] $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string[]
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataGcsSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTimeSeriesDatasetMetadataGcsSource');
