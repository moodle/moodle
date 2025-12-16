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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3Changelog extends \Google\Model
{
  /**
   * The action of the change.
   *
   * @var string
   */
  public $action;
  /**
   * The timestamp of the change.
   *
   * @var string
   */
  public $createTime;
  /**
   * The affected resource display name of the change.
   *
   * @var string
   */
  public $displayName;
  /**
   * The affected language code of the change.
   *
   * @var string
   */
  public $languageCode;
  /**
   * The unique identifier of the changelog. Format:
   * `projects//locations//agents//changelogs/`.
   *
   * @var string
   */
  public $name;
  /**
   * The affected resource name of the change.
   *
   * @var string
   */
  public $resource;
  /**
   * The affected resource type.
   *
   * @var string
   */
  public $type;
  /**
   * Email address of the authenticated user.
   *
   * @var string
   */
  public $userEmail;

  /**
   * The action of the change.
   *
   * @param string $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return string
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * The timestamp of the change.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The affected resource display name of the change.
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
   * The affected language code of the change.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The unique identifier of the changelog. Format:
   * `projects//locations//agents//changelogs/`.
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
   * The affected resource name of the change.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * The affected resource type.
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
  /**
   * Email address of the authenticated user.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Changelog::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Changelog');
