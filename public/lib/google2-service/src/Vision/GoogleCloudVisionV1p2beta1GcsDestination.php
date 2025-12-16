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

namespace Google\Service\Vision;

class GoogleCloudVisionV1p2beta1GcsDestination extends \Google\Model
{
  /**
   * Google Cloud Storage URI prefix where the results will be stored. Results
   * will be in JSON format and preceded by its corresponding input URI prefix.
   * This field can either represent a gcs file prefix or gcs directory. In
   * either case, the uri should be unique because in order to get all of the
   * output files, you will need to do a wildcard gcs search on the uri prefix
   * you provide. Examples: * File Prefix: gs://bucket-name/here/filenameprefix
   * The output files will be created in gs://bucket-name/here/ and the names of
   * the output files will begin with "filenameprefix". * Directory Prefix:
   * gs://bucket-name/some/location/ The output files will be created in
   * gs://bucket-name/some/location/ and the names of the output files could be
   * anything because there was no filename prefix specified. If multiple
   * outputs, each response is still AnnotateFileResponse, each of which
   * contains some subset of the full list of AnnotateImageResponse. Multiple
   * outputs can happen if, for example, the output JSON is too large and
   * overflows into multiple sharded files.
   *
   * @var string
   */
  public $uri;

  /**
   * Google Cloud Storage URI prefix where the results will be stored. Results
   * will be in JSON format and preceded by its corresponding input URI prefix.
   * This field can either represent a gcs file prefix or gcs directory. In
   * either case, the uri should be unique because in order to get all of the
   * output files, you will need to do a wildcard gcs search on the uri prefix
   * you provide. Examples: * File Prefix: gs://bucket-name/here/filenameprefix
   * The output files will be created in gs://bucket-name/here/ and the names of
   * the output files will begin with "filenameprefix". * Directory Prefix:
   * gs://bucket-name/some/location/ The output files will be created in
   * gs://bucket-name/some/location/ and the names of the output files could be
   * anything because there was no filename prefix specified. If multiple
   * outputs, each response is still AnnotateFileResponse, each of which
   * contains some subset of the full list of AnnotateImageResponse. Multiple
   * outputs can happen if, for example, the output JSON is too large and
   * overflows into multiple sharded files.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVisionV1p2beta1GcsDestination::class, 'Google_Service_Vision_GoogleCloudVisionV1p2beta1GcsDestination');
