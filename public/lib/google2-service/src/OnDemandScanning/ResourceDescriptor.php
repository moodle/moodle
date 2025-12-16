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

namespace Google\Service\OnDemandScanning;

class ResourceDescriptor extends \Google\Model
{
  /**
   * @var array[]
   */
  public $annotations;
  /**
   * @var string
   */
  public $content;
  /**
   * @var string[]
   */
  public $digest;
  /**
   * @var string
   */
  public $downloadLocation;
  /**
   * @var string
   */
  public $mediaType;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $uri;

  /**
   * @param array[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return array[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * @param string[] $digest
   */
  public function setDigest($digest)
  {
    $this->digest = $digest;
  }
  /**
   * @return string[]
   */
  public function getDigest()
  {
    return $this->digest;
  }
  /**
   * @param string $downloadLocation
   */
  public function setDownloadLocation($downloadLocation)
  {
    $this->downloadLocation = $downloadLocation;
  }
  /**
   * @return string
   */
  public function getDownloadLocation()
  {
    return $this->downloadLocation;
  }
  /**
   * @param string $mediaType
   */
  public function setMediaType($mediaType)
  {
    $this->mediaType = $mediaType;
  }
  /**
   * @return string
   */
  public function getMediaType()
  {
    return $this->mediaType;
  }
  /**
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
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceDescriptor::class, 'Google_Service_OnDemandScanning_ResourceDescriptor');
