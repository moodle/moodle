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

namespace Google\Service\YouTube;

class Activity extends \Google\Model
{
  protected $contentDetailsType = ActivityContentDetails::class;
  protected $contentDetailsDataType = '';
  /**
   * Etag of this resource
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the activity.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#activity".
   *
   * @var string
   */
  public $kind;
  protected $snippetType = ActivitySnippet::class;
  protected $snippetDataType = '';

  /**
   * The contentDetails object contains information about the content associated
   * with the activity. For example, if the snippet.type value is videoRated,
   * then the contentDetails object's content identifies the rated video.
   *
   * @param ActivityContentDetails $contentDetails
   */
  public function setContentDetails(ActivityContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return ActivityContentDetails
   */
  public function getContentDetails()
  {
    return $this->contentDetails;
  }
  /**
   * Etag of this resource
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
   * The ID that YouTube uses to uniquely identify the activity.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#activity".
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
   * The snippet object contains basic details about the activity, including the
   * activity's type and group ID.
   *
   * @param ActivitySnippet $snippet
   */
  public function setSnippet(ActivitySnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return ActivitySnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Activity::class, 'Google_Service_YouTube_Activity');
