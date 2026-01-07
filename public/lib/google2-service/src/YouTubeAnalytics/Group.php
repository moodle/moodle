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

namespace Google\Service\YouTubeAnalytics;

class Group extends \Google\Model
{
  protected $contentDetailsType = GroupContentDetails::class;
  protected $contentDetailsDataType = '';
  protected $errorsType = Errors::class;
  protected $errorsDataType = '';
  /**
   * The Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The ID that YouTube uses to uniquely identify the group.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies the API resource's type. The value will be `youtube#group`.
   *
   * @var string
   */
  public $kind;
  protected $snippetType = GroupSnippet::class;
  protected $snippetDataType = '';

  /**
   * The `contentDetails` object contains additional information about the
   * group, such as the number and type of items that it contains.
   *
   * @param GroupContentDetails $contentDetails
   */
  public function setContentDetails(GroupContentDetails $contentDetails)
  {
    $this->contentDetails = $contentDetails;
  }
  /**
   * @return GroupContentDetails
   */
  public function getContentDetails()
  {
    return $this->contentDetails;
  }
  /**
   * Apiary error details
   *
   * @param Errors $errors
   */
  public function setErrors(Errors $errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Errors
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * The Etag of this resource.
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
   * The ID that YouTube uses to uniquely identify the group.
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
   * Identifies the API resource's type. The value will be `youtube#group`.
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
   * The `snippet` object contains basic information about the group, including
   * its creation date and name.
   *
   * @param GroupSnippet $snippet
   */
  public function setSnippet(GroupSnippet $snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return GroupSnippet
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Group::class, 'Google_Service_YouTubeAnalytics_Group');
