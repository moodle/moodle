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

namespace Google\Service\ManufacturerCenter;

class Issue extends \Google\Collection
{
  /**
   * Unspecified resolution, never used.
   */
  public const RESOLUTION_RESOLUTION_UNSPECIFIED = 'RESOLUTION_UNSPECIFIED';
  /**
   * The user who provided the data must act in order to resolve the issue (for
   * example by correcting some data).
   */
  public const RESOLUTION_USER_ACTION = 'USER_ACTION';
  /**
   * The issue will be resolved automatically (for example image crawl or Google
   * review). No action is required now. Resolution might lead to another issue
   * (for example if crawl fails).
   */
  public const RESOLUTION_PENDING_PROCESSING = 'PENDING_PROCESSING';
  /**
   * Unspecified severity, never used.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Error severity. The issue prevents the usage of the whole item.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Warning severity. The issue is either one that prevents the usage of the
   * attribute that triggered it or one that will soon prevent the usage of the
   * whole item.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Info severity. The issue is one that doesn't require immediate attention.
   * It is, for example, used to communicate which attributes are still pending
   * review.
   */
  public const SEVERITY_INFO = 'INFO';
  protected $collection_key = 'applicableCountries';
  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where issue applies
   * to the manufacturer product.
   *
   * @var string[]
   */
  public $applicableCountries;
  /**
   * If present, the attribute that triggered the issue. For more information
   * about attributes, see
   * https://support.google.com/manufacturers/answer/6124116.
   *
   * @var string
   */
  public $attribute;
  /**
   * Longer description of the issue focused on how to resolve it.
   *
   * @var string
   */
  public $description;
  /**
   * The destination this issue applies to.
   *
   * @var string
   */
  public $destination;
  /**
   * What needs to happen to resolve the issue.
   *
   * @var string
   */
  public $resolution;
  /**
   * The severity of the issue.
   *
   * @var string
   */
  public $severity;
  /**
   * The timestamp when this issue appeared.
   *
   * @var string
   */
  public $timestamp;
  /**
   * Short title describing the nature of the issue.
   *
   * @var string
   */
  public $title;
  /**
   * The server-generated type of the issue, for example,
   * “INCORRECT_TEXT_FORMATTING”, “IMAGE_NOT_SERVEABLE”, etc.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. List of country codes (ISO 3166-1 alpha-2) where issue applies
   * to the manufacturer product.
   *
   * @param string[] $applicableCountries
   */
  public function setApplicableCountries($applicableCountries)
  {
    $this->applicableCountries = $applicableCountries;
  }
  /**
   * @return string[]
   */
  public function getApplicableCountries()
  {
    return $this->applicableCountries;
  }
  /**
   * If present, the attribute that triggered the issue. For more information
   * about attributes, see
   * https://support.google.com/manufacturers/answer/6124116.
   *
   * @param string $attribute
   */
  public function setAttribute($attribute)
  {
    $this->attribute = $attribute;
  }
  /**
   * @return string
   */
  public function getAttribute()
  {
    return $this->attribute;
  }
  /**
   * Longer description of the issue focused on how to resolve it.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The destination this issue applies to.
   *
   * @param string $destination
   */
  public function setDestination($destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return string
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * What needs to happen to resolve the issue.
   *
   * Accepted values: RESOLUTION_UNSPECIFIED, USER_ACTION, PENDING_PROCESSING
   *
   * @param self::RESOLUTION_* $resolution
   */
  public function setResolution($resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return self::RESOLUTION_*
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * The severity of the issue.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, ERROR, WARNING, INFO
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The timestamp when this issue appeared.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  /**
   * Short title describing the nature of the issue.
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
   * The server-generated type of the issue, for example,
   * “INCORRECT_TEXT_FORMATTING”, “IMAGE_NOT_SERVEABLE”, etc.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Issue::class, 'Google_Service_ManufacturerCenter_Issue');
