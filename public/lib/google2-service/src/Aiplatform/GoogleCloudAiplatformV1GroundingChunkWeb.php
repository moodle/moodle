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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1GroundingChunkWeb extends \Google\Model
{
  /**
   * The domain of the web page that contains the evidence. This can be used to
   * filter out low-quality sources.
   *
   * @var string
   */
  public $domain;
  /**
   * The title of the web page that contains the evidence.
   *
   * @var string
   */
  public $title;
  /**
   * The URI of the web page that contains the evidence.
   *
   * @var string
   */
  public $uri;

  /**
   * The domain of the web page that contains the evidence. This can be used to
   * filter out low-quality sources.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * The title of the web page that contains the evidence.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The URI of the web page that contains the evidence.
   *
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
class_alias(GoogleCloudAiplatformV1GroundingChunkWeb::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GroundingChunkWeb');
