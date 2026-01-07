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

class GoogleCloudDataplexV1MetadataJobImportJobResult extends \Google\Model
{
  /**
   * Output only. The total number of entries that were created.
   *
   * @var string
   */
  public $createdEntries;
  /**
   * Output only. The total number of entry links that were successfully
   * created.
   *
   * @var string
   */
  public $createdEntryLinks;
  /**
   * Output only. The total number of entries that were deleted.
   *
   * @var string
   */
  public $deletedEntries;
  /**
   * Output only. The total number of entry links that were successfully
   * deleted.
   *
   * @var string
   */
  public $deletedEntryLinks;
  /**
   * Output only. The total number of entries that were recreated.
   *
   * @var string
   */
  public $recreatedEntries;
  /**
   * Output only. The total number of entries that were unchanged.
   *
   * @var string
   */
  public $unchangedEntries;
  /**
   * Output only. The total number of entry links that were left unchanged.
   *
   * @var string
   */
  public $unchangedEntryLinks;
  /**
   * Output only. The time when the status was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The total number of entries that were updated.
   *
   * @var string
   */
  public $updatedEntries;

  /**
   * Output only. The total number of entries that were created.
   *
   * @param string $createdEntries
   */
  public function setCreatedEntries($createdEntries)
  {
    $this->createdEntries = $createdEntries;
  }
  /**
   * @return string
   */
  public function getCreatedEntries()
  {
    return $this->createdEntries;
  }
  /**
   * Output only. The total number of entry links that were successfully
   * created.
   *
   * @param string $createdEntryLinks
   */
  public function setCreatedEntryLinks($createdEntryLinks)
  {
    $this->createdEntryLinks = $createdEntryLinks;
  }
  /**
   * @return string
   */
  public function getCreatedEntryLinks()
  {
    return $this->createdEntryLinks;
  }
  /**
   * Output only. The total number of entries that were deleted.
   *
   * @param string $deletedEntries
   */
  public function setDeletedEntries($deletedEntries)
  {
    $this->deletedEntries = $deletedEntries;
  }
  /**
   * @return string
   */
  public function getDeletedEntries()
  {
    return $this->deletedEntries;
  }
  /**
   * Output only. The total number of entry links that were successfully
   * deleted.
   *
   * @param string $deletedEntryLinks
   */
  public function setDeletedEntryLinks($deletedEntryLinks)
  {
    $this->deletedEntryLinks = $deletedEntryLinks;
  }
  /**
   * @return string
   */
  public function getDeletedEntryLinks()
  {
    return $this->deletedEntryLinks;
  }
  /**
   * Output only. The total number of entries that were recreated.
   *
   * @param string $recreatedEntries
   */
  public function setRecreatedEntries($recreatedEntries)
  {
    $this->recreatedEntries = $recreatedEntries;
  }
  /**
   * @return string
   */
  public function getRecreatedEntries()
  {
    return $this->recreatedEntries;
  }
  /**
   * Output only. The total number of entries that were unchanged.
   *
   * @param string $unchangedEntries
   */
  public function setUnchangedEntries($unchangedEntries)
  {
    $this->unchangedEntries = $unchangedEntries;
  }
  /**
   * @return string
   */
  public function getUnchangedEntries()
  {
    return $this->unchangedEntries;
  }
  /**
   * Output only. The total number of entry links that were left unchanged.
   *
   * @param string $unchangedEntryLinks
   */
  public function setUnchangedEntryLinks($unchangedEntryLinks)
  {
    $this->unchangedEntryLinks = $unchangedEntryLinks;
  }
  /**
   * @return string
   */
  public function getUnchangedEntryLinks()
  {
    return $this->unchangedEntryLinks;
  }
  /**
   * Output only. The time when the status was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The total number of entries that were updated.
   *
   * @param string $updatedEntries
   */
  public function setUpdatedEntries($updatedEntries)
  {
    $this->updatedEntries = $updatedEntries;
  }
  /**
   * @return string
   */
  public function getUpdatedEntries()
  {
    return $this->updatedEntries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1MetadataJobImportJobResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1MetadataJobImportJobResult');
