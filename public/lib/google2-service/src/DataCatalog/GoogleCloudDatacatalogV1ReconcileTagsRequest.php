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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1ReconcileTagsRequest extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * @var bool
   */
  public $forceDeleteMissing;
  /**
   * Required. The name of the tag template, which is used for reconciliation.
   *
   * @var string
   */
  public $tagTemplate;
  protected $tagsType = GoogleCloudDatacatalogV1Tag::class;
  protected $tagsDataType = 'array';

  /**
   * @param bool $forceDeleteMissing
   */
  public function setForceDeleteMissing($forceDeleteMissing)
  {
    $this->forceDeleteMissing = $forceDeleteMissing;
  }
  /**
   * @return bool
   */
  public function getForceDeleteMissing()
  {
    return $this->forceDeleteMissing;
  }
  /**
   * Required. The name of the tag template, which is used for reconciliation.
   *
   * @param string $tagTemplate
   */
  public function setTagTemplate($tagTemplate)
  {
    $this->tagTemplate = $tagTemplate;
  }
  /**
   * @return string
   */
  public function getTagTemplate()
  {
    return $this->tagTemplate;
  }
  /**
   * A list of tags to apply to an entry. A tag can specify a tag template,
   * which must be the template specified in the `ReconcileTagsRequest`. The
   * sole entry and each of its columns must be mentioned at most once.
   *
   * @param GoogleCloudDatacatalogV1Tag[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return GoogleCloudDatacatalogV1Tag[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1ReconcileTagsRequest::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1ReconcileTagsRequest');
