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

namespace Google\Service\Compute;

class CacheInvalidationRule extends \Google\Collection
{
  protected $collection_key = 'cacheTags';
  /**
   * A list of cache tags used to identify cached objects.
   *
   *        - Cache tags are specified when the response is first cached, by
   * setting    the `Cache-Tag` response header at the origin.    - Multiple
   * cache tags in the same invalidation request are treated as    Boolean `OR`
   * - for example, `tag1 OR tag2 OR tag3`.    - If other fields are also
   * specified, these are treated as Boolean `AND`    with any tags.
   *
   * Up to 10 tags can be specified in a single invalidation request.
   *
   * @var string[]
   */
  public $cacheTags;
  /**
   * If set, this invalidation rule will only apply to requests with a Host
   * header matching host.
   *
   * @var string
   */
  public $host;
  /**
   * @var string
   */
  public $path;

  /**
   * A list of cache tags used to identify cached objects.
   *
   *        - Cache tags are specified when the response is first cached, by
   * setting    the `Cache-Tag` response header at the origin.    - Multiple
   * cache tags in the same invalidation request are treated as    Boolean `OR`
   * - for example, `tag1 OR tag2 OR tag3`.    - If other fields are also
   * specified, these are treated as Boolean `AND`    with any tags.
   *
   * Up to 10 tags can be specified in a single invalidation request.
   *
   * @param string[] $cacheTags
   */
  public function setCacheTags($cacheTags)
  {
    $this->cacheTags = $cacheTags;
  }
  /**
   * @return string[]
   */
  public function getCacheTags()
  {
    return $this->cacheTags;
  }
  /**
   * If set, this invalidation rule will only apply to requests with a Host
   * header matching host.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CacheInvalidationRule::class, 'Google_Service_Compute_CacheInvalidationRule');
