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

namespace Google\Service\FirebaseAppHosting;

class SourceUserMetadata extends \Google\Model
{
  /**
   * Output only. The user-chosen displayname. May be empty.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The account email linked to the EUC that created the build.
   * May be a service account or other robot account.
   *
   * @var string
   */
  public $email;
  /**
   * Output only. The URI of a profile photo associated with the user who
   * created the build.
   *
   * @var string
   */
  public $imageUri;

  /**
   * Output only. The user-chosen displayname. May be empty.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The account email linked to the EUC that created the build.
   * May be a service account or other robot account.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Output only. The URI of a profile photo associated with the user who
   * created the build.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SourceUserMetadata::class, 'Google_Service_FirebaseAppHosting_SourceUserMetadata');
