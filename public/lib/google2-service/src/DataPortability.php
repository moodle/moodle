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

namespace Google\Service;

use Google\Client;

/**
 * Service definition for DataPortability (v1).
 *
 * <p>
 * The Data Portability API lets you build applications that request
 * authorization from a user to move a copy of data from Google services into
 * your application. This enables data portability and facilitates switching
 * services.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/data-portability" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class DataPortability extends \Google\Service
{
  /** Move a copy of the Google Alerts subscriptions you created. */
  const DATAPORTABILITY_ALERTS_SUBSCRIPTIONS =
      "https://www.googleapis.com/auth/dataportability.alerts.subscriptions";
  /** Move a copy of the information you entered into online forms in Chrome. */
  const DATAPORTABILITY_CHROME_AUTOFILL =
      "https://www.googleapis.com/auth/dataportability.chrome.autofill";
  /** Move a copy of pages you bookmarked in Chrome. */
  const DATAPORTABILITY_CHROME_BOOKMARKS =
      "https://www.googleapis.com/auth/dataportability.chrome.bookmarks";
  /** Move a copy of words you added to Chrome's dictionary. */
  const DATAPORTABILITY_CHROME_DICTIONARY =
      "https://www.googleapis.com/auth/dataportability.chrome.dictionary";
  /** Move a copy of extensions you installed from the Chrome Web Store. */
  const DATAPORTABILITY_CHROME_EXTENSIONS =
      "https://www.googleapis.com/auth/dataportability.chrome.extensions";
  /** Move a copy of sites you visited in Chrome. */
  const DATAPORTABILITY_CHROME_HISTORY =
      "https://www.googleapis.com/auth/dataportability.chrome.history";
  /** Move a copy of pages you added to your reading list in Chrome. */
  const DATAPORTABILITY_CHROME_READING_LIST =
      "https://www.googleapis.com/auth/dataportability.chrome.reading_list";
  /** Move a copy of your settings in Chrome. */
  const DATAPORTABILITY_CHROME_SETTINGS =
      "https://www.googleapis.com/auth/dataportability.chrome.settings";
  /** Move a copy of searches and sites you follow, saved by Discover. */
  const DATAPORTABILITY_DISCOVER_FOLLOWS =
      "https://www.googleapis.com/auth/dataportability.discover.follows";
  /** Move a copy of links to your liked documents, saved by Discover. */
  const DATAPORTABILITY_DISCOVER_LIKES =
      "https://www.googleapis.com/auth/dataportability.discover.likes";
  /** Move a copy of content you marked as not interested, saved by Discover. */
  const DATAPORTABILITY_DISCOVER_NOT_INTERESTED =
      "https://www.googleapis.com/auth/dataportability.discover.not_interested";
  /** Move a copy of the places you labeled on Maps. */
  const DATAPORTABILITY_MAPS_ALIASED_PLACES =
      "https://www.googleapis.com/auth/dataportability.maps.aliased_places";
  /** Move a copy of your pinned trips on Maps. */
  const DATAPORTABILITY_MAPS_COMMUTE_ROUTES =
      "https://www.googleapis.com/auth/dataportability.maps.commute_routes";
  /** Move a copy of your commute settings on Maps. */
  const DATAPORTABILITY_MAPS_COMMUTE_SETTINGS =
      "https://www.googleapis.com/auth/dataportability.maps.commute_settings";
  /** Move a copy of your electric vehicle profile on Maps. */
  const DATAPORTABILITY_MAPS_EV_PROFILE =
      "https://www.googleapis.com/auth/dataportability.maps.ev_profile";
  /** Move a copy of the corrections you made to places or map information on Maps. */
  const DATAPORTABILITY_MAPS_FACTUAL_CONTRIBUTIONS =
      "https://www.googleapis.com/auth/dataportability.maps.factual_contributions";
  /** Move a copy of your updates to places on Maps. */
  const DATAPORTABILITY_MAPS_OFFERING_CONTRIBUTIONS =
      "https://www.googleapis.com/auth/dataportability.maps.offering_contributions";
  /** Move a copy of the photos and videos you posted on Maps. */
  const DATAPORTABILITY_MAPS_PHOTOS_VIDEOS =
      "https://www.googleapis.com/auth/dataportability.maps.photos_videos";
  /** Move a copy of the questions and answers you posted on Maps. */
  const DATAPORTABILITY_MAPS_QUESTIONS_ANSWERS =
      "https://www.googleapis.com/auth/dataportability.maps.questions_answers";
  /** Move a copy of your reviews and posts on Maps. */
  const DATAPORTABILITY_MAPS_REVIEWS =
      "https://www.googleapis.com/auth/dataportability.maps.reviews";
  /** Move a copy of your Starred places list on Maps. */
  const DATAPORTABILITY_MAPS_STARRED_PLACES =
      "https://www.googleapis.com/auth/dataportability.maps.starred_places";
  /** Move a copy of your vehicle profile on Maps. */
  const DATAPORTABILITY_MAPS_VEHICLE_PROFILE =
      "https://www.googleapis.com/auth/dataportability.maps.vehicle_profile";
  /** Move a copy of your Maps activity. */
  const DATAPORTABILITY_MYACTIVITY_MAPS =
      "https://www.googleapis.com/auth/dataportability.myactivity.maps";
  /** Move a copy of your My Ad Center activity. */
  const DATAPORTABILITY_MYACTIVITY_MYADCENTER =
      "https://www.googleapis.com/auth/dataportability.myactivity.myadcenter";
  /** Move a copy of your Google Play activity. */
  const DATAPORTABILITY_MYACTIVITY_PLAY =
      "https://www.googleapis.com/auth/dataportability.myactivity.play";
  /** Move a copy of your Google Search activity. */
  const DATAPORTABILITY_MYACTIVITY_SEARCH =
      "https://www.googleapis.com/auth/dataportability.myactivity.search";
  /** Move a copy of your Shopping activity. */
  const DATAPORTABILITY_MYACTIVITY_SHOPPING =
      "https://www.googleapis.com/auth/dataportability.myactivity.shopping";
  /** Move a copy of your YouTube activity. */
  const DATAPORTABILITY_MYACTIVITY_YOUTUBE =
      "https://www.googleapis.com/auth/dataportability.myactivity.youtube";
  /** Move a copy of the maps you created in My Maps. */
  const DATAPORTABILITY_MYMAPS_MAPS =
      "https://www.googleapis.com/auth/dataportability.mymaps.maps";
  /** Move a copy of your food purchase and reservation activity. */
  const DATAPORTABILITY_ORDER_RESERVE_PURCHASES_RESERVATIONS =
      "https://www.googleapis.com/auth/dataportability.order_reserve.purchases_reservations";
  /** Move a copy of information about your devices with Google Play Store installed. */
  const DATAPORTABILITY_PLAY_DEVICES =
      "https://www.googleapis.com/auth/dataportability.play.devices";
  /** Move a copy of your Google Play Store Grouping tags created by app developers. */
  const DATAPORTABILITY_PLAY_GROUPING =
      "https://www.googleapis.com/auth/dataportability.play.grouping";
  /** Move a copy of your Google Play Store app installations. */
  const DATAPORTABILITY_PLAY_INSTALLS =
      "https://www.googleapis.com/auth/dataportability.play.installs";
  /** Move a copy of your Google Play Store downloads, including books, games, and apps. */
  const DATAPORTABILITY_PLAY_LIBRARY =
      "https://www.googleapis.com/auth/dataportability.play.library";
  /** Move a copy of information about your Google Play Store Points. */
  const DATAPORTABILITY_PLAY_PLAYPOINTS =
      "https://www.googleapis.com/auth/dataportability.play.playpoints";
  /** Move a copy of information about your Google Play Store promotions. */
  const DATAPORTABILITY_PLAY_PROMOTIONS =
      "https://www.googleapis.com/auth/dataportability.play.promotions";
  /** Move a copy of your Google Play Store purchases. */
  const DATAPORTABILITY_PLAY_PURCHASES =
      "https://www.googleapis.com/auth/dataportability.play.purchases";
  /** Move a copy of your Google Play Store redemption activities. */
  const DATAPORTABILITY_PLAY_REDEMPTIONS =
      "https://www.googleapis.com/auth/dataportability.play.redemptions";
  /** Move a copy of your Google Play Store subscriptions. */
  const DATAPORTABILITY_PLAY_SUBSCRIPTIONS =
      "https://www.googleapis.com/auth/dataportability.play.subscriptions";
  /** Move a copy of your Google Play Store user settings and preferences. */
  const DATAPORTABILITY_PLAY_USERSETTINGS =
      "https://www.googleapis.com/auth/dataportability.play.usersettings";
  /** Move a copy of your saved links, images, places, and collections from your use of Google services. */
  const DATAPORTABILITY_SAVED_COLLECTIONS =
      "https://www.googleapis.com/auth/dataportability.saved.collections";
  /** Move a copy of your comments on Google Search. */
  const DATAPORTABILITY_SEARCH_UGC_COMMENTS =
      "https://www.googleapis.com/auth/dataportability.search_ugc.comments";
  /** Move a copy of your media reviews on Google Search. */
  const DATAPORTABILITY_SEARCH_UGC_MEDIA_REVIEWS_AND_STARS =
      "https://www.googleapis.com/auth/dataportability.search_ugc.media.reviews_and_stars";
  /** Move a copy of your self-reported video streaming provider preferences from Google Search and Google TV. */
  const DATAPORTABILITY_SEARCH_UGC_MEDIA_STREAMING_VIDEO_PROVIDERS =
      "https://www.googleapis.com/auth/dataportability.search_ugc.media.streaming_video_providers";
  /** Move a copy of your indicated thumbs up and thumbs down on media in Google Search and Google TV. */
  const DATAPORTABILITY_SEARCH_UGC_MEDIA_THUMBS =
      "https://www.googleapis.com/auth/dataportability.search_ugc.media.thumbs";
  /** Move a copy of information about the movies and TV shows you marked as watched on Google Search and Google TV. */
  const DATAPORTABILITY_SEARCH_UGC_MEDIA_WATCHED =
      "https://www.googleapis.com/auth/dataportability.search_ugc.media.watched";
  /** Move a copy of your notification settings on the Google Search app. */
  const DATAPORTABILITY_SEARCHNOTIFICATIONS_SETTINGS =
      "https://www.googleapis.com/auth/dataportability.searchnotifications.settings";
  /** Move a copy of your notification subscriptions on Google Search app. */
  const DATAPORTABILITY_SEARCHNOTIFICATIONS_SUBSCRIPTIONS =
      "https://www.googleapis.com/auth/dataportability.searchnotifications.subscriptions";
  /** Move a copy of your shipping information on Shopping. */
  const DATAPORTABILITY_SHOPPING_ADDRESSES =
      "https://www.googleapis.com/auth/dataportability.shopping.addresses";
  /** Move a copy of reviews you wrote about products or online stores on Google Search. */
  const DATAPORTABILITY_SHOPPING_REVIEWS =
      "https://www.googleapis.com/auth/dataportability.shopping.reviews";
  /** Move a copy of the images and videos you uploaded to Street View. */
  const DATAPORTABILITY_STREETVIEW_IMAGERY =
      "https://www.googleapis.com/auth/dataportability.streetview.imagery";
  /** Move a copy of information about your YouTube channel. */
  const DATAPORTABILITY_YOUTUBE_CHANNEL =
      "https://www.googleapis.com/auth/dataportability.youtube.channel";
  /** Move a copy of your YouTube clips metadata. */
  const DATAPORTABILITY_YOUTUBE_CLIPS =
      "https://www.googleapis.com/auth/dataportability.youtube.clips";
  /** Move a copy of your YouTube comments. */
  const DATAPORTABILITY_YOUTUBE_COMMENTS =
      "https://www.googleapis.com/auth/dataportability.youtube.comments";
  /** Move a copy of all your YouTube messages. */
  const DATAPORTABILITY_YOUTUBE_CONVERSATIONS =
      "https://www.googleapis.com/auth/dataportability.youtube.conversations";
  /** Move a copy of your YouTube messages in live chat. */
  const DATAPORTABILITY_YOUTUBE_LIVE_CHAT =
      "https://www.googleapis.com/auth/dataportability.youtube.live_chat";
  /** Move a copy of your uploaded YouTube music tracks and your YouTube music library. */
  const DATAPORTABILITY_YOUTUBE_MUSIC =
      "https://www.googleapis.com/auth/dataportability.youtube.music";
  /** Move a copy of your YouTube playables saved game progress files. */
  const DATAPORTABILITY_YOUTUBE_PLAYABLE =
      "https://www.googleapis.com/auth/dataportability.youtube.playable";
  /** Move a copy of your YouTube posts. */
  const DATAPORTABILITY_YOUTUBE_POSTS =
      "https://www.googleapis.com/auth/dataportability.youtube.posts";
  /** Move a copy of your YouTube private playlists. */
  const DATAPORTABILITY_YOUTUBE_PRIVATE_PLAYLISTS =
      "https://www.googleapis.com/auth/dataportability.youtube.private_playlists";
  /** Move a copy of your private YouTube videos and information about them. */
  const DATAPORTABILITY_YOUTUBE_PRIVATE_VIDEOS =
      "https://www.googleapis.com/auth/dataportability.youtube.private_videos";
  /** Move a copy of your public YouTube playlists. */
  const DATAPORTABILITY_YOUTUBE_PUBLIC_PLAYLISTS =
      "https://www.googleapis.com/auth/dataportability.youtube.public_playlists";
  /** Move a copy of your public YouTube videos and information about them. */
  const DATAPORTABILITY_YOUTUBE_PUBLIC_VIDEOS =
      "https://www.googleapis.com/auth/dataportability.youtube.public_videos";
  /** Move a copy of your YouTube shopping wishlists, and wishlist items. */
  const DATAPORTABILITY_YOUTUBE_SHOPPING =
      "https://www.googleapis.com/auth/dataportability.youtube.shopping";
  /** Move a copy of your YouTube channel subscriptions, even if they're private. */
  const DATAPORTABILITY_YOUTUBE_SUBSCRIPTIONS =
      "https://www.googleapis.com/auth/dataportability.youtube.subscriptions";
  /** Move a copy of your unlisted YouTube playlists. */
  const DATAPORTABILITY_YOUTUBE_UNLISTED_PLAYLISTS =
      "https://www.googleapis.com/auth/dataportability.youtube.unlisted_playlists";
  /** Move a copy of your unlisted YouTube videos and information about them. */
  const DATAPORTABILITY_YOUTUBE_UNLISTED_VIDEOS =
      "https://www.googleapis.com/auth/dataportability.youtube.unlisted_videos";

  public $accessType;
  public $archiveJobs;
  public $authorization;
  public $portabilityArchive;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the DataPortability service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://dataportability.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://dataportability.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'dataportability';

    $this->accessType = new DataPortability\Resource\AccessType(
        $this,
        $this->serviceName,
        'accessType',
        [
          'methods' => [
            'check' => [
              'path' => 'v1/accessType:check',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->archiveJobs = new DataPortability\Resource\ArchiveJobs(
        $this,
        $this->serviceName,
        'archiveJobs',
        [
          'methods' => [
            'cancel' => [
              'path' => 'v1/{+name}:cancel',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'getPortabilityArchiveState' => [
              'path' => 'v1/{+name}',
              'httpMethod' => 'GET',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'retry' => [
              'path' => 'v1/{+name}:retry',
              'httpMethod' => 'POST',
              'parameters' => [
                'name' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->authorization = new DataPortability\Resource\Authorization(
        $this,
        $this->serviceName,
        'authorization',
        [
          'methods' => [
            'reset' => [
              'path' => 'v1/authorization:reset',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
    $this->portabilityArchive = new DataPortability\Resource\PortabilityArchive(
        $this,
        $this->serviceName,
        'portabilityArchive',
        [
          'methods' => [
            'initiate' => [
              'path' => 'v1/portabilityArchive:initiate',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataPortability::class, 'Google_Service_DataPortability');
