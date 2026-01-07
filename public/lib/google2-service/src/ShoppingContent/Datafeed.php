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

namespace Google\Service\ShoppingContent;

class Datafeed extends \Google\Collection
{
  protected $collection_key = 'targets';
  /**
   * The two-letter ISO 639-1 language in which the attributes are defined in
   * the data feed.
   *
   * @var string
   */
  public $attributeLanguage;
  /**
   * Required. The type of data feed. For product inventory feeds, only feeds
   * for local stores, not online stores, are supported. Acceptable values are:
   * - "`local products`" - "`product inventory`" - "`products`"
   *
   * @var string
   */
  public $contentType;
  protected $fetchScheduleType = DatafeedFetchSchedule::class;
  protected $fetchScheduleDataType = '';
  /**
   * Required. The filename of the feed. All feeds must have a unique file name.
   *
   * @var string
   */
  public $fileName;
  protected $formatType = DatafeedFormat::class;
  protected $formatDataType = '';
  /**
   * Required for update. The ID of the data feed.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#datafeed`"
   *
   * @var string
   */
  public $kind;
  /**
   * Required for insert. A descriptive name of the data feed.
   *
   * @var string
   */
  public $name;
  protected $targetsType = DatafeedTarget::class;
  protected $targetsDataType = 'array';

  /**
   * The two-letter ISO 639-1 language in which the attributes are defined in
   * the data feed.
   *
   * @param string $attributeLanguage
   */
  public function setAttributeLanguage($attributeLanguage)
  {
    $this->attributeLanguage = $attributeLanguage;
  }
  /**
   * @return string
   */
  public function getAttributeLanguage()
  {
    return $this->attributeLanguage;
  }
  /**
   * Required. The type of data feed. For product inventory feeds, only feeds
   * for local stores, not online stores, are supported. Acceptable values are:
   * - "`local products`" - "`product inventory`" - "`products`"
   *
   * @param string $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return string
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * Fetch schedule for the feed file.
   *
   * @param DatafeedFetchSchedule $fetchSchedule
   */
  public function setFetchSchedule(DatafeedFetchSchedule $fetchSchedule)
  {
    $this->fetchSchedule = $fetchSchedule;
  }
  /**
   * @return DatafeedFetchSchedule
   */
  public function getFetchSchedule()
  {
    return $this->fetchSchedule;
  }
  /**
   * Required. The filename of the feed. All feeds must have a unique file name.
   *
   * @param string $fileName
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  /**
   * @return string
   */
  public function getFileName()
  {
    return $this->fileName;
  }
  /**
   * Format of the feed file.
   *
   * @param DatafeedFormat $format
   */
  public function setFormat(DatafeedFormat $format)
  {
    $this->format = $format;
  }
  /**
   * @return DatafeedFormat
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * Required for update. The ID of the data feed.
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
   * "`content#datafeed`"
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
   * Required for insert. A descriptive name of the data feed.
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
   * The targets this feed should apply to (country, language, destinations).
   *
   * @param DatafeedTarget[] $targets
   */
  public function setTargets($targets)
  {
    $this->targets = $targets;
  }
  /**
   * @return DatafeedTarget[]
   */
  public function getTargets()
  {
    return $this->targets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Datafeed::class, 'Google_Service_ShoppingContent_Datafeed');
