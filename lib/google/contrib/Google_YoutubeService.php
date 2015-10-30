<?php
/*
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


  /**
   * The "channels" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new Google_YoutubeService(...);
   *   $channels = $youtubeService->channels;
   *  </code>
   */
  class Google_ChannelsServiceResource extends Google_ServiceResource {


    /**
     * Browse the YouTube channel collection. Either the 'id' or 'mine' parameter must be set.
     * (channels.list)
     *
     * @param string $part Parts of the channel resource to be returned.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string id YouTube IDs of the channels to be returned.
     * @opt_param string mine Flag indicating only return the channel ids of the authenticated user.
     * @return Google_ChannelListResponse
     */
    public function listChannels($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_ChannelListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "search" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new Google_YoutubeService(...);
   *   $search = $youtubeService->search;
   *  </code>
   */
  class Google_SearchServiceResource extends Google_ServiceResource {


    /**
     * Universal search for youtube. (search.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string q Query to search in Youtube.
     * @opt_param string startIndex Index of the first search result to return.
     * @opt_param string type Type of resource to search.
     * @opt_param string order Sort order.
     * @opt_param string maxResults Maximum number of search results to return per page.
     * @return Google_SearchListResponse
     */
    public function listSearch($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SearchListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "playlistitems" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new Google_YoutubeService(...);
   *   $playlistitems = $youtubeService->playlistitems;
   *  </code>
   */
  class Google_PlaylistitemsServiceResource extends Google_ServiceResource {


    /**
     * Browse the YouTube playlist collection. (playlistitems.list)
     *
     * @param string $part Parts of the playlist resource to be returned.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string startIndex Index of the first element to return (starts at 0)
     * @opt_param string playlistId Retrieves playlist items from the given playlist id.
     * @opt_param string id YouTube IDs of the playlists to be returned.
     * @opt_param string maxResults Maximum number of results to return
     * @return Google_PlaylistItemListResponse
     */
    public function listPlaylistitems($part, $optParams = array()) {
      $params = array('part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_PlaylistItemListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "playlists" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new Google_YoutubeService(...);
   *   $playlists = $youtubeService->playlists;
   *  </code>
   */
  class Google_PlaylistsServiceResource extends Google_ServiceResource {


    /**
     * Browse the YouTube playlist collection. (playlists.list)
     *
     * @param string $id YouTube IDs of the playlists to be returned.
     * @param string $part Parts of the playlist resource to be returned.
     * @param array $optParams Optional parameters.
     * @return Google_PlaylistListResponse
     */
    public function listPlaylists($id, $part, $optParams = array()) {
      $params = array('id' => $id, 'part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_PlaylistListResponse($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "videos" collection of methods.
   * Typical usage is:
   *  <code>
   *   $youtubeService = new Google_YoutubeService(...);
   *   $videos = $youtubeService->videos;
   *  </code>
   */
  class Google_VideosServiceResource extends Google_ServiceResource {


    /**
     * Browse the YouTube video collection. (videos.list)
     *
     * @param string $id YouTube IDs of the videos to be returned.
     * @param string $part Parts of the video resource to be returned.
     * @param array $optParams Optional parameters.
     * @return Google_VideoListResponse
     */
    public function listVideos($id, $part, $optParams = array()) {
      $params = array('id' => $id, 'part' => $part);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_VideoListResponse($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Youtube (v3alpha).
 *
 * <p>
 * Programmatic access to YouTube features.
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="https://developers.google.com/youtube" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_YoutubeService extends Google_Service {
  public $channels;
  public $search;
  public $playlistitems;
  public $playlists;
  public $videos;
  /**
   * Constructs the internal representation of the Youtube service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'youtube/v3alpha/';
    $this->version = 'v3alpha';
    $this->serviceName = 'youtube';

    $client->addService($this->serviceName, $this->version);
    $this->channels = new Google_ChannelsServiceResource($this, $this->serviceName, 'channels', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube"], "parameters": {"part": {"required": true, "type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}, "mine": {"type": "string", "location": "query"}}, "id": "youtube.channels.list", "httpMethod": "GET", "path": "channels", "response": {"$ref": "ChannelListResponse"}}}}', true));
    $this->search = new Google_SearchServiceResource($this, $this->serviceName, 'search', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube"], "parameters": {"q": {"type": "string", "location": "query"}, "startIndex": {"format": "uint32", "default": "0", "maximum": "1000", "minimum": "0", "location": "query", "type": "integer"}, "type": {"repeated": true, "enum": ["channel", "playlist", "video"], "type": "string", "location": "query"}, "order": {"default": "SEARCH_SORT_RELEVANCE", "enum": ["date", "rating", "relevance", "view_count"], "type": "string", "location": "query"}, "maxResults": {"format": "uint32", "default": "25", "maximum": "50", "minimum": "0", "location": "query", "type": "integer"}}, "response": {"$ref": "SearchListResponse"}, "httpMethod": "GET", "path": "search", "id": "youtube.search.list"}}}', true));
    $this->playlistitems = new Google_PlaylistitemsServiceResource($this, $this->serviceName, 'playlistitems', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube"], "parameters": {"startIndex": {"minimum": "0", "type": "integer", "location": "query", "format": "uint32"}, "part": {"required": true, "type": "string", "location": "query"}, "playlistId": {"type": "string", "location": "query"}, "id": {"type": "string", "location": "query"}, "maxResults": {"default": "50", "minimum": "0", "type": "integer", "location": "query", "format": "uint32"}}, "id": "youtube.playlistitems.list", "httpMethod": "GET", "path": "playlistitems", "response": {"$ref": "PlaylistItemListResponse"}}}}', true));
    $this->playlists = new Google_PlaylistsServiceResource($this, $this->serviceName, 'playlists', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube"], "parameters": {"part": {"required": true, "type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "id": "youtube.playlists.list", "httpMethod": "GET", "path": "playlists", "response": {"$ref": "PlaylistListResponse"}}}}', true));
    $this->videos = new Google_VideosServiceResource($this, $this->serviceName, 'videos', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/youtube"], "parameters": {"part": {"required": true, "type": "string", "location": "query"}, "id": {"required": true, "type": "string", "location": "query"}}, "id": "youtube.videos.list", "httpMethod": "GET", "path": "videos", "response": {"$ref": "VideoListResponse"}}}}', true));

  }
}

class Google_Channel extends Google_Model {
  public $kind;
  protected $__statisticsType = 'Google_ChannelStatistics';
  protected $__statisticsDataType = '';
  public $statistics;
  protected $__contentDetailsType = 'Google_ChannelContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  protected $__snippetType = 'Google_ChannelSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $etag;
  public $id;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStatistics(Google_ChannelStatistics $statistics) {
    $this->statistics = $statistics;
  }
  public function getStatistics() {
    return $this->statistics;
  }
  public function setContentDetails(Google_ChannelContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setSnippet(Google_ChannelSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_ChannelContentDetails extends Google_Model {
  public $privacyStatus;
  public $uploads;
  public function setPrivacyStatus($privacyStatus) {
    $this->privacyStatus = $privacyStatus;
  }
  public function getPrivacyStatus() {
    return $this->privacyStatus;
  }
  public function setUploads($uploads) {
    $this->uploads = $uploads;
  }
  public function getUploads() {
    return $this->uploads;
  }
}

class Google_ChannelListResponse extends Google_Model {
  protected $__channelsType = 'Google_Channel';
  protected $__channelsDataType = 'map';
  public $channels;
  public $kind;
  public $etag;
  public function setChannels(Google_Channel $channels) {
    $this->channels = $channels;
  }
  public function getChannels() {
    return $this->channels;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_ChannelSnippet extends Google_Model {
  public $title;
  public $description;
  protected $__thumbnailsType = 'Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setThumbnails(Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
}

class Google_ChannelStatistics extends Google_Model {
  public $commentCount;
  public $subscriberCount;
  public $videoCount;
  public $viewCount;
  public function setCommentCount($commentCount) {
    $this->commentCount = $commentCount;
  }
  public function getCommentCount() {
    return $this->commentCount;
  }
  public function setSubscriberCount($subscriberCount) {
    $this->subscriberCount = $subscriberCount;
  }
  public function getSubscriberCount() {
    return $this->subscriberCount;
  }
  public function setVideoCount($videoCount) {
    $this->videoCount = $videoCount;
  }
  public function getVideoCount() {
    return $this->videoCount;
  }
  public function setViewCount($viewCount) {
    $this->viewCount = $viewCount;
  }
  public function getViewCount() {
    return $this->viewCount;
  }
}

class Google_PageInfo extends Google_Model {
  public $totalResults;
  public $startIndex;
  public $resultPerPage;
  public function setTotalResults($totalResults) {
    $this->totalResults = $totalResults;
  }
  public function getTotalResults() {
    return $this->totalResults;
  }
  public function setStartIndex($startIndex) {
    $this->startIndex = $startIndex;
  }
  public function getStartIndex() {
    return $this->startIndex;
  }
  public function setResultPerPage($resultPerPage) {
    $this->resultPerPage = $resultPerPage;
  }
  public function getResultPerPage() {
    return $this->resultPerPage;
  }
}

class Google_Playlist extends Google_Model {
  protected $__snippetType = 'Google_PlaylistSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(Google_PlaylistSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_PlaylistItem extends Google_Model {
  protected $__snippetType = 'Google_PlaylistItemSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $kind;
  public $etag;
  public $id;
  public function setSnippet(Google_PlaylistItemSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_PlaylistItemListResponse extends Google_Model {
  protected $__playlistItemsType = 'Google_PlaylistItem';
  protected $__playlistItemsDataType = 'map';
  public $playlistItems;
  public $kind;
  public $etag;
  public function setPlaylistItems(Google_PlaylistItem $playlistItems) {
    $this->playlistItems = $playlistItems;
  }
  public function getPlaylistItems() {
    return $this->playlistItems;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
}

class Google_PlaylistItemSnippet extends Google_Model {
  public $playlistId;
  public $description;
  public $title;
  protected $__resourceIdType = 'Google_ResourceId';
  protected $__resourceIdDataType = '';
  public $resourceId;
  public $channelId;
  public $publishedAt;
  public $position;
  public function setPlaylistId($playlistId) {
    $this->playlistId = $playlistId;
  }
  public function getPlaylistId() {
    return $this->playlistId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setResourceId(Google_ResourceId $resourceId) {
    $this->resourceId = $resourceId;
  }
  public function getResourceId() {
    return $this->resourceId;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setPosition($position) {
    $this->position = $position;
  }
  public function getPosition() {
    return $this->position;
  }
}

class Google_PlaylistListResponse extends Google_Model {
  public $kind;
  public $etag;
  protected $__playlistsType = 'Google_Playlist';
  protected $__playlistsDataType = 'map';
  public $playlists;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setPlaylists(Google_Playlist $playlists) {
    $this->playlists = $playlists;
  }
  public function getPlaylists() {
    return $this->playlists;
  }
}

class Google_PlaylistSnippet extends Google_Model {
  public $title;
  public $channelId;
  public $description;
  public $publishedAt;
  public $tags;
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setTags(/* array(Google_string) */ $tags) {
    $this->assertIsArray($tags, 'Google_string', __METHOD__);
    $this->tags = $tags;
  }
  public function getTags() {
    return $this->tags;
  }
}

class Google_ResourceId extends Google_Model {
  public $kind;
  public $channelId;
  public $playlistId;
  public $videoId;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPlaylistId($playlistId) {
    $this->playlistId = $playlistId;
  }
  public function getPlaylistId() {
    return $this->playlistId;
  }
  public function setVideoId($videoId) {
    $this->videoId = $videoId;
  }
  public function getVideoId() {
    return $this->videoId;
  }
}

class Google_SearchListResponse extends Google_Model {
  protected $__searchResultsType = 'Google_SearchResult';
  protected $__searchResultsDataType = 'array';
  public $searchResults;
  public $kind;
  public $etag;
  protected $__pageInfoType = 'Google_PageInfo';
  protected $__pageInfoDataType = '';
  public $pageInfo;
  public function setSearchResults(/* array(Google_SearchResult) */ $searchResults) {
    $this->assertIsArray($searchResults, 'Google_SearchResult', __METHOD__);
    $this->searchResults = $searchResults;
  }
  public function getSearchResults() {
    return $this->searchResults;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setPageInfo(Google_PageInfo $pageInfo) {
    $this->pageInfo = $pageInfo;
  }
  public function getPageInfo() {
    return $this->pageInfo;
  }
}

class Google_SearchResult extends Google_Model {
  protected $__snippetType = 'Google_SearchResultSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  public $kind;
  public $etag;
  protected $__idType = 'Google_ResourceId';
  protected $__idDataType = '';
  public $id;
  public function setSnippet(Google_SearchResultSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId(Google_ResourceId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_SearchResultSnippet extends Google_Model {
  public $channelId;
  public $description;
  public $publishedAt;
  public $title;
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
}

class Google_Thumbnail extends Google_Model {
  public $url;
  public function setUrl($url) {
    $this->url = $url;
  }
  public function getUrl() {
    return $this->url;
  }
}

class Google_Video extends Google_Model {
  protected $__statusType = 'Google_VideoStatus';
  protected $__statusDataType = '';
  public $status;
  public $kind;
  protected $__statisticsType = 'Google_VideoStatistics';
  protected $__statisticsDataType = '';
  public $statistics;
  protected $__contentDetailsType = 'Google_VideoContentDetails';
  protected $__contentDetailsDataType = '';
  public $contentDetails;
  protected $__snippetType = 'Google_VideoSnippet';
  protected $__snippetDataType = '';
  public $snippet;
  protected $__playerType = 'Google_VideoPlayer';
  protected $__playerDataType = '';
  public $player;
  public $etag;
  public $id;
  public function setStatus(Google_VideoStatus $status) {
    $this->status = $status;
  }
  public function getStatus() {
    return $this->status;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setStatistics(Google_VideoStatistics $statistics) {
    $this->statistics = $statistics;
  }
  public function getStatistics() {
    return $this->statistics;
  }
  public function setContentDetails(Google_VideoContentDetails $contentDetails) {
    $this->contentDetails = $contentDetails;
  }
  public function getContentDetails() {
    return $this->contentDetails;
  }
  public function setSnippet(Google_VideoSnippet $snippet) {
    $this->snippet = $snippet;
  }
  public function getSnippet() {
    return $this->snippet;
  }
  public function setPlayer(Google_VideoPlayer $player) {
    $this->player = $player;
  }
  public function getPlayer() {
    return $this->player;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setId($id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_VideoContentDetails extends Google_Model {
  public $duration;
  public $aspectRatio;
  public function setDuration($duration) {
    $this->duration = $duration;
  }
  public function getDuration() {
    return $this->duration;
  }
  public function setAspectRatio($aspectRatio) {
    $this->aspectRatio = $aspectRatio;
  }
  public function getAspectRatio() {
    return $this->aspectRatio;
  }
}

class Google_VideoListResponse extends Google_Model {
  public $kind;
  public $etag;
  protected $__videosType = 'Google_Video';
  protected $__videosDataType = 'map';
  public $videos;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setEtag($etag) {
    $this->etag = $etag;
  }
  public function getEtag() {
    return $this->etag;
  }
  public function setVideos(Google_Video $videos) {
    $this->videos = $videos;
  }
  public function getVideos() {
    return $this->videos;
  }
}

class Google_VideoPlayer extends Google_Model {
  public $embedHtml;
  public function setEmbedHtml($embedHtml) {
    $this->embedHtml = $embedHtml;
  }
  public function getEmbedHtml() {
    return $this->embedHtml;
  }
}

class Google_VideoSnippet extends Google_Model {
  protected $__thumbnailsType = 'Google_Thumbnail';
  protected $__thumbnailsDataType = 'map';
  public $thumbnails;
  public $tags;
  public $channelId;
  public $publishedAt;
  public $title;
  public $categoryId;
  public $description;
  public function setThumbnails(Google_Thumbnail $thumbnails) {
    $this->thumbnails = $thumbnails;
  }
  public function getThumbnails() {
    return $this->thumbnails;
  }
  public function setTags(/* array(Google_string) */ $tags) {
    $this->assertIsArray($tags, 'Google_string', __METHOD__);
    $this->tags = $tags;
  }
  public function getTags() {
    return $this->tags;
  }
  public function setChannelId($channelId) {
    $this->channelId = $channelId;
  }
  public function getChannelId() {
    return $this->channelId;
  }
  public function setPublishedAt($publishedAt) {
    $this->publishedAt = $publishedAt;
  }
  public function getPublishedAt() {
    return $this->publishedAt;
  }
  public function setTitle($title) {
    $this->title = $title;
  }
  public function getTitle() {
    return $this->title;
  }
  public function setCategoryId($categoryId) {
    $this->categoryId = $categoryId;
  }
  public function getCategoryId() {
    return $this->categoryId;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
}

class Google_VideoStatistics extends Google_Model {
  public $commentCount;
  public $viewCount;
  public $favoriteCount;
  public $dislikeCount;
  public $likeCount;
  public function setCommentCount($commentCount) {
    $this->commentCount = $commentCount;
  }
  public function getCommentCount() {
    return $this->commentCount;
  }
  public function setViewCount($viewCount) {
    $this->viewCount = $viewCount;
  }
  public function getViewCount() {
    return $this->viewCount;
  }
  public function setFavoriteCount($favoriteCount) {
    $this->favoriteCount = $favoriteCount;
  }
  public function getFavoriteCount() {
    return $this->favoriteCount;
  }
  public function setDislikeCount($dislikeCount) {
    $this->dislikeCount = $dislikeCount;
  }
  public function getDislikeCount() {
    return $this->dislikeCount;
  }
  public function setLikeCount($likeCount) {
    $this->likeCount = $likeCount;
  }
  public function getLikeCount() {
    return $this->likeCount;
  }
}

class Google_VideoStatus extends Google_Model {
  public $privacyStatus;
  public $uploadStatus;
  public $rejectionReason;
  public $failureReason;
  public function setPrivacyStatus($privacyStatus) {
    $this->privacyStatus = $privacyStatus;
  }
  public function getPrivacyStatus() {
    return $this->privacyStatus;
  }
  public function setUploadStatus($uploadStatus) {
    $this->uploadStatus = $uploadStatus;
  }
  public function getUploadStatus() {
    return $this->uploadStatus;
  }
  public function setRejectionReason($rejectionReason) {
    $this->rejectionReason = $rejectionReason;
  }
  public function getRejectionReason() {
    return $this->rejectionReason;
  }
  public function setFailureReason($failureReason) {
    $this->failureReason = $failureReason;
  }
  public function getFailureReason() {
    return $this->failureReason;
  }
}
