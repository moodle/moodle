import { TestBed } from '@angular/core/testing';

import { FeedService } from './feed.service';
import {HttpClientTestingModule, HttpTestingController} from "@angular/common/http/testing";
import {MoodleRes} from "./moodle.res";
import {MoodleResKey} from "./moodle-res-key";
import {FeedServiceArgs} from "./feed-service-args";
import {CachedMoodleRes} from "./cached-moodle-res";
import {StringService} from "./string.service";
import {MockStringService} from "./string.service.spec";
import {ErrorReporterService} from "./error-reporter.service";
import {MockErrorReporterService} from "./error-reporter.service.spec";

describe('FeedService', () => {
  let feedService: FeedService;
  let httpTestingController: HttpTestingController;

  // Fixtures.

  // Empty request result.
  const emptyTestData: MoodleRes[]= [{
    error: false,
    data: []
  }];

  // Test messages feed data.
  const testData: MoodleRes[] = [{
    "error": false,
    "data": [{
      "iconUrl": "http:\/\/moodle.test\/pluginfile.php\/1099\/user\/icon\/snap\/f1?rev=1",
      "iconDesc": "",
      "iconClass": "userpicture",
      "title": "Brandi Gonzales",
      "subTitle": "All is good, Thanks!",
      "actionUrl": "http:\/\/moodle.test\/message\/index.php?history=0&amp;user1=22&amp;user2=4",
      "description": "<time is=\"relative-time\" datetime=\"2020-06-25T08:22:39-07:00\">1 min ago<\/time>",
      "extraClasses": "",
      "fromCache": 0,
      "itemId": 5
    }, {
      "iconUrl": "http:\/\/moodle.test\/pluginfile.php\/1099\/user\/icon\/snap\/f1?rev=1",
      "iconDesc": "",
      "iconClass": "userpicture",
      "title": "Brandi Gonzales",
      "subTitle": "How are you doing?",
      "actionUrl": "http:\/\/moodle.test\/message\/index.php?history=0&amp;user1=22&amp;user2=4",
      "description": "<time is=\"relative-time\" datetime=\"2020-06-25T08:18:29-07:00\">6 mins ago<\/time>",
      "extraClasses": "",
      "fromCache": 0,
      "itemId": 2
    },  {
      "iconUrl": "http:\/\/moodle.test\/pluginfile.php\/1099\/user\/icon\/snap\/f1?rev=1",
      "iconDesc": "",
      "iconClass": "userpicture",
      "title": "Brandi Gonzales",
      "subTitle": "That is great to know!",
      "actionUrl": "http:\/\/moodle.test\/message\/index.php?history=0&amp;user1=22&amp;user2=4",
      "description": "<time is=\"relative-time\" datetime=\"2020-06-25T08:20:01-07:00\">8 mins ago<\/time>",
      "extraClasses": "",
      "fromCache": 0,
      "itemId": 2
    }]
  }];

  beforeEach(() => {
    TestBed.configureTestingModule({
      imports: [HttpClientTestingModule],
      providers: [
        {
          provide: ErrorReporterService,
          useClass: MockErrorReporterService
        }
      ]
    });

    httpTestingController = TestBed.inject(HttpTestingController);

    feedService = TestBed.inject(FeedService);
    // Default cache value is 30 mins, so the following request should be cached.
    feedService.setMaxLifeTime(1800);
  });

  it('should be created', () => {
    expect(feedService).toBeTruthy();
  });

  it('caches data when request is sent', () => {
    const sessKey = 'TestSessionCacheTest';
    const testUrl = `http://moodle.test/lib/ajax/service.php?sesskey=${sessKey}`

    feedService.getFeed('http://moodle.test', sessKey, 'messages', 0, 3, -1, 0)
      .subscribe(data =>
        // When observable resolves, result should match test data.
        expect(data).toEqual(testData)
      );

    const req = httpTestingController.expectOne(testUrl);

    // Assert that the request is a POST.
    expect(req.request.method).toEqual('POST');

    // Respond with mock data, causing Observable to resolve.
    // Subscribe callback asserts that correct data was returned.
    req.flush(testData);

    // Finally, assert that there are no outstanding requests.
    httpTestingController.verify();

    // We have a cached value, so the first request should linger as a result.
    feedService.getFeed('http://moodle.test', sessKey, 'messages', 0, 3, -1, 0)
      .subscribe(data =>
        // When observable resolves, result should match test data.
        expect(data).toEqual(testData)
      );

    // Resolve and expect no request was made.
    httpTestingController.expectNone(testUrl);

    // Purge the caches.
    feedService.purgeDataInLocalCache(sessKey, 'messages', 0, 3, 0);

    // We have a cached value, so the first request should linger as a result.
    feedService.getFeed('http://moodle.test', sessKey, 'messages', 0, 3, -1, 0)
      .subscribe(data =>
        // When observable resolves, result should match test data.
        expect(data).toEqual(emptyTestData)
      );

    const newReq = httpTestingController.expectOne(testUrl);

    // Assert that the request is a POST.
    expect(newReq.request.method).toEqual('POST');

    // Respond with mock data, causing Observable to resolve.
    // Subscribe callback asserts that correct data was returned.
    newReq.flush(emptyTestData);

    // Finally, assert that there are no outstanding requests.
    httpTestingController.verify();
  });

  it('does not cache if max life time is 0', () => {
    const sessKey = 'TestSessionCacheZeroLifeTimeTest';
    const testUrl = `http://moodle.test/lib/ajax/service.php?sesskey=${sessKey}`
    // Now we set 0 so there is no caching.
    feedService.setMaxLifeTime(0);

    feedService.getFeed('http://moodle.test', sessKey, 'messages', 0, 3, -1, 0)
      .subscribe(data =>
        // When observable resolves, result should match test data.
        expect(data).toEqual(testData)
      );

    let req = httpTestingController.expectOne(testUrl);

    // Assert that the request is a POST.
    expect(req.request.method).toEqual('POST');

    // Respond with mock data, causing Observable to resolve.
    // Subscribe callback asserts that correct data was returned.
    req.flush(testData);

    // Finally, assert that there are no outstanding requests.
    httpTestingController.verify();

    // We have a cached value, so the first request should linger as a result.
    feedService.getFeed('http://moodle.test', sessKey, 'messages', 0, 3, -1, 0)
      .subscribe(data =>
        // When observable resolves, result should match test data.
        expect(data).toEqual(testData)
      );

    req = httpTestingController.expectOne(testUrl);

    // Assert that the request is a POST.
    expect(req.request.method).toEqual('POST');

    // Respond with mock data, causing Observable to resolve.
    // Subscribe callback asserts that correct data was returned.
    req.flush(testData);

    // Finally, assert that there are no outstanding requests.
    httpTestingController.verify();
  });

  it('clears cache if max life time is reached', () => {
    const sessKey = 'TestSessionCacheMaxLifeTest';
    const testUrl = `http://moodle.test/lib/ajax/service.php?sesskey=${sessKey}`
    // We're manually creating a new request to store:
    const moodleResKey: MoodleResKey = new MoodleResKey();
    moodleResKey.sessKey = sessKey;

    const feedServiceArgs: FeedServiceArgs = new FeedServiceArgs();
    feedServiceArgs.feedid = 'messages';
    feedServiceArgs.page = 0;
    feedServiceArgs.pagesize = 3;

    moodleResKey.args = feedServiceArgs;

    const itemKey = feedService.createLocalCacheKey(moodleResKey);
    let cachedFeedRes: CachedMoodleRes = {
      timeCreated : (Date.now() / 1000) - 11, // 11 seconds ago.
      key : moodleResKey,
      result : testData,
    };
    localStorage.setItem(itemKey, JSON.stringify(cachedFeedRes));

    feedService.setMaxLifeTime(10); // 10 seconds.

    // We have a cached value, but it exeeds max life time, it should be cleared and a new request made.
    feedService.getFeed('http://moodle.test', sessKey, 'messages', 0, 3, -1, 0)
      .subscribe(data =>
        // When observable resolves, result should match test data.
        expect(data).toEqual(emptyTestData)
      );

    const newReq = httpTestingController.expectOne(testUrl);

    // Assert that the request is a POST.
    expect(newReq.request.method).toEqual('POST');

    // Respond with mock data, causing Observable to resolve.
    // Subscribe callback asserts that correct data was returned.
    newReq.flush(emptyTestData);

    // Finally, assert that there are no outstanding requests.
    httpTestingController.verify();
  });
});
