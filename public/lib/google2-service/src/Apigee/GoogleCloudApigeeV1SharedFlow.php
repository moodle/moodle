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

class GoogleCloudApigeeV1SharedFlow extends \Google\Collection
{
  protected $collection_key = 'revision';
  /**
   * The id of the most recently created revision for this shared flow.
   *
   * @var string
   */
  public $latestRevisionId;
  protected $metaDataType = GoogleCloudApigeeV1EntityMetadata::class;
  protected $metaDataDataType = '';
  /**
   * The ID of the shared flow.
   *
   * @var string
   */
  public $name;
  /**
   * A list of revisions of this shared flow.
   *
   * @var string[]
   */
  public $revision;
  /**
   * Optional. The ID of the space associated with this shared flow. Any IAM
   * policies applied to the space will control access to this shared flow. To
   * learn how Spaces can be used to manage resources, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   *
   * @var string
   */
  public $space;

  /**
   * The id of the most recently created revision for this shared flow.
   *
   * @param string $latestRevisionId
   */
  public function setLatestRevisionId($latestRevisionId)
  {
    $this->latestRevisionId = $latestRevisionId;
  }
  /**
   * @return string
   */
  public function getLatestRevisionId()
  {
    return $this->latestRevisionId;
  }
  /**
   * Metadata describing the shared flow.
   *
   * @param GoogleCloudApigeeV1EntityMetadata $metaData
   */
  public function setMetaData(GoogleCloudApigeeV1EntityMetadata $metaData)
  {
    $this->metaData = $metaData;
  }
  /**
   * @return GoogleCloudApigeeV1EntityMetadata
   */
  public function getMetaData()
  {
    return $this->metaData;
  }
  /**
   * The ID of the shared flow.
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
   * A list of revisions of this shared flow.
   *
   * @param string[] $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string[]
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Optional. The ID of the space associated with this shared flow. Any IAM
   * policies applied to the space will control access to this shared flow. To
   * learn how Spaces can be used to manage resources, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   *
   * @param string $space
   */
  public function setSpace($space)
  {
    $this->space = $space;
  }
  /**
   * @return string
   */
  public function getSpace()
  {
    return $this->space;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SharedFlow::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SharedFlow');
