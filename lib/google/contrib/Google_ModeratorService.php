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
   * The "votes" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $votes = $moderatorService->votes;
   *  </code>
   */
  class Google_VotesServiceResource extends Google_ServiceResource {


    /**
     * Inserts a new vote by the authenticated user for the specified submission within the specified
     * series. (votes.insert)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param Google_Vote $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string unauthToken User identifier for unauthenticated usage mode
     * @return Google_Vote
     */
    public function insert($seriesId, $submissionId, Google_Vote $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Vote($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the votes by the authenticated user for the specified submission within the specified
     * series. This method supports patch semantics. (votes.patch)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param Google_Vote $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string userId
     * @opt_param string unauthToken User identifier for unauthenticated usage mode
     * @return Google_Vote
     */
    public function patch($seriesId, $submissionId, Google_Vote $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Vote($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists the votes by the authenticated user for the given series. (votes.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param string start-index Index of the first result to be retrieved.
     * @return Google_VoteList
     */
    public function listVotes($seriesId, $optParams = array()) {
      $params = array('seriesId' => $seriesId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_VoteList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the votes by the authenticated user for the specified submission within the specified
     * series. (votes.update)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param Google_Vote $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string userId
     * @opt_param string unauthToken User identifier for unauthenticated usage mode
     * @return Google_Vote
     */
    public function update($seriesId, $submissionId, Google_Vote $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Vote($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the votes by the authenticated user for the specified submission within the specified
     * series. (votes.get)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string userId
     * @opt_param string unauthToken User identifier for unauthenticated usage mode
     * @return Google_Vote
     */
    public function get($seriesId, $submissionId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Vote($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "responses" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $responses = $moderatorService->responses;
   *  </code>
   */
  class Google_ResponsesServiceResource extends Google_ServiceResource {


    /**
     * Inserts a response for the specified submission in the specified topic within the specified
     * series. (responses.insert)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $topicId The decimal ID of the Topic within the Series.
     * @param string $parentSubmissionId The decimal ID of the parent Submission within the Series.
     * @param Google_Submission $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string unauthToken User identifier for unauthenticated usage mode
     * @opt_param bool anonymous Set to true to mark the new submission as anonymous.
     * @return Google_Submission
     */
    public function insert($seriesId, $topicId, $parentSubmissionId, Google_Submission $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'topicId' => $topicId, 'parentSubmissionId' => $parentSubmissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Submission($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists or searches the responses for the specified submission within the specified series and
     * returns the search results. (responses.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param string sort Sort order.
     * @opt_param string author Restricts the results to submissions by a specific author.
     * @opt_param string start-index Index of the first result to be retrieved.
     * @opt_param string q Search query.
     * @opt_param bool hasAttachedVideo Specifies whether to restrict to submissions that have videos attached.
     * @return Google_SubmissionList
     */
    public function listResponses($seriesId, $submissionId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SubmissionList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "tags" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $tags = $moderatorService->tags;
   *  </code>
   */
  class Google_TagsServiceResource extends Google_ServiceResource {


    /**
     * Inserts a new tag for the specified submission within the specified series. (tags.insert)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param Google_Tag $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Tag
     */
    public function insert($seriesId, $submissionId, Google_Tag $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Tag($data);
      } else {
        return $data;
      }
    }
    /**
     * Lists all tags for the specified submission within the specified series. (tags.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param array $optParams Optional parameters.
     * @return Google_TagList
     */
    public function listTags($seriesId, $submissionId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TagList($data);
      } else {
        return $data;
      }
    }
    /**
     * Deletes the specified tag from the specified submission within the specified series.
     * (tags.delete)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param string $tagId
     * @param array $optParams Optional parameters.
     */
    public function delete($seriesId, $submissionId, $tagId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId, 'tagId' => $tagId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('delete', array($params));
      return $data;
    }
  }

  /**
   * The "series" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $series = $moderatorService->series;
   *  </code>
   */
  class Google_SeriesServiceResource extends Google_ServiceResource {


    /**
     * Inserts a new series. (series.insert)
     *
     * @param Google_Series $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Series
     */
    public function insert(Google_Series $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Series($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the specified series. This method supports patch semantics. (series.patch)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param Google_Series $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Series
     */
    public function patch($seriesId, Google_Series $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Series($data);
      } else {
        return $data;
      }
    }
    /**
     * Searches the series and returns the search results. (series.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param string q Search query.
     * @opt_param string start-index Index of the first result to be retrieved.
     * @return Google_SeriesList
     */
    public function listSeries($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SeriesList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the specified series. (series.update)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param Google_Series $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Series
     */
    public function update($seriesId, Google_Series $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Series($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the specified series. (series.get)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param array $optParams Optional parameters.
     * @return Google_Series
     */
    public function get($seriesId, $optParams = array()) {
      $params = array('seriesId' => $seriesId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Series($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "submissions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $submissions = $moderatorService->submissions;
   *  </code>
   */
  class Google_SeriesSubmissionsServiceResource extends Google_ServiceResource {


    /**
     * Searches the submissions for the specified series and returns the search results.
     * (submissions.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string lang The language code for the language the client prefers resuls in.
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param bool includeVotes Specifies whether to include the current user's vote
     * @opt_param string start-index Index of the first result to be retrieved.
     * @opt_param string author Restricts the results to submissions by a specific author.
     * @opt_param string sort Sort order.
     * @opt_param string q Search query.
     * @opt_param bool hasAttachedVideo Specifies whether to restrict to submissions that have videos attached.
     * @return Google_SubmissionList
     */
    public function listSeriesSubmissions($seriesId, $optParams = array()) {
      $params = array('seriesId' => $seriesId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SubmissionList($data);
      } else {
        return $data;
      }
    }
  }
  /**
   * The "responses" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $responses = $moderatorService->responses;
   *  </code>
   */
  class Google_SeriesResponsesServiceResource extends Google_ServiceResource {


    /**
     * Searches the responses for the specified series and returns the search results. (responses.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param string sort Sort order.
     * @opt_param string author Restricts the results to submissions by a specific author.
     * @opt_param string start-index Index of the first result to be retrieved.
     * @opt_param string q Search query.
     * @opt_param bool hasAttachedVideo Specifies whether to restrict to submissions that have videos attached.
     * @return Google_SeriesList
     */
    public function listSeriesResponses($seriesId, $optParams = array()) {
      $params = array('seriesId' => $seriesId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SeriesList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "topics" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $topics = $moderatorService->topics;
   *  </code>
   */
  class Google_TopicsServiceResource extends Google_ServiceResource {


    /**
     * Inserts a new topic into the specified series. (topics.insert)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param Google_Topic $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Topic
     */
    public function insert($seriesId, Google_Topic $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Topic($data);
      } else {
        return $data;
      }
    }
    /**
     * Searches the topics within the specified series and returns the search results. (topics.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param string q Search query.
     * @opt_param string start-index Index of the first result to be retrieved.
     * @opt_param string mode
     * @return Google_TopicList
     */
    public function listTopics($seriesId, $optParams = array()) {
      $params = array('seriesId' => $seriesId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_TopicList($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the specified topic within the specified series. (topics.update)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $topicId The decimal ID of the Topic within the Series.
     * @param Google_Topic $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Topic
     */
    public function update($seriesId, $topicId, Google_Topic $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'topicId' => $topicId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Topic($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the specified topic from the specified series. (topics.get)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $topicId The decimal ID of the Topic within the Series.
     * @param array $optParams Optional parameters.
     * @return Google_Topic
     */
    public function get($seriesId, $topicId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'topicId' => $topicId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Topic($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "submissions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $submissions = $moderatorService->submissions;
   *  </code>
   */
  class Google_TopicsSubmissionsServiceResource extends Google_ServiceResource {


    /**
     * Searches the submissions for the specified topic within the specified series and returns the
     * search results. (submissions.list)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $topicId The decimal ID of the Topic within the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param bool includeVotes Specifies whether to include the current user's vote
     * @opt_param string start-index Index of the first result to be retrieved.
     * @opt_param string author Restricts the results to submissions by a specific author.
     * @opt_param string sort Sort order.
     * @opt_param string q Search query.
     * @opt_param bool hasAttachedVideo Specifies whether to restrict to submissions that have videos attached.
     * @return Google_SubmissionList
     */
    public function listTopicsSubmissions($seriesId, $topicId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'topicId' => $topicId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SubmissionList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "global" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $global = $moderatorService->global;
   *  </code>
   */
  class Google_ModeratorGlobalServiceResource extends Google_ServiceResource {


  }

  /**
   * The "series" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $series = $moderatorService->series;
   *  </code>
   */
  class Google_ModeratorGlobalSeriesServiceResource extends Google_ServiceResource {


    /**
     * Searches the public series and returns the search results. (series.list)
     *
     * @param array $optParams Optional parameters.
     *
     * @opt_param string max-results Maximum number of results to return.
     * @opt_param string q Search query.
     * @opt_param string start-index Index of the first result to be retrieved.
     * @return Google_SeriesList
     */
    public function listModeratorGlobalSeries($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SeriesList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "profiles" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $profiles = $moderatorService->profiles;
   *  </code>
   */
  class Google_ProfilesServiceResource extends Google_ServiceResource {


    /**
     * Updates the profile information for the authenticated user. This method supports patch semantics.
     * (profiles.patch)
     *
     * @param Google_Profile $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Profile
     */
    public function patch(Google_Profile $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('patch', array($params));
      if ($this->useObjects()) {
        return new Google_Profile($data);
      } else {
        return $data;
      }
    }
    /**
     * Updates the profile information for the authenticated user. (profiles.update)
     *
     * @param Google_Profile $postBody
     * @param array $optParams Optional parameters.
     * @return Google_Profile
     */
    public function update(Google_Profile $postBody, $optParams = array()) {
      $params = array('postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('update', array($params));
      if ($this->useObjects()) {
        return new Google_Profile($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the profile information for the authenticated user. (profiles.get)
     *
     * @param array $optParams Optional parameters.
     * @return Google_Profile
     */
    public function get($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Profile($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "featured" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $featured = $moderatorService->featured;
   *  </code>
   */
  class Google_FeaturedServiceResource extends Google_ServiceResource {


  }

  /**
   * The "series" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $series = $moderatorService->series;
   *  </code>
   */
  class Google_FeaturedSeriesServiceResource extends Google_ServiceResource {


    /**
     * Lists the featured series. (series.list)
     *
     * @param array $optParams Optional parameters.
     * @return Google_SeriesList
     */
    public function listFeaturedSeries($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SeriesList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "myrecent" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $myrecent = $moderatorService->myrecent;
   *  </code>
   */
  class Google_MyrecentServiceResource extends Google_ServiceResource {


  }

  /**
   * The "series" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $series = $moderatorService->series;
   *  </code>
   */
  class Google_MyrecentSeriesServiceResource extends Google_ServiceResource {


    /**
     * Lists the series the authenticated user has visited. (series.list)
     *
     * @param array $optParams Optional parameters.
     * @return Google_SeriesList
     */
    public function listMyrecentSeries($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SeriesList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "my" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $my = $moderatorService->my;
   *  </code>
   */
  class Google_MyServiceResource extends Google_ServiceResource {


  }

  /**
   * The "series" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $series = $moderatorService->series;
   *  </code>
   */
  class Google_MySeriesServiceResource extends Google_ServiceResource {


    /**
     * Lists all series created by the authenticated user. (series.list)
     *
     * @param array $optParams Optional parameters.
     * @return Google_SeriesList
     */
    public function listMySeries($optParams = array()) {
      $params = array();
      $params = array_merge($params, $optParams);
      $data = $this->__call('list', array($params));
      if ($this->useObjects()) {
        return new Google_SeriesList($data);
      } else {
        return $data;
      }
    }
  }

  /**
   * The "submissions" collection of methods.
   * Typical usage is:
   *  <code>
   *   $moderatorService = new Google_ModeratorService(...);
   *   $submissions = $moderatorService->submissions;
   *  </code>
   */
  class Google_SubmissionsServiceResource extends Google_ServiceResource {


    /**
     * Inserts a new submission in the specified topic within the specified series. (submissions.insert)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $topicId The decimal ID of the Topic within the Series.
     * @param Google_Submission $postBody
     * @param array $optParams Optional parameters.
     *
     * @opt_param string unauthToken User identifier for unauthenticated usage mode
     * @opt_param bool anonymous Set to true to mark the new submission as anonymous.
     * @return Google_Submission
     */
    public function insert($seriesId, $topicId, Google_Submission $postBody, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'topicId' => $topicId, 'postBody' => $postBody);
      $params = array_merge($params, $optParams);
      $data = $this->__call('insert', array($params));
      if ($this->useObjects()) {
        return new Google_Submission($data);
      } else {
        return $data;
      }
    }
    /**
     * Returns the specified submission within the specified series. (submissions.get)
     *
     * @param string $seriesId The decimal ID of the Series.
     * @param string $submissionId The decimal ID of the Submission within the Series.
     * @param array $optParams Optional parameters.
     *
     * @opt_param string lang The language code for the language the client prefers resuls in.
     * @opt_param bool includeVotes Specifies whether to include the current user's vote
     * @return Google_Submission
     */
    public function get($seriesId, $submissionId, $optParams = array()) {
      $params = array('seriesId' => $seriesId, 'submissionId' => $submissionId);
      $params = array_merge($params, $optParams);
      $data = $this->__call('get', array($params));
      if ($this->useObjects()) {
        return new Google_Submission($data);
      } else {
        return $data;
      }
    }
  }

/**
 * Service definition for Google_Moderator (v1).
 *
 * <p>
 * Moderator API
 * </p>
 *
 * <p>
 * For more information about this service, see the
 * <a href="http://code.google.com/apis/moderator/v1/using_rest.html" target="_blank">API Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Google_ModeratorService extends Google_Service {
  public $votes;
  public $responses;
  public $tags;
  public $series;
  public $series_submissions;
  public $series_responses;
  public $topics;
  public $topics_submissions;
  public $global_series;
  public $profiles;
  public $featured_series;
  public $myrecent_series;
  public $my_series;
  public $submissions;
  /**
   * Constructs the internal representation of the Moderator service.
   *
   * @param Google_Client $client
   */
  public function __construct(Google_Client $client) {
    $this->servicePath = 'moderator/v1/';
    $this->version = 'v1';
    $this->serviceName = 'moderator';

    $client->addService($this->serviceName, $this->version);
    $this->votes = new Google_VotesServiceResource($this, $this->serviceName, 'votes', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "unauthToken": {"type": "string", "location": "query"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Vote"}, "response": {"$ref": "Vote"}, "httpMethod": "POST", "path": "series/{seriesId}/submissions/{submissionId}/votes/@me", "id": "moderator.votes.insert"}, "patch": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "userId": {"type": "string", "location": "query"}, "unauthToken": {"type": "string", "location": "query"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Vote"}, "response": {"$ref": "Vote"}, "httpMethod": "PATCH", "path": "series/{seriesId}/submissions/{submissionId}/votes/@me", "id": "moderator.votes.patch"}, "list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}}, "id": "moderator.votes.list", "httpMethod": "GET", "path": "series/{seriesId}/votes/@me", "response": {"$ref": "VoteList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "userId": {"type": "string", "location": "query"}, "unauthToken": {"type": "string", "location": "query"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Vote"}, "response": {"$ref": "Vote"}, "httpMethod": "PUT", "path": "series/{seriesId}/submissions/{submissionId}/votes/@me", "id": "moderator.votes.update"}, "get": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "userId": {"type": "string", "location": "query"}, "unauthToken": {"type": "string", "location": "query"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "id": "moderator.votes.get", "httpMethod": "GET", "path": "series/{seriesId}/submissions/{submissionId}/votes/@me", "response": {"$ref": "Vote"}}}}', true));
    $this->responses = new Google_ResponsesServiceResource($this, $this->serviceName, 'responses', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "parentSubmissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "unauthToken": {"type": "string", "location": "query"}, "anonymous": {"type": "boolean", "location": "query"}, "topicId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Submission"}, "response": {"$ref": "Submission"}, "httpMethod": "POST", "path": "series/{seriesId}/topics/{topicId}/submissions/{parentSubmissionId}/responses", "id": "moderator.responses.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "sort": {"type": "string", "location": "query"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "author": {"type": "string", "location": "query"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "q": {"type": "string", "location": "query"}, "hasAttachedVideo": {"type": "boolean", "location": "query"}}, "id": "moderator.responses.list", "httpMethod": "GET", "path": "series/{seriesId}/submissions/{submissionId}/responses", "response": {"$ref": "SubmissionList"}}}}', true));
    $this->tags = new Google_TagsServiceResource($this, $this->serviceName, 'tags', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Tag"}, "response": {"$ref": "Tag"}, "httpMethod": "POST", "path": "series/{seriesId}/submissions/{submissionId}/tags", "id": "moderator.tags.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "id": "moderator.tags.list", "httpMethod": "GET", "path": "series/{seriesId}/submissions/{submissionId}/tags", "response": {"$ref": "TagList"}}, "delete": {"scopes": ["https://www.googleapis.com/auth/moderator"], "path": "series/{seriesId}/submissions/{submissionId}/tags/{tagId}", "id": "moderator.tags.delete", "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "tagId": {"required": true, "type": "string", "location": "path"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "httpMethod": "DELETE"}}}', true));
    $this->series = new Google_SeriesServiceResource($this, $this->serviceName, 'series', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/moderator"], "request": {"$ref": "Series"}, "response": {"$ref": "Series"}, "httpMethod": "POST", "path": "series", "id": "moderator.series.insert"}, "patch": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Series"}, "response": {"$ref": "Series"}, "httpMethod": "PATCH", "path": "series/{seriesId}", "id": "moderator.series.patch"}, "list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "q": {"type": "string", "location": "query"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}}, "response": {"$ref": "SeriesList"}, "httpMethod": "GET", "path": "series", "id": "moderator.series.list"}, "update": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Series"}, "response": {"$ref": "Series"}, "httpMethod": "PUT", "path": "series/{seriesId}", "id": "moderator.series.update"}, "get": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "id": "moderator.series.get", "httpMethod": "GET", "path": "series/{seriesId}", "response": {"$ref": "Series"}}}}', true));
    $this->series_submissions = new Google_SeriesSubmissionsServiceResource($this, $this->serviceName, 'submissions', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"lang": {"type": "string", "location": "query"}, "max-results": {"type": "integer", "location": "query", "format": "uint32"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "includeVotes": {"type": "boolean", "location": "query"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}, "author": {"type": "string", "location": "query"}, "sort": {"type": "string", "location": "query"}, "q": {"type": "string", "location": "query"}, "hasAttachedVideo": {"type": "boolean", "location": "query"}}, "id": "moderator.series.submissions.list", "httpMethod": "GET", "path": "series/{seriesId}/submissions", "response": {"$ref": "SubmissionList"}}}}', true));
    $this->series_responses = new Google_SeriesResponsesServiceResource($this, $this->serviceName, 'responses', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "sort": {"type": "string", "location": "query"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "author": {"type": "string", "location": "query"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}, "q": {"type": "string", "location": "query"}, "hasAttachedVideo": {"type": "boolean", "location": "query"}}, "id": "moderator.series.responses.list", "httpMethod": "GET", "path": "series/{seriesId}/responses", "response": {"$ref": "SeriesList"}}}}', true));
    $this->topics = new Google_TopicsServiceResource($this, $this->serviceName, 'topics', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Topic"}, "response": {"$ref": "Topic"}, "httpMethod": "POST", "path": "series/{seriesId}/topics", "id": "moderator.topics.insert"}, "list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "q": {"type": "string", "location": "query"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}, "mode": {"type": "string", "location": "query"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "id": "moderator.topics.list", "httpMethod": "GET", "path": "series/{seriesId}/topics", "response": {"$ref": "TopicList"}}, "update": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "topicId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "request": {"$ref": "Topic"}, "response": {"$ref": "Topic"}, "httpMethod": "PUT", "path": "series/{seriesId}/topics/{topicId}", "id": "moderator.topics.update"}, "get": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "topicId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}}, "id": "moderator.topics.get", "httpMethod": "GET", "path": "series/{seriesId}/topics/{topicId}", "response": {"$ref": "Topic"}}}}', true));
    $this->topics_submissions = new Google_TopicsSubmissionsServiceResource($this, $this->serviceName, 'submissions', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "includeVotes": {"type": "boolean", "location": "query"}, "topicId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}, "author": {"type": "string", "location": "query"}, "sort": {"type": "string", "location": "query"}, "q": {"type": "string", "location": "query"}, "hasAttachedVideo": {"type": "boolean", "location": "query"}}, "id": "moderator.topics.submissions.list", "httpMethod": "GET", "path": "series/{seriesId}/topics/{topicId}/submissions", "response": {"$ref": "SubmissionList"}}}}', true));
    $this->global_series = new Google_ModeratorGlobalSeriesServiceResource($this, $this->serviceName, 'series', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"max-results": {"type": "integer", "location": "query", "format": "uint32"}, "q": {"type": "string", "location": "query"}, "start-index": {"type": "integer", "location": "query", "format": "uint32"}}, "response": {"$ref": "SeriesList"}, "httpMethod": "GET", "path": "search", "id": "moderator.global.series.list"}}}', true));
    $this->profiles = new Google_ProfilesServiceResource($this, $this->serviceName, 'profiles', json_decode('{"methods": {"patch": {"scopes": ["https://www.googleapis.com/auth/moderator"], "request": {"$ref": "Profile"}, "response": {"$ref": "Profile"}, "httpMethod": "PATCH", "path": "profiles/@me", "id": "moderator.profiles.patch"}, "update": {"scopes": ["https://www.googleapis.com/auth/moderator"], "request": {"$ref": "Profile"}, "response": {"$ref": "Profile"}, "httpMethod": "PUT", "path": "profiles/@me", "id": "moderator.profiles.update"}, "get": {"scopes": ["https://www.googleapis.com/auth/moderator"], "path": "profiles/@me", "response": {"$ref": "Profile"}, "id": "moderator.profiles.get", "httpMethod": "GET"}}}', true));
    $this->featured_series = new Google_FeaturedSeriesServiceResource($this, $this->serviceName, 'series', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "path": "series/featured", "response": {"$ref": "SeriesList"}, "id": "moderator.featured.series.list", "httpMethod": "GET"}}}', true));
    $this->myrecent_series = new Google_MyrecentSeriesServiceResource($this, $this->serviceName, 'series', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "path": "series/@me/recent", "response": {"$ref": "SeriesList"}, "id": "moderator.myrecent.series.list", "httpMethod": "GET"}}}', true));
    $this->my_series = new Google_MySeriesServiceResource($this, $this->serviceName, 'series', json_decode('{"methods": {"list": {"scopes": ["https://www.googleapis.com/auth/moderator"], "path": "series/@me/mine", "response": {"$ref": "SeriesList"}, "id": "moderator.my.series.list", "httpMethod": "GET"}}}', true));
    $this->submissions = new Google_SubmissionsServiceResource($this, $this->serviceName, 'submissions', json_decode('{"methods": {"insert": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "topicId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "unauthToken": {"type": "string", "location": "query"}, "anonymous": {"type": "boolean", "location": "query"}}, "request": {"$ref": "Submission"}, "response": {"$ref": "Submission"}, "httpMethod": "POST", "path": "series/{seriesId}/topics/{topicId}/submissions", "id": "moderator.submissions.insert"}, "get": {"scopes": ["https://www.googleapis.com/auth/moderator"], "parameters": {"lang": {"type": "string", "location": "query"}, "seriesId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "submissionId": {"required": true, "type": "integer", "location": "path", "format": "uint32"}, "includeVotes": {"type": "boolean", "location": "query"}}, "id": "moderator.submissions.get", "httpMethod": "GET", "path": "series/{seriesId}/submissions/{submissionId}", "response": {"$ref": "Submission"}}}}', true));

  }
}

class Google_ModeratorTopicsResourcePartial extends Google_Model {
  protected $__idType = 'Google_ModeratorTopicsResourcePartialId';
  protected $__idDataType = '';
  public $id;
  public function setId(Google_ModeratorTopicsResourcePartialId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_ModeratorTopicsResourcePartialId extends Google_Model {
  public $seriesId;
  public $topicId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
  public function setTopicId($topicId) {
    $this->topicId = $topicId;
  }
  public function getTopicId() {
    return $this->topicId;
  }
}

class Google_ModeratorVotesResourcePartial extends Google_Model {
  public $vote;
  public $flag;
  public function setVote($vote) {
    $this->vote = $vote;
  }
  public function getVote() {
    return $this->vote;
  }
  public function setFlag($flag) {
    $this->flag = $flag;
  }
  public function getFlag() {
    return $this->flag;
  }
}

class Google_Profile extends Google_Model {
  public $kind;
  protected $__attributionType = 'Google_ProfileAttribution';
  protected $__attributionDataType = '';
  public $attribution;
  protected $__idType = 'Google_ProfileId';
  protected $__idDataType = '';
  public $id;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setAttribution(Google_ProfileAttribution $attribution) {
    $this->attribution = $attribution;
  }
  public function getAttribution() {
    return $this->attribution;
  }
  public function setId(Google_ProfileId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_ProfileAttribution extends Google_Model {
  protected $__geoType = 'Google_ProfileAttributionGeo';
  protected $__geoDataType = '';
  public $geo;
  public $displayName;
  public $location;
  public $avatarUrl;
  public function setGeo(Google_ProfileAttributionGeo $geo) {
    $this->geo = $geo;
  }
  public function getGeo() {
    return $this->geo;
  }
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setAvatarUrl($avatarUrl) {
    $this->avatarUrl = $avatarUrl;
  }
  public function getAvatarUrl() {
    return $this->avatarUrl;
  }
}

class Google_ProfileAttributionGeo extends Google_Model {
  public $latitude;
  public $location;
  public $longitude;
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }
  public function getLatitude() {
    return $this->latitude;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }
  public function getLongitude() {
    return $this->longitude;
  }
}

class Google_ProfileId extends Google_Model {
  public $user;
  public function setUser($user) {
    $this->user = $user;
  }
  public function getUser() {
    return $this->user;
  }
}

class Google_Series extends Google_Model {
  public $kind;
  public $description;
  protected $__rulesType = 'Google_SeriesRules';
  protected $__rulesDataType = '';
  public $rules;
  public $unauthVotingAllowed;
  public $videoSubmissionAllowed;
  public $name;
  public $numTopics;
  public $anonymousSubmissionAllowed;
  public $unauthSubmissionAllowed;
  protected $__idType = 'Google_SeriesId';
  protected $__idDataType = '';
  public $id;
  protected $__countersType = 'Google_SeriesCounters';
  protected $__countersDataType = '';
  public $counters;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setRules(Google_SeriesRules $rules) {
    $this->rules = $rules;
  }
  public function getRules() {
    return $this->rules;
  }
  public function setUnauthVotingAllowed($unauthVotingAllowed) {
    $this->unauthVotingAllowed = $unauthVotingAllowed;
  }
  public function getUnauthVotingAllowed() {
    return $this->unauthVotingAllowed;
  }
  public function setVideoSubmissionAllowed($videoSubmissionAllowed) {
    $this->videoSubmissionAllowed = $videoSubmissionAllowed;
  }
  public function getVideoSubmissionAllowed() {
    return $this->videoSubmissionAllowed;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
  public function setNumTopics($numTopics) {
    $this->numTopics = $numTopics;
  }
  public function getNumTopics() {
    return $this->numTopics;
  }
  public function setAnonymousSubmissionAllowed($anonymousSubmissionAllowed) {
    $this->anonymousSubmissionAllowed = $anonymousSubmissionAllowed;
  }
  public function getAnonymousSubmissionAllowed() {
    return $this->anonymousSubmissionAllowed;
  }
  public function setUnauthSubmissionAllowed($unauthSubmissionAllowed) {
    $this->unauthSubmissionAllowed = $unauthSubmissionAllowed;
  }
  public function getUnauthSubmissionAllowed() {
    return $this->unauthSubmissionAllowed;
  }
  public function setId(Google_SeriesId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setCounters(Google_SeriesCounters $counters) {
    $this->counters = $counters;
  }
  public function getCounters() {
    return $this->counters;
  }
}

class Google_SeriesCounters extends Google_Model {
  public $users;
  public $noneVotes;
  public $videoSubmissions;
  public $minusVotes;
  public $anonymousSubmissions;
  public $submissions;
  public $plusVotes;
  public function setUsers($users) {
    $this->users = $users;
  }
  public function getUsers() {
    return $this->users;
  }
  public function setNoneVotes($noneVotes) {
    $this->noneVotes = $noneVotes;
  }
  public function getNoneVotes() {
    return $this->noneVotes;
  }
  public function setVideoSubmissions($videoSubmissions) {
    $this->videoSubmissions = $videoSubmissions;
  }
  public function getVideoSubmissions() {
    return $this->videoSubmissions;
  }
  public function setMinusVotes($minusVotes) {
    $this->minusVotes = $minusVotes;
  }
  public function getMinusVotes() {
    return $this->minusVotes;
  }
  public function setAnonymousSubmissions($anonymousSubmissions) {
    $this->anonymousSubmissions = $anonymousSubmissions;
  }
  public function getAnonymousSubmissions() {
    return $this->anonymousSubmissions;
  }
  public function setSubmissions($submissions) {
    $this->submissions = $submissions;
  }
  public function getSubmissions() {
    return $this->submissions;
  }
  public function setPlusVotes($plusVotes) {
    $this->plusVotes = $plusVotes;
  }
  public function getPlusVotes() {
    return $this->plusVotes;
  }
}

class Google_SeriesId extends Google_Model {
  public $seriesId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
}

class Google_SeriesList extends Google_Model {
  protected $__itemsType = 'Google_Series';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Series) */ $items) {
    $this->assertIsArray($items, 'Google_Series', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_SeriesRules extends Google_Model {
  protected $__votesType = 'Google_SeriesRulesVotes';
  protected $__votesDataType = '';
  public $votes;
  protected $__submissionsType = 'Google_SeriesRulesSubmissions';
  protected $__submissionsDataType = '';
  public $submissions;
  public function setVotes(Google_SeriesRulesVotes $votes) {
    $this->votes = $votes;
  }
  public function getVotes() {
    return $this->votes;
  }
  public function setSubmissions(Google_SeriesRulesSubmissions $submissions) {
    $this->submissions = $submissions;
  }
  public function getSubmissions() {
    return $this->submissions;
  }
}

class Google_SeriesRulesSubmissions extends Google_Model {
  public $close;
  public $open;
  public function setClose($close) {
    $this->close = $close;
  }
  public function getClose() {
    return $this->close;
  }
  public function setOpen($open) {
    $this->open = $open;
  }
  public function getOpen() {
    return $this->open;
  }
}

class Google_SeriesRulesVotes extends Google_Model {
  public $close;
  public $open;
  public function setClose($close) {
    $this->close = $close;
  }
  public function getClose() {
    return $this->close;
  }
  public function setOpen($open) {
    $this->open = $open;
  }
  public function getOpen() {
    return $this->open;
  }
}

class Google_Submission extends Google_Model {
  public $kind;
  protected $__attributionType = 'Google_SubmissionAttribution';
  protected $__attributionDataType = '';
  public $attribution;
  public $created;
  public $text;
  protected $__topicsType = 'Google_ModeratorTopicsResourcePartial';
  protected $__topicsDataType = 'array';
  public $topics;
  public $author;
  protected $__translationsType = 'Google_SubmissionTranslations';
  protected $__translationsDataType = 'array';
  public $translations;
  protected $__parentSubmissionIdType = 'Google_SubmissionParentSubmissionId';
  protected $__parentSubmissionIdDataType = '';
  public $parentSubmissionId;
  protected $__voteType = 'Google_ModeratorVotesResourcePartial';
  protected $__voteDataType = '';
  public $vote;
  public $attachmentUrl;
  protected $__geoType = 'Google_SubmissionGeo';
  protected $__geoDataType = '';
  public $geo;
  protected $__idType = 'Google_SubmissionId';
  protected $__idDataType = '';
  public $id;
  protected $__countersType = 'Google_SubmissionCounters';
  protected $__countersDataType = '';
  public $counters;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setAttribution(Google_SubmissionAttribution $attribution) {
    $this->attribution = $attribution;
  }
  public function getAttribution() {
    return $this->attribution;
  }
  public function setCreated($created) {
    $this->created = $created;
  }
  public function getCreated() {
    return $this->created;
  }
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
  public function setTopics(/* array(Google_ModeratorTopicsResourcePartial) */ $topics) {
    $this->assertIsArray($topics, 'Google_ModeratorTopicsResourcePartial', __METHOD__);
    $this->topics = $topics;
  }
  public function getTopics() {
    return $this->topics;
  }
  public function setAuthor($author) {
    $this->author = $author;
  }
  public function getAuthor() {
    return $this->author;
  }
  public function setTranslations(/* array(Google_SubmissionTranslations) */ $translations) {
    $this->assertIsArray($translations, 'Google_SubmissionTranslations', __METHOD__);
    $this->translations = $translations;
  }
  public function getTranslations() {
    return $this->translations;
  }
  public function setParentSubmissionId(Google_SubmissionParentSubmissionId $parentSubmissionId) {
    $this->parentSubmissionId = $parentSubmissionId;
  }
  public function getParentSubmissionId() {
    return $this->parentSubmissionId;
  }
  public function setVote(Google_ModeratorVotesResourcePartial $vote) {
    $this->vote = $vote;
  }
  public function getVote() {
    return $this->vote;
  }
  public function setAttachmentUrl($attachmentUrl) {
    $this->attachmentUrl = $attachmentUrl;
  }
  public function getAttachmentUrl() {
    return $this->attachmentUrl;
  }
  public function setGeo(Google_SubmissionGeo $geo) {
    $this->geo = $geo;
  }
  public function getGeo() {
    return $this->geo;
  }
  public function setId(Google_SubmissionId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setCounters(Google_SubmissionCounters $counters) {
    $this->counters = $counters;
  }
  public function getCounters() {
    return $this->counters;
  }
}

class Google_SubmissionAttribution extends Google_Model {
  public $displayName;
  public $location;
  public $avatarUrl;
  public function setDisplayName($displayName) {
    $this->displayName = $displayName;
  }
  public function getDisplayName() {
    return $this->displayName;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setAvatarUrl($avatarUrl) {
    $this->avatarUrl = $avatarUrl;
  }
  public function getAvatarUrl() {
    return $this->avatarUrl;
  }
}

class Google_SubmissionCounters extends Google_Model {
  public $noneVotes;
  public $minusVotes;
  public $plusVotes;
  public function setNoneVotes($noneVotes) {
    $this->noneVotes = $noneVotes;
  }
  public function getNoneVotes() {
    return $this->noneVotes;
  }
  public function setMinusVotes($minusVotes) {
    $this->minusVotes = $minusVotes;
  }
  public function getMinusVotes() {
    return $this->minusVotes;
  }
  public function setPlusVotes($plusVotes) {
    $this->plusVotes = $plusVotes;
  }
  public function getPlusVotes() {
    return $this->plusVotes;
  }
}

class Google_SubmissionGeo extends Google_Model {
  public $latitude;
  public $location;
  public $longitude;
  public function setLatitude($latitude) {
    $this->latitude = $latitude;
  }
  public function getLatitude() {
    return $this->latitude;
  }
  public function setLocation($location) {
    $this->location = $location;
  }
  public function getLocation() {
    return $this->location;
  }
  public function setLongitude($longitude) {
    $this->longitude = $longitude;
  }
  public function getLongitude() {
    return $this->longitude;
  }
}

class Google_SubmissionId extends Google_Model {
  public $seriesId;
  public $submissionId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
  public function setSubmissionId($submissionId) {
    $this->submissionId = $submissionId;
  }
  public function getSubmissionId() {
    return $this->submissionId;
  }
}

class Google_SubmissionList extends Google_Model {
  protected $__itemsType = 'Google_Submission';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Submission) */ $items) {
    $this->assertIsArray($items, 'Google_Submission', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_SubmissionParentSubmissionId extends Google_Model {
  public $seriesId;
  public $submissionId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
  public function setSubmissionId($submissionId) {
    $this->submissionId = $submissionId;
  }
  public function getSubmissionId() {
    return $this->submissionId;
  }
}

class Google_SubmissionTranslations extends Google_Model {
  public $lang;
  public $text;
  public function setLang($lang) {
    $this->lang = $lang;
  }
  public function getLang() {
    return $this->lang;
  }
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
}

class Google_Tag extends Google_Model {
  public $text;
  public $kind;
  protected $__idType = 'Google_TagId';
  protected $__idDataType = '';
  public $id;
  public function setText($text) {
    $this->text = $text;
  }
  public function getText() {
    return $this->text;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setId(Google_TagId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
}

class Google_TagId extends Google_Model {
  public $seriesId;
  public $tagId;
  public $submissionId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
  public function setTagId($tagId) {
    $this->tagId = $tagId;
  }
  public function getTagId() {
    return $this->tagId;
  }
  public function setSubmissionId($submissionId) {
    $this->submissionId = $submissionId;
  }
  public function getSubmissionId() {
    return $this->submissionId;
  }
}

class Google_TagList extends Google_Model {
  protected $__itemsType = 'Google_Tag';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Tag) */ $items) {
    $this->assertIsArray($items, 'Google_Tag', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_Topic extends Google_Model {
  public $kind;
  public $description;
  protected $__rulesType = 'Google_TopicRules';
  protected $__rulesDataType = '';
  public $rules;
  protected $__featuredSubmissionType = 'Google_Submission';
  protected $__featuredSubmissionDataType = '';
  public $featuredSubmission;
  public $presenter;
  protected $__countersType = 'Google_TopicCounters';
  protected $__countersDataType = '';
  public $counters;
  protected $__idType = 'Google_TopicId';
  protected $__idDataType = '';
  public $id;
  public $name;
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
  public function setDescription($description) {
    $this->description = $description;
  }
  public function getDescription() {
    return $this->description;
  }
  public function setRules(Google_TopicRules $rules) {
    $this->rules = $rules;
  }
  public function getRules() {
    return $this->rules;
  }
  public function setFeaturedSubmission(Google_Submission $featuredSubmission) {
    $this->featuredSubmission = $featuredSubmission;
  }
  public function getFeaturedSubmission() {
    return $this->featuredSubmission;
  }
  public function setPresenter($presenter) {
    $this->presenter = $presenter;
  }
  public function getPresenter() {
    return $this->presenter;
  }
  public function setCounters(Google_TopicCounters $counters) {
    $this->counters = $counters;
  }
  public function getCounters() {
    return $this->counters;
  }
  public function setId(Google_TopicId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setName($name) {
    $this->name = $name;
  }
  public function getName() {
    return $this->name;
  }
}

class Google_TopicCounters extends Google_Model {
  public $users;
  public $noneVotes;
  public $videoSubmissions;
  public $minusVotes;
  public $submissions;
  public $plusVotes;
  public function setUsers($users) {
    $this->users = $users;
  }
  public function getUsers() {
    return $this->users;
  }
  public function setNoneVotes($noneVotes) {
    $this->noneVotes = $noneVotes;
  }
  public function getNoneVotes() {
    return $this->noneVotes;
  }
  public function setVideoSubmissions($videoSubmissions) {
    $this->videoSubmissions = $videoSubmissions;
  }
  public function getVideoSubmissions() {
    return $this->videoSubmissions;
  }
  public function setMinusVotes($minusVotes) {
    $this->minusVotes = $minusVotes;
  }
  public function getMinusVotes() {
    return $this->minusVotes;
  }
  public function setSubmissions($submissions) {
    $this->submissions = $submissions;
  }
  public function getSubmissions() {
    return $this->submissions;
  }
  public function setPlusVotes($plusVotes) {
    $this->plusVotes = $plusVotes;
  }
  public function getPlusVotes() {
    return $this->plusVotes;
  }
}

class Google_TopicId extends Google_Model {
  public $seriesId;
  public $topicId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
  public function setTopicId($topicId) {
    $this->topicId = $topicId;
  }
  public function getTopicId() {
    return $this->topicId;
  }
}

class Google_TopicList extends Google_Model {
  protected $__itemsType = 'Google_Topic';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Topic) */ $items) {
    $this->assertIsArray($items, 'Google_Topic', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_TopicRules extends Google_Model {
  protected $__votesType = 'Google_TopicRulesVotes';
  protected $__votesDataType = '';
  public $votes;
  protected $__submissionsType = 'Google_TopicRulesSubmissions';
  protected $__submissionsDataType = '';
  public $submissions;
  public function setVotes(Google_TopicRulesVotes $votes) {
    $this->votes = $votes;
  }
  public function getVotes() {
    return $this->votes;
  }
  public function setSubmissions(Google_TopicRulesSubmissions $submissions) {
    $this->submissions = $submissions;
  }
  public function getSubmissions() {
    return $this->submissions;
  }
}

class Google_TopicRulesSubmissions extends Google_Model {
  public $close;
  public $open;
  public function setClose($close) {
    $this->close = $close;
  }
  public function getClose() {
    return $this->close;
  }
  public function setOpen($open) {
    $this->open = $open;
  }
  public function getOpen() {
    return $this->open;
  }
}

class Google_TopicRulesVotes extends Google_Model {
  public $close;
  public $open;
  public function setClose($close) {
    $this->close = $close;
  }
  public function getClose() {
    return $this->close;
  }
  public function setOpen($open) {
    $this->open = $open;
  }
  public function getOpen() {
    return $this->open;
  }
}

class Google_Vote extends Google_Model {
  public $vote;
  public $flag;
  protected $__idType = 'Google_VoteId';
  protected $__idDataType = '';
  public $id;
  public $kind;
  public function setVote($vote) {
    $this->vote = $vote;
  }
  public function getVote() {
    return $this->vote;
  }
  public function setFlag($flag) {
    $this->flag = $flag;
  }
  public function getFlag() {
    return $this->flag;
  }
  public function setId(Google_VoteId $id) {
    $this->id = $id;
  }
  public function getId() {
    return $this->id;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}

class Google_VoteId extends Google_Model {
  public $seriesId;
  public $submissionId;
  public function setSeriesId($seriesId) {
    $this->seriesId = $seriesId;
  }
  public function getSeriesId() {
    return $this->seriesId;
  }
  public function setSubmissionId($submissionId) {
    $this->submissionId = $submissionId;
  }
  public function getSubmissionId() {
    return $this->submissionId;
  }
}

class Google_VoteList extends Google_Model {
  protected $__itemsType = 'Google_Vote';
  protected $__itemsDataType = 'array';
  public $items;
  public $kind;
  public function setItems(/* array(Google_Vote) */ $items) {
    $this->assertIsArray($items, 'Google_Vote', __METHOD__);
    $this->items = $items;
  }
  public function getItems() {
    return $this->items;
  }
  public function setKind($kind) {
    $this->kind = $kind;
  }
  public function getKind() {
    return $this->kind;
  }
}
