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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1GcsSource extends \Google\Collection
{
  protected $collection_key = 'inputUris';
  /**
   * The schema to use when parsing the data from the source. Supported values
   * for document imports: * `document` (default): One JSON Document per line.
   * Each document must have a valid Document.id. * `content`: Unstructured data
   * (e.g. PDF, HTML). Each file matched by `input_uris` becomes a document,
   * with the ID set to the first 128 bits of SHA256(URI) encoded as a hex
   * string. * `custom`: One custom data JSON per row in arbitrary format that
   * conforms to the defined Schema of the data store. This can only be used by
   * the GENERIC Data Store vertical. * `csv`: A CSV file with header conforming
   * to the defined Schema of the data store. Each entry after the header is
   * imported as a Document. This can only be used by the GENERIC Data Store
   * vertical. Supported values for user event imports: * `user_event`
   * (default): One JSON UserEvent per line.
   *
   * @var string
   */
  public $dataSchema;
  /**
   * Required. Cloud Storage URIs to input files. Each URI can be up to 2000
   * characters long. URIs can match the full object path (for example,
   * `gs://bucket/directory/object.json`) or a pattern matching one or more
   * files, such as `gs://bucket/directory.json`. A request can contain at most
   * 100 files (or 100,000 files if `data_schema` is `content`). Each file can
   * be up to 2 GB (or 100 MB if `data_schema` is `content`).
   *
   * @var string[]
   */
  public $inputUris;

  /**
   * The schema to use when parsing the data from the source. Supported values
   * for document imports: * `document` (default): One JSON Document per line.
   * Each document must have a valid Document.id. * `content`: Unstructured data
   * (e.g. PDF, HTML). Each file matched by `input_uris` becomes a document,
   * with the ID set to the first 128 bits of SHA256(URI) encoded as a hex
   * string. * `custom`: One custom data JSON per row in arbitrary format that
   * conforms to the defined Schema of the data store. This can only be used by
   * the GENERIC Data Store vertical. * `csv`: A CSV file with header conforming
   * to the defined Schema of the data store. Each entry after the header is
   * imported as a Document. This can only be used by the GENERIC Data Store
   * vertical. Supported values for user event imports: * `user_event`
   * (default): One JSON UserEvent per line.
   *
   * @param string $dataSchema
   */
  public function setDataSchema($dataSchema)
  {
    $this->dataSchema = $dataSchema;
  }
  /**
   * @return string
   */
  public function getDataSchema()
  {
    return $this->dataSchema;
  }
  /**
   * Required. Cloud Storage URIs to input files. Each URI can be up to 2000
   * characters long. URIs can match the full object path (for example,
   * `gs://bucket/directory/object.json`) or a pattern matching one or more
   * files, such as `gs://bucket/directory.json`. A request can contain at most
   * 100 files (or 100,000 files if `data_schema` is `content`). Each file can
   * be up to 2 GB (or 100 MB if `data_schema` is `content`).
   *
   * @param string[] $inputUris
   */
  public function setInputUris($inputUris)
  {
    $this->inputUris = $inputUris;
  }
  /**
   * @return string[]
   */
  public function getInputUris()
  {
    return $this->inputUris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GcsSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GcsSource');
