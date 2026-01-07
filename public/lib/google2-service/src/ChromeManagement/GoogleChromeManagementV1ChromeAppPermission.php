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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1ChromeAppPermission extends \Google\Model
{
  /**
   * Output only. If available, whether this permissions grants the
   * app/extension access to user data.
   *
   * @var bool
   */
  public $accessUserData;
  /**
   * Output only. If available, a URI to a page that has documentation for the
   * current permission.
   *
   * @var string
   */
  public $documentationUri;
  /**
   * Output only. The type of the permission.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. If available, whether this permissions grants the
   * app/extension access to user data.
   *
   * @param bool $accessUserData
   */
  public function setAccessUserData($accessUserData)
  {
    $this->accessUserData = $accessUserData;
  }
  /**
   * @return bool
   */
  public function getAccessUserData()
  {
    return $this->accessUserData;
  }
  /**
   * Output only. If available, a URI to a page that has documentation for the
   * current permission.
   *
   * @param string $documentationUri
   */
  public function setDocumentationUri($documentationUri)
  {
    $this->documentationUri = $documentationUri;
  }
  /**
   * @return string
   */
  public function getDocumentationUri()
  {
    return $this->documentationUri;
  }
  /**
   * Output only. The type of the permission.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1ChromeAppPermission::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1ChromeAppPermission');
