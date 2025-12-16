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

namespace Google\Service\Games;

class EventDefinition extends \Google\Collection
{
  /**
   * This event should be visible to all users.
   */
  public const VISIBILITY_REVEALED = 'REVEALED';
  /**
   * This event should only be shown to users that have recorded this event at
   * least once.
   */
  public const VISIBILITY_HIDDEN = 'HIDDEN';
  protected $collection_key = 'childEvents';
  protected $childEventsType = EventChild::class;
  protected $childEventsDataType = 'array';
  /**
   * Description of what this event represents.
   *
   * @var string
   */
  public $description;
  /**
   * The name to display for the event.
   *
   * @var string
   */
  public $displayName;
  /**
   * The ID of the event.
   *
   * @var string
   */
  public $id;
  /**
   * The base URL for the image that represents the event.
   *
   * @var string
   */
  public $imageUrl;
  /**
   * Indicates whether the icon image being returned is a default image, or is
   * game-provided.
   *
   * @var bool
   */
  public $isDefaultImageUrl;
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventDefinition`.
   *
   * @var string
   */
  public $kind;
  /**
   * The visibility of event being tracked in this definition.
   *
   * @var string
   */
  public $visibility;

  /**
   * A list of events that are a child of this event.
   *
   * @param EventChild[] $childEvents
   */
  public function setChildEvents($childEvents)
  {
    $this->childEvents = $childEvents;
  }
  /**
   * @return EventChild[]
   */
  public function getChildEvents()
  {
    return $this->childEvents;
  }
  /**
   * Description of what this event represents.
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
   * The name to display for the event.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The ID of the event.
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
   * The base URL for the image that represents the event.
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * Indicates whether the icon image being returned is a default image, or is
   * game-provided.
   *
   * @param bool $isDefaultImageUrl
   */
  public function setIsDefaultImageUrl($isDefaultImageUrl)
  {
    $this->isDefaultImageUrl = $isDefaultImageUrl;
  }
  /**
   * @return bool
   */
  public function getIsDefaultImageUrl()
  {
    return $this->isDefaultImageUrl;
  }
  /**
   * Uniquely identifies the type of this resource. Value is always the fixed
   * string `games#eventDefinition`.
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
   * The visibility of event being tracked in this definition.
   *
   * Accepted values: REVEALED, HIDDEN
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventDefinition::class, 'Google_Service_Games_EventDefinition');
