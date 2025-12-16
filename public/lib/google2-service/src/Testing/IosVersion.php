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

namespace Google\Service\Testing;

class IosVersion extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * An opaque id for this iOS version. Use this id to invoke the
   * TestExecutionService.
   *
   * @var string
   */
  public $id;
  /**
   * An integer representing the major iOS version. Examples: "8", "9".
   *
   * @var int
   */
  public $majorVersion;
  /**
   * An integer representing the minor iOS version. Examples: "1", "2".
   *
   * @var int
   */
  public $minorVersion;
  /**
   * The available Xcode versions for this version.
   *
   * @var string[]
   */
  public $supportedXcodeVersionIds;
  /**
   * Tags for this dimension. Examples: "default", "preview", "deprecated".
   *
   * @var string[]
   */
  public $tags;

  /**
   * An opaque id for this iOS version. Use this id to invoke the
   * TestExecutionService.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * An integer representing the major iOS version. Examples: "8", "9".
   *
   * @param int $majorVersion
   */
  public function setMajorVersion($majorVersion)
  {
    $this->majorVersion = $majorVersion;
  }
  /**
   * @return int
   */
  public function getMajorVersion()
  {
    return $this->majorVersion;
  }
  /**
   * An integer representing the minor iOS version. Examples: "1", "2".
   *
   * @param int $minorVersion
   */
  public function setMinorVersion($minorVersion)
  {
    $this->minorVersion = $minorVersion;
  }
  /**
   * @return int
   */
  public function getMinorVersion()
  {
    return $this->minorVersion;
  }
  /**
   * The available Xcode versions for this version.
   *
   * @param string[] $supportedXcodeVersionIds
   */
  public function setSupportedXcodeVersionIds($supportedXcodeVersionIds)
  {
    $this->supportedXcodeVersionIds = $supportedXcodeVersionIds;
  }
  /**
   * @return string[]
   */
  public function getSupportedXcodeVersionIds()
  {
    return $this->supportedXcodeVersionIds;
  }
  /**
   * Tags for this dimension. Examples: "default", "preview", "deprecated".
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosVersion::class, 'Google_Service_Testing_IosVersion');
