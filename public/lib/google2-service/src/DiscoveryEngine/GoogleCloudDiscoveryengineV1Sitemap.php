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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1Sitemap extends \Google\Model
{
  /**
   * Output only. The sitemap's creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The fully qualified resource name of the sitemap.
   * `projects/locations/collections/dataStores/siteSearchEngine/sitemaps` The
   * `sitemap_id` suffix is system-generated.
   *
   * @var string
   */
  public $name;
  /**
   * Public URI for the sitemap, e.g. `www.example.com/sitemap.xml`.
   *
   * @var string
   */
  public $uri;

  /**
   * Output only. The sitemap's creation time.
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
   * Output only. The fully qualified resource name of the sitemap.
   * `projects/locations/collections/dataStores/siteSearchEngine/sitemaps` The
   * `sitemap_id` suffix is system-generated.
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
   * Public URI for the sitemap, e.g. `www.example.com/sitemap.xml`.
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
class_alias(GoogleCloudDiscoveryengineV1Sitemap::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Sitemap');
