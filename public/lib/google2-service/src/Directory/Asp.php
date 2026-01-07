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

namespace Google\Service\Directory;

class Asp extends \Google\Model
{
  /**
   * The unique ID of the ASP.
   *
   * @var int
   */
  public $codeId;
  /**
   * The time when the ASP was created. Expressed in [Unix
   * time](https://en.wikipedia.org/wiki/Epoch_time) format.
   *
   * @var string
   */
  public $creationTime;
  /**
   * ETag of the ASP.
   *
   * @var string
   */
  public $etag;
  /**
   * The type of the API resource. This is always `admin#directory#asp`.
   *
   * @var string
   */
  public $kind;
  /**
   * The time when the ASP was last used. Expressed in [Unix
   * time](https://en.wikipedia.org/wiki/Epoch_time) format.
   *
   * @var string
   */
  public $lastTimeUsed;
  /**
   * The name of the application that the user, represented by their `userId`,
   * entered when the ASP was created.
   *
   * @var string
   */
  public $name;
  /**
   * The unique ID of the user who issued the ASP.
   *
   * @var string
   */
  public $userKey;

  /**
   * The unique ID of the ASP.
   *
   * @param int $codeId
   */
  public function setCodeId($codeId)
  {
    $this->codeId = $codeId;
  }
  /**
   * @return int
   */
  public function getCodeId()
  {
    return $this->codeId;
  }
  /**
   * The time when the ASP was created. Expressed in [Unix
   * time](https://en.wikipedia.org/wiki/Epoch_time) format.
   *
   * @param string $creationTime
   */
  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }
  /**
   * @return string
   */
  public function getCreationTime()
  {
    return $this->creationTime;
  }
  /**
   * ETag of the ASP.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The type of the API resource. This is always `admin#directory#asp`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The time when the ASP was last used. Expressed in [Unix
   * time](https://en.wikipedia.org/wiki/Epoch_time) format.
   *
   * @param string $lastTimeUsed
   */
  public function setLastTimeUsed($lastTimeUsed)
  {
    $this->lastTimeUsed = $lastTimeUsed;
  }
  /**
   * @return string
   */
  public function getLastTimeUsed()
  {
    return $this->lastTimeUsed;
  }
  /**
   * The name of the application that the user, represented by their `userId`,
   * entered when the ASP was created.
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
   * The unique ID of the user who issued the ASP.
   *
   * @param string $userKey
   */
  public function setUserKey($userKey)
  {
    $this->userKey = $userKey;
  }
  /**
   * @return string
   */
  public function getUserKey()
  {
    return $this->userKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Asp::class, 'Google_Service_Directory_Asp');
