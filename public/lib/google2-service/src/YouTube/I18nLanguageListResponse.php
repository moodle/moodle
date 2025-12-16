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

class I18nLanguageListResponse extends \Google\Collection
{
  protected $collection_key = 'items';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Serialized EventId of the request which produced this response.
   *
   * @var string
   */
  public $eventId;
  protected $itemsType = I18nLanguage::class;
  protected $itemsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#i18nLanguageListResponse".
   *
   * @var string
   */
  public $kind;
  /**
   * The visitorId identifies the visitor.
   *
   * @var string
   */
  public $visitorId;

  /**
   * Etag of this resource.
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
   * Serialized EventId of the request which produced this response.
   *
   * @param string $eventId
   */
  public function setEventId($eventId)
  {
    $this->eventId = $eventId;
  }
  /**
   * @return string
   */
  public function getEventId()
  {
    return $this->eventId;
  }
  /**
   * A list of supported i18n languages. In this map, the i18n language ID is
   * the map key, and its value is the corresponding i18nLanguage resource.
   *
   * @param I18nLanguage[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return I18nLanguage[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "youtube#i18nLanguageListResponse".
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
   * The visitorId identifies the visitor.
   *
   * @param string $visitorId
   */
  public function setVisitorId($visitorId)
  {
    $this->visitorId = $visitorId;
  }
  /**
   * @return string
   */
  public function getVisitorId()
  {
    return $this->visitorId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(I18nLanguageListResponse::class, 'Google_Service_YouTube_I18nLanguageListResponse');
