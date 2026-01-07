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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1Entity extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Person.
   */
  public const TYPE_PERSON = 'PERSON';
  /**
   * Location.
   */
  public const TYPE_LOCATION = 'LOCATION';
  /**
   * Organization.
   */
  public const TYPE_ORGANIZATION = 'ORGANIZATION';
  /**
   * Event.
   */
  public const TYPE_EVENT = 'EVENT';
  /**
   * Artwork.
   */
  public const TYPE_WORK_OF_ART = 'WORK_OF_ART';
  /**
   * Consumer product.
   */
  public const TYPE_CONSUMER_GOOD = 'CONSUMER_GOOD';
  /**
   * Other types of entities.
   */
  public const TYPE_OTHER = 'OTHER';
  /**
   * Phone number. The metadata lists the phone number (formatted according to
   * local convention), plus whichever additional elements appear in the text: *
   * `number` - The actual number, broken down into sections according to local
   * convention. * `national_prefix` - Country code, if detected. * `area_code`
   * - Region or area code, if detected. * `extension` - Phone extension (to be
   * dialed after connection), if detected.
   */
  public const TYPE_PHONE_NUMBER = 'PHONE_NUMBER';
  /**
   * Address. The metadata identifies the street number and locality plus
   * whichever additional elements appear in the text: * `street_number` -
   * Street number. * `locality` - City or town. * `street_name` - Street/route
   * name, if detected. * `postal_code` - Postal code, if detected. * `country`
   * - Country, if detected. * `broad_region` - Administrative area, such as the
   * state, if detected. * `narrow_region` - Smaller administrative area, such
   * as county, if detected. * `sublocality` - Used in Asian addresses to demark
   * a district within a city, if detected.
   */
  public const TYPE_ADDRESS = 'ADDRESS';
  /**
   * Date. The metadata identifies the components of the date: * `year` - Four
   * digit year, if detected. * `month` - Two digit month number, if detected. *
   * `day` - Two digit day number, if detected.
   */
  public const TYPE_DATE = 'DATE';
  /**
   * Number. The metadata is the number itself.
   */
  public const TYPE_NUMBER = 'NUMBER';
  /**
   * Price. The metadata identifies the `value` and `currency`.
   */
  public const TYPE_PRICE = 'PRICE';
  /**
   * The representative name for the entity.
   *
   * @var string
   */
  public $displayName;
  /**
   * Metadata associated with the entity. For most entity types, the metadata is
   * a Wikipedia URL (`wikipedia_url`) and Knowledge Graph MID (`mid`), if they
   * are available. For the metadata associated with other entity types, see the
   * Type table below.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * The salience score associated with the entity in the [0, 1.0] range. The
   * salience score for an entity provides information about the importance or
   * centrality of that entity to the entire document text. Scores closer to 0
   * are less salient, while scores closer to 1.0 are highly salient.
   *
   * @var float
   */
  public $salience;
  protected $sentimentType = GoogleCloudContactcenterinsightsV1alpha1SentimentData::class;
  protected $sentimentDataType = '';
  /**
   * The entity type.
   *
   * @var string
   */
  public $type;

  /**
   * The representative name for the entity.
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
   * Metadata associated with the entity. For most entity types, the metadata is
   * a Wikipedia URL (`wikipedia_url`) and Knowledge Graph MID (`mid`), if they
   * are available. For the metadata associated with other entity types, see the
   * Type table below.
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
   * The salience score associated with the entity in the [0, 1.0] range. The
   * salience score for an entity provides information about the importance or
   * centrality of that entity to the entire document text. Scores closer to 0
   * are less salient, while scores closer to 1.0 are highly salient.
   *
   * @param float $salience
   */
  public function setSalience($salience)
  {
    $this->salience = $salience;
  }
  /**
   * @return float
   */
  public function getSalience()
  {
    return $this->salience;
  }
  /**
   * The aggregate sentiment expressed for this entity in the conversation.
   *
   * @param GoogleCloudContactcenterinsightsV1alpha1SentimentData $sentiment
   */
  public function setSentiment(GoogleCloudContactcenterinsightsV1alpha1SentimentData $sentiment)
  {
    $this->sentiment = $sentiment;
  }
  /**
   * @return GoogleCloudContactcenterinsightsV1alpha1SentimentData
   */
  public function getSentiment()
  {
    return $this->sentiment;
  }
  /**
   * The entity type.
   *
   * Accepted values: TYPE_UNSPECIFIED, PERSON, LOCATION, ORGANIZATION, EVENT,
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
class_alias(GoogleCloudContactcenterinsightsV1alpha1Entity::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1Entity');
