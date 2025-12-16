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

class GoogleCloudApigeeV1RevisionStatus extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $errorsType = GoogleCloudApigeeV1UpdateError::class;
  protected $errorsDataType = 'array';
  /**
   * The json content of the resource revision. Large specs should be sent
   * individually via the spec field to avoid hitting request size limits.
   *
   * @var string
   */
  public $jsonSpec;
  /**
   * The number of replicas that have successfully loaded this revision.
   *
   * @var int
   */
  public $replicas;
  /**
   * The revision of the resource.
   *
   * @var string
   */
  public $revisionId;

  /**
   * Errors reported when attempting to load this revision.
   *
   * @param GoogleCloudApigeeV1UpdateError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return GoogleCloudApigeeV1UpdateError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The json content of the resource revision. Large specs should be sent
   * individually via the spec field to avoid hitting request size limits.
   *
   * @param string $jsonSpec
   */
  public function setJsonSpec($jsonSpec)
  {
    $this->jsonSpec = $jsonSpec;
  }
  /**
   * @return string
   */
  public function getJsonSpec()
  {
    return $this->jsonSpec;
  }
  /**
   * The number of replicas that have successfully loaded this revision.
   *
   * @param int $replicas
   */
  public function setReplicas($replicas)
  {
    $this->replicas = $replicas;
  }
  /**
   * @return int
   */
  public function getReplicas()
  {
    return $this->replicas;
  }
  /**
   * The revision of the resource.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1RevisionStatus::class, 'Google_Service_Apigee_GoogleCloudApigeeV1RevisionStatus');
