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

class GoogleCloudApigeeV1Metadata extends \Google\Collection
{
  protected $collection_key = 'notices';
  /**
   * List of error messages as strings.
   *
   * @var string[]
   */
  public $errors;
  /**
   * List of additional information such as data source, if result was
   * truncated. For example: ``` "notices": [ "Source:Postgres", "PG
   * Host:uappg0rw.e2e.apigeeks.net", "query served
   * by:4b64601e-40de-4eb1-bfb9-eeee7ac929ed", "Table used:
   * edge.api.uapgroup2.agg_api" ]```
   *
   * @var string[]
   */
  public $notices;

  /**
   * List of error messages as strings.
   *
   * @param string[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return string[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * List of additional information such as data source, if result was
   * truncated. For example: ``` "notices": [ "Source:Postgres", "PG
   * Host:uappg0rw.e2e.apigeeks.net", "query served
   * by:4b64601e-40de-4eb1-bfb9-eeee7ac929ed", "Table used:
   * edge.api.uapgroup2.agg_api" ]```
   *
   * @param string[] $notices
   */
  public function setNotices($notices)
  {
    $this->notices = $notices;
  }
  /**
   * @return string[]
   */
  public function getNotices()
  {
    return $this->notices;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Metadata::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Metadata');
