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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataAssetAccessGroupConfig extends \Google\Collection
{
  protected $collection_key = 'iamRoles';
  /**
   * Optional. IAM roles granted on the resource to this access group. Role name
   * follows https://cloud.google.com/iam/docs/reference/rest/v1/roles. Example:
   * "roles/bigquery.dataViewer"
   *
   * @var string[]
   */
  public $iamRoles;

  /**
   * Optional. IAM roles granted on the resource to this access group. Role name
   * follows https://cloud.google.com/iam/docs/reference/rest/v1/roles. Example:
   * "roles/bigquery.dataViewer"
   *
   * @param string[] $iamRoles
   */
  public function setIamRoles($iamRoles)
  {
    $this->iamRoles = $iamRoles;
  }
  /**
   * @return string[]
   */
  public function getIamRoles()
  {
    return $this->iamRoles;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataAssetAccessGroupConfig::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataAssetAccessGroupConfig');
