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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1StyleGuide extends \Google\Model
{
  /**
   * Linter type unspecified.
   */
  public const LINTER_LINTER_UNSPECIFIED = 'LINTER_UNSPECIFIED';
  /**
   * Linter type spectral.
   */
  public const LINTER_SPECTRAL = 'SPECTRAL';
  /**
   * Linter type other.
   */
  public const LINTER_OTHER = 'OTHER';
  protected $contentsType = GoogleCloudApihubV1StyleGuideContents::class;
  protected $contentsDataType = '';
  /**
   * Required. Target linter for the style guide.
   *
   * @var string
   */
  public $linter;
  /**
   * Identifier. The name of the style guide. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}/styleGuide`
   *
   * @var string
   */
  public $name;

  /**
   * Required. Input only. The contents of the uploaded style guide.
   *
   * @param GoogleCloudApihubV1StyleGuideContents $contents
   */
  public function setContents(GoogleCloudApihubV1StyleGuideContents $contents)
  {
    $this->contents = $contents;
  }
  /**
   * @return GoogleCloudApihubV1StyleGuideContents
   */
  public function getContents()
  {
    return $this->contents;
  }
  /**
   * Required. Target linter for the style guide.
   *
   * Accepted values: LINTER_UNSPECIFIED, SPECTRAL, OTHER
   *
   * @param self::LINTER_* $linter
   */
  public function setLinter($linter)
  {
    $this->linter = $linter;
  }
  /**
   * @return self::LINTER_*
   */
  public function getLinter()
  {
    return $this->linter;
  }
  /**
   * Identifier. The name of the style guide. Format:
   * `projects/{project}/locations/{location}/plugins/{plugin}/styleGuide`
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1StyleGuide::class, 'Google_Service_APIhub_GoogleCloudApihubV1StyleGuide');
