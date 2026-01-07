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

namespace Google\Service\Vision;

class Feature extends \Google\Model
{
  /**
   * Unspecified feature type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Run face detection.
   */
  public const TYPE_FACE_DETECTION = 'FACE_DETECTION';
  /**
   * Run landmark detection.
   */
  public const TYPE_LANDMARK_DETECTION = 'LANDMARK_DETECTION';
  /**
   * Run logo detection.
   */
  public const TYPE_LOGO_DETECTION = 'LOGO_DETECTION';
  /**
   * Run label detection.
   */
  public const TYPE_LABEL_DETECTION = 'LABEL_DETECTION';
  /**
   * Run text detection / optical character recognition (OCR). Text detection is
   * optimized for areas of text within a larger image; if the image is a
   * document, use `DOCUMENT_TEXT_DETECTION` instead.
   */
  public const TYPE_TEXT_DETECTION = 'TEXT_DETECTION';
  /**
   * Run dense text document OCR. Takes precedence when both
   * `DOCUMENT_TEXT_DETECTION` and `TEXT_DETECTION` are present.
   */
  public const TYPE_DOCUMENT_TEXT_DETECTION = 'DOCUMENT_TEXT_DETECTION';
  /**
   * Run Safe Search to detect potentially unsafe or undesirable content.
   */
  public const TYPE_SAFE_SEARCH_DETECTION = 'SAFE_SEARCH_DETECTION';
  /**
   * Compute a set of image properties, such as the image's dominant colors.
   */
  public const TYPE_IMAGE_PROPERTIES = 'IMAGE_PROPERTIES';
  /**
   * Run crop hints.
   */
  public const TYPE_CROP_HINTS = 'CROP_HINTS';
  /**
   * Run web detection.
   */
  public const TYPE_WEB_DETECTION = 'WEB_DETECTION';
  /**
   * Run Product Search.
   */
  public const TYPE_PRODUCT_SEARCH = 'PRODUCT_SEARCH';
  /**
   * Run localizer for object detection.
   */
  public const TYPE_OBJECT_LOCALIZATION = 'OBJECT_LOCALIZATION';
  /**
   * Maximum number of results of this type. Does not apply to `TEXT_DETECTION`,
   * `DOCUMENT_TEXT_DETECTION`, or `CROP_HINTS`.
   *
   * @var int
   */
  public $maxResults;
  /**
   * Model to use for the feature. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest". `DOCUMENT_TEXT_DETECTION` and
   * `TEXT_DETECTION` also support "builtin/rc" for the latest release
   * candidate.
   *
   * @var string
   */
  public $model;
  /**
   * The feature type.
   *
   * @var string
   */
  public $type;

  /**
   * Maximum number of results of this type. Does not apply to `TEXT_DETECTION`,
   * `DOCUMENT_TEXT_DETECTION`, or `CROP_HINTS`.
   *
   * @param int $maxResults
   */
  public function setMaxResults($maxResults)
  {
    $this->maxResults = $maxResults;
  }
  /**
   * @return int
   */
  public function getMaxResults()
  {
    return $this->maxResults;
  }
  /**
   * Model to use for the feature. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest". `DOCUMENT_TEXT_DETECTION` and
   * `TEXT_DETECTION` also support "builtin/rc" for the latest release
   * candidate.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * The feature type.
   *
   * Accepted values: TYPE_UNSPECIFIED, FACE_DETECTION, LANDMARK_DETECTION,
   * LOGO_DETECTION, LABEL_DETECTION, TEXT_DETECTION, DOCUMENT_TEXT_DETECTION,
   * SAFE_SEARCH_DETECTION, IMAGE_PROPERTIES, CROP_HINTS, WEB_DETECTION,
   * PRODUCT_SEARCH, OBJECT_LOCALIZATION
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Feature::class, 'Google_Service_Vision_Feature');
