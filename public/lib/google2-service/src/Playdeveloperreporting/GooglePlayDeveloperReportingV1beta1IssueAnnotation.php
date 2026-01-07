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

namespace Google\Service\Playdeveloperreporting;

class GooglePlayDeveloperReportingV1beta1IssueAnnotation extends \Google\Model
{
  /**
   * Contains the contents of the annotation message.
   *
   * @var string
   */
  public $body;
  /**
   * Category that the annotation belongs to. An annotation will belong to a
   * single category. Example categories: "Potential fix", "Insight".
   *
   * @var string
   */
  public $category;
  /**
   * Title for the annotation.
   *
   * @var string
   */
  public $title;

  /**
   * Contains the contents of the annotation message.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Category that the annotation belongs to. An annotation will belong to a
   * single category. Example categories: "Potential fix", "Insight".
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Title for the annotation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePlayDeveloperReportingV1beta1IssueAnnotation::class, 'Google_Service_Playdeveloperreporting_GooglePlayDeveloperReportingV1beta1IssueAnnotation');
