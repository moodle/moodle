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

namespace Google\Service\Dfareporting;

class Metro extends \Google\Model
{
  /**
   * Country code of the country to which this metro region belongs.
   *
   * @var string
   */
  public $countryCode;
  /**
   * DART ID of the country to which this metro region belongs.
   *
   * @var string
   */
  public $countryDartId;
  /**
   * DART ID of this metro region.
   *
   * @var string
   */
  public $dartId;
  /**
   * DMA ID of this metro region. This is the ID used for targeting and
   * generating reports, and is equivalent to metro_code.
   *
   * @var string
   */
  public $dmaId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#metro".
   *
   * @var string
   */
  public $kind;
  /**
   * Metro code of this metro region. This is equivalent to dma_id.
   *
   * @var string
   */
  public $metroCode;
  /**
   * Name of this metro region.
   *
   * @var string
   */
  public $name;

  /**
   * Country code of the country to which this metro region belongs.
   *
   * @param string $countryCode
   */
  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }
  /**
   * @return string
   */
  public function getCountryCode()
  {
    return $this->countryCode;
  }
  /**
   * DART ID of the country to which this metro region belongs.
   *
   * @param string $countryDartId
   */
  public function setCountryDartId($countryDartId)
  {
    $this->countryDartId = $countryDartId;
  }
  /**
   * @return string
   */
  public function getCountryDartId()
  {
    return $this->countryDartId;
  }
  /**
   * DART ID of this metro region.
   *
   * @param string $dartId
   */
  public function setDartId($dartId)
  {
    $this->dartId = $dartId;
  }
  /**
   * @return string
   */
  public function getDartId()
  {
    return $this->dartId;
  }
  /**
   * DMA ID of this metro region. This is the ID used for targeting and
   * generating reports, and is equivalent to metro_code.
   *
   * @param string $dmaId
   */
  public function setDmaId($dmaId)
  {
    $this->dmaId = $dmaId;
  }
  /**
   * @return string
   */
  public function getDmaId()
  {
    return $this->dmaId;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#metro".
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
   * Metro code of this metro region. This is equivalent to dma_id.
   *
   * @param string $metroCode
   */
  public function setMetroCode($metroCode)
  {
    $this->metroCode = $metroCode;
  }
  /**
   * @return string
   */
  public function getMetroCode()
  {
    return $this->metroCode;
  }
  /**
   * Name of this metro region.
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
class_alias(Metro::class, 'Google_Service_Dfareporting_Metro');
