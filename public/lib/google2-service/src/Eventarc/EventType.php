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

namespace Google\Service\Eventarc;

class EventType extends \Google\Collection
{
  protected $collection_key = 'filteringAttributes';
  /**
   * Output only. Human friendly description of what the event type is about.
   * For example "Bucket created in Cloud Storage".
   *
   * @var string
   */
  public $description;
  /**
   * Output only. URI for the event schema. For example
   * "https://github.com/googleapis/google-
   * cloudevents/blob/master/proto/google/events/cloud/storage/v1/events.proto"
   *
   * @var string
   */
  public $eventSchemaUri;
  protected $filteringAttributesType = FilteringAttribute::class;
  protected $filteringAttributesDataType = 'array';
  /**
   * Output only. The full name of the event type (for example,
   * "google.cloud.storage.object.v1.finalized"). In the form of {provider-
   * specific-prefix}.{resource}.{version}.{verb}. Types MUST be versioned and
   * event schemas are guaranteed to remain backward compatible within one
   * version. Note that event type versions and API versions do not need to
   * match.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. Human friendly description of what the event type is about.
   * For example "Bucket created in Cloud Storage".
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
   * Output only. URI for the event schema. For example
   * "https://github.com/googleapis/google-
   * cloudevents/blob/master/proto/google/events/cloud/storage/v1/events.proto"
   *
   * @param string $eventSchemaUri
   */
  public function setEventSchemaUri($eventSchemaUri)
  {
    $this->eventSchemaUri = $eventSchemaUri;
  }
  /**
   * @return string
   */
  public function getEventSchemaUri()
  {
    return $this->eventSchemaUri;
  }
  /**
   * Output only. Filtering attributes for the event type.
   *
   * @param FilteringAttribute[] $filteringAttributes
   */
  public function setFilteringAttributes($filteringAttributes)
  {
    $this->filteringAttributes = $filteringAttributes;
  }
  /**
   * @return FilteringAttribute[]
   */
  public function getFilteringAttributes()
  {
    return $this->filteringAttributes;
  }
  /**
   * Output only. The full name of the event type (for example,
   * "google.cloud.storage.object.v1.finalized"). In the form of {provider-
   * specific-prefix}.{resource}.{version}.{verb}. Types MUST be versioned and
   * event schemas are guaranteed to remain backward compatible within one
   * version. Note that event type versions and API versions do not need to
   * match.
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
class_alias(EventType::class, 'Google_Service_Eventarc_EventType');
