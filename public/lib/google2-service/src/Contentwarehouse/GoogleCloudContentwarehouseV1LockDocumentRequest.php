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

namespace Google\Service\Contentwarehouse;

class GoogleCloudContentwarehouseV1LockDocumentRequest extends \Google\Model
{
  /**
   * The collection the document connects to.
   *
   * @var string
   */
  public $collectionId;
  protected $lockingUserType = GoogleCloudContentwarehouseV1UserInfo::class;
  protected $lockingUserDataType = '';

  /**
   * The collection the document connects to.
   *
   * @param string $collectionId
   */
  public function setCollectionId($collectionId)
  {
    $this->collectionId = $collectionId;
  }
  /**
   * @return string
   */
  public function getCollectionId()
  {
    return $this->collectionId;
  }
  /**
   * The user information who locks the document.
   *
   * @param GoogleCloudContentwarehouseV1UserInfo $lockingUser
   */
  public function setLockingUser(GoogleCloudContentwarehouseV1UserInfo $lockingUser)
  {
    $this->lockingUser = $lockingUser;
  }
  /**
   * @return GoogleCloudContentwarehouseV1UserInfo
   */
  public function getLockingUser()
  {
    return $this->lockingUser;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContentwarehouseV1LockDocumentRequest::class, 'Google_Service_Contentwarehouse_GoogleCloudContentwarehouseV1LockDocumentRequest');
