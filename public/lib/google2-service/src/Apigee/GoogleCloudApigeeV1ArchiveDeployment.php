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

class GoogleCloudApigeeV1ArchiveDeployment extends \Google\Model
{
  /**
   * Output only. The time at which the Archive Deployment was created in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Input only. The Google Cloud Storage signed URL returned from
   * GenerateUploadUrl and used to upload the Archive zip file.
   *
   * @var string
   */
  public $gcsUri;
  /**
   * User-supplied key-value pairs used to organize ArchiveDeployments. Label
   * keys must be between 1 and 63 characters long, have a UTF-8 encoding of
   * maximum 128 bytes, and must conform to the following PCRE regular
   * expression: \p{Ll}\p{Lo}{0,62} Label values must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Name of the Archive Deployment in the following format:
   * `organizations/{org}/environments/{env}/archiveDeployments/{id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A reference to the LRO that created this Archive Deployment in
   * the following format: `organizations/{org}/operations/{id}`
   *
   * @var string
   */
  public $operation;
  /**
   * Output only. The time at which the Archive Deployment was updated in
   * milliseconds since the epoch.
   *
   * @var string
   */
  public $updatedAt;

  /**
   * Output only. The time at which the Archive Deployment was created in
   * milliseconds since the epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Input only. The Google Cloud Storage signed URL returned from
   * GenerateUploadUrl and used to upload the Archive zip file.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
  /**
   * User-supplied key-value pairs used to organize ArchiveDeployments. Label
   * keys must be between 1 and 63 characters long, have a UTF-8 encoding of
   * maximum 128 bytes, and must conform to the following PCRE regular
   * expression: \p{Ll}\p{Lo}{0,62} Label values must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Name of the Archive Deployment in the following format:
   * `organizations/{org}/environments/{env}/archiveDeployments/{id}`.
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
   * Output only. A reference to the LRO that created this Archive Deployment in
   * the following format: `organizations/{org}/operations/{id}`
   *
   * @param string $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return string
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Output only. The time at which the Archive Deployment was updated in
   * milliseconds since the epoch.
   *
   * @param string $updatedAt
   */
  public function setUpdatedAt($updatedAt)
  {
    $this->updatedAt = $updatedAt;
  }
  /**
   * @return string
   */
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ArchiveDeployment::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ArchiveDeployment');
