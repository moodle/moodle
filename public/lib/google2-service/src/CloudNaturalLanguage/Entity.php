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

namespace Google\Service\CloudNaturalLanguage;

class Entity extends \Google\Collection
{
  /**
   * Unknown
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Person
   */
  public const TYPE_PERSON = 'PERSON';
  /**
   * Location
   */
  public const TYPE_LOCATION = 'LOCATION';
  /**
   * Organization
   */
  public const TYPE_ORGANIZATION = 'ORGANIZATION';
  /**
   * Event
   */
  public const TYPE_EVENT = 'EVENT';
  /**
   * Artwork
   */
  public const TYPE_WORK_OF_ART = 'WORK_OF_ART';
  /**
   * Consumer product
   */
  public const TYPE_CONSUMER_GOOD = 'CONSUMER_GOOD';
  /**
   * Other types of entities
   */
  public const TYPE_OTHER = 'OTHER';
  /**
   * Phone number The metadata lists the phone number, formatted according to
   * local convention, plus whichever additional elements appear in the text: *
   * `number` - the actual number, broken down into sections as per local
   * convention * `national_prefix` - country code, if detected * `area_code` -
   * region or area code, if detected * `extension` - phone extension (to be
   * dialed after connection), if detected
   */
  public const TYPE_PHONE_NUMBER = 'PHONE_NUMBER';
  /**
   * Address The metadata identifies the street number and locality plus
   * whichever additional elements appear in the text: * `street_number` -
   * street number * `locality` - city or town * `street_name` - street/route
   * name, if detected * `postal_code` - postal code, if detected * `country` -
   * country, if detected * `broad_region` - administrative area, such as the
   * state, if detected * `narrow_region` - smaller administrative area, such as
   * county, if detected * `sublocality` - used in Asian addresses to demark a
   * district within a city, if detected
   */
  public const TYPE_ADDRESS = 'ADDRESS';
  /**
   * Date The metadata identifies the components of the date: * `year` - four
   * digit year, if detected * `month` - two digit month number, if detected *
   * `day` - two digit day number, if detected
   */
  public const TYPE_DATE = 'DATE';
  /**
   * Number The metadata is the number itself.
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * Price The metadata identifies the `value` and `currency`.
   */
  public const TYPE_PRICE = 'PRICE';
  protected $collection_key = 'mentions';
  protected $mentionsType = EntityMention::class;
  protected $mentionsDataType = 'array';
  /**
   * Metadata associated with the entity. For the metadata associated with other
   * entity types, see the Type table below.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The representative name for the entity.
   *
   * @var string
   */
  public $name;
  protected $sentimentType = Sentiment::class;
  protected $sentimentDataType = '';
  /**
   * The entity type.
   *
   * @var string
   */
  public $type;

  /**
   * The mentions of this entity in the input document. The API currently
   * supports proper noun mentions.
   *
   * @param EntityMention[] $mentions
   */
  public function setMentions($mentions)
  {
    $this->mentions = $mentions;
  }
  /**
   * @return EntityMention[]
   */
  public function getMentions()
  {
    return $this->mentions;
  }
  /**
   * Metadata associated with the entity. For the metadata associated with other
   * entity types, see the Type table below.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The representative name for the entity.
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
   * For calls to AnalyzeEntitySentiment this field will contain the aggregate
   * sentiment expressed for this entity in the provided document.
   *
   * @param Sentiment $sentiment
   */
  public function setSentiment(Sentiment $sentiment)
  {
    $this->sentiment = $sentiment;
  }
  /**
   * @return Sentiment
   */
  public function getSentiment()
  {
    return $this->sentiment;
  }
  /**
   * The entity type.
   *
   * Accepted values: UNKNOWN, PERSON, LOCATION, ORGANIZATION, EVENT,
   * WORK_OF_ART, CONSUMER_GOOD, OTHER, PHONE_NUMBER, ADDRESS, DATE, NUMBER,
   * PRICE
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
class_alias(Entity::class, 'Google_Service_CloudNaturalLanguage_Entity');
