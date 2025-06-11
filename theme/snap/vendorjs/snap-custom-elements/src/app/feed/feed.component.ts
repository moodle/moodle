import {Component, OnInit, ViewEncapsulation, Input, HostListener} from '@angular/core';

import {FeedService} from '../feed.service';
import {FeedItem} from "../feed-item";
import {animate, query, stagger, style, transition, trigger} from "@angular/animations";
import {StringService} from "../string.service";
import {MoodleService} from "../moodle.service";
import {ErrorReporterService} from "../error-reporter.service";
import {FeedErrorModalComponent} from "../feed-error-modal/feed-error-modal.component"
import {MoodleRes} from "../moodle.res";

@Component({
  selector: 'snap-feed',
  template: `
      <h2>{{ title }}</h2>
      <div id="{{ elemId }}" [@growIn]="feedItemTotal">
          <div class="snap-media-object feeditem {{feedItem.extraClasses}}" *ngFor="let feedItem of feedItems" [attr.data-from-cache]="feedItem.fromCache" [attr.data-mod-name]="feedItem.modName">
              <img *ngIf="feedItem.iconUrl !== ''" src="{{feedItem.iconUrl}}" alt="{{feedItem.iconDesc}}" [className]="feedItem.iconClass" [attr.data-mod-name]="feedItem.modName">
              <div class="snap-media-body">
                  <a [attr.href]="feedItem.urlParameter ? feedItem.actionUrl + '&snapfeedsclicked=on' : feedItem.actionUrl">
                      <h3>
                          {{feedItem.title}}
                          <small [attr.data-from-cache]="feedItem.fromCache">
                              <br>
                              {{feedItem.subTitle}}
                          </small>
                      </h3>
                  </a>
                  <span *ngIf="feedItem.description" class="snap-media-meta" [innerHTML]="feedItem.description">
                  </span>
              </div>
          </div>
          <p class="small" *ngIf="feedItemTotal == 0">{{emptyMessage}}</p>
      </div>
      <div class="alert alert-danger alert-block fade in" role="alert" *ngIf="feedError === true && !retryFeed">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">×</button>
        {{strings['errorgettingfeed']}}
        <a
          *ngIf="errorMsg && feedError"
          class="view-more-error"
          (click)="reportError(errorMsg)"
        >
          {{strings['viewmore']}}
        </a>
      </div>
      <a *ngIf="viewMoreEnabled && nextPage >= 0" href="javascript: void(0);" class="snap-sidebar-menu-more"
         (click)="getFeed($event)" title="{{ viewMoreMessage }} {{ title.toLowerCase() }}">
          <small>{{viewMoreMessage}}</small>
          <i class="snap-feeds-more-icon fa fa-caret-down"></i>
      </a>
      <a *ngIf="nextPage === -1 && showReload" href="javascript: void(0);" class="snap-sidebar-menu-more"
         (click)="purgeDataAndResetFeed()" title="{{ reloadMessage }} {{ title.toLowerCase() }}">
          <small>{{reloadMessage}}</small>
          <i class="snap-feeds-refresh-icon fa fa-refresh"></i>
      </a>
      <span *ngIf="fetchingData" class="snap-sidebar-menu-more snap-sidebar-menu-feed-loading">
        <a class="small text-muted">{{loadingFeed}} </a>
      </span>
  `,
  animations: [
    trigger('growIn', [
      transition(':increment', [
        query(':enter', [
          style({opacity: 0, transform: 'translateY(-100px)', height: 0}),
          stagger(100, [
            animate('500ms cubic-bezier(0.35, 0, 0.25, 1)',
              style({opacity: 1, transform: 'none',  height: '*'}))
          ])
        ], {optional: true})
      ]),
      transition(':decrement', [
        query(':leave', [
          stagger(50, [
            animate('300ms ease-out', style({ opacity: 0, height: '0px' })),
          ]),
        ], {optional: true})
      ])
    ])
  ],
  styles: [`
    .view-more-error {
    cursor: pointer;
  }`],
  encapsulation: ViewEncapsulation.None
})
export class FeedComponent implements OnInit {
  @Input() elemId: string;
  @Input() title: string;
  @Input() feedId: string;
  @Input() sessKey: string;
  @Input() showReload?: boolean;
  @Input() virtualPaging?: boolean;
  @Input() pageSize: number;
  @Input() emptyMessage: string;
  @Input() viewMoreMessage: string;
  @Input() reloadMessage: string;
  @Input() initialValue?: string;
  @Input() wwwRoot: string;
  @Input() maxLifeTime?: number;
  @Input() courseId?: number;
  @Input() loadingFeed?: string;

  nextPage: number;
  feedItems: FeedItem[];
  feedItemTotal: number;
  resetInProgress: boolean;
  viewMoreEnabled: boolean;
  fetchingData: boolean;
  feedError: boolean;
  retryFeed: boolean;
  errorMsg: string;
  strings: string[];


  private feedItemCache: FeedItem[];

  constructor(
    private feedService: FeedService,
    private stringService: StringService,
    private moodleService: MoodleService,
    private errorReporterService: ErrorReporterService,
  ) {
  }

  ngOnInit() {
    this.moodleService.sessKey = this.sessKey;
    this.moodleService.wwwRoot = this.wwwRoot;
    this.feedError = false;
    this.errorMsg = null;
    this.getStrings();

    // Initialize caching for feed service.
    this.initFeedService();

    this.feedItems = [];
    if (this.initialValue) {
      let initialItems = JSON.parse(this.decodeHtmlSpecialChars(this.initialValue));
      if (initialItems.length > 0) {
        this.nextPage = 0;
        this.feedItemCache = [];
        this.resetInProgress = true;

        if (this.virtualPaging) {
          this.feedItemCache = initialItems;
          this.nextPage = 1;
          this.processVirtualPaging();
        } else {
          this.processNextPage(initialItems);
        }
      } else {
        this.feedItemTotal = 0;
        this.nextPage = -1;
      }
    } else {
      if (document.querySelectorAll('#snap_feeds_side_menu .snap-feeds').length > 0) {
        this.resetFeed();
      }
    }
  }

  /**
   * Purge all data from previous session and set max life time for new data.
   */
  private initFeedService() {
    this.feedService.purgeOtherDataInLocalCache(this.sessKey);

    if (this.maxLifeTime !== null && this.maxLifeTime !== undefined && this.maxLifeTime >= 0) {
      this.feedService.setMaxLifeTime(+this.maxLifeTime); // Adding the plus sign b/c sometimes this.maxLifeTime is a string.
    }
  }

  getFeed(event?: Event): void {
    this.viewMoreEnabled = false;
    this.fetchingData = true;
    this.feedError = false;
    this.errorMsg = null;
    this.retryFeed = false;

    if (event) {
      event.preventDefault();
    }

    if (this.processVirtualPaging()) {
      this.viewMoreEnabled = true;
      this.fetchingData = false;
      return;
    }
    const maxId: number = !this.resetInProgress && this.feedItems[0] && this.feedItems[0].itemId || -1;
    this.feedService.getFeed(this.wwwRoot, this.sessKey, this.feedId, this.nextPage, this.pageSize, maxId, this.courseId)
      .subscribe(feedResponse => {
        if (feedResponse === undefined || feedResponse[0].error) {
          this.viewMoreEnabled = false;

          this.feedError = true;
          if (feedResponse[0].exception && feedResponse[0].exception.errorcode === 'retryfeed') {
            this.retryFeed = true;
            setTimeout(() => {
              this.getFeed();
            }, 5000);
          } else {
            this.fetchingData = false;
          }
          let singleMoodleRes: any = feedResponse[0];
          this.errorMsg = singleMoodleRes;
          return;
        }

        const data: FeedItem[] = feedResponse[0].data;

        if (this.virtualPaging && data.length > 0) {
          this.feedItemCache = data;
          this.nextPage = 1;
          this.processVirtualPaging();
        } else {
          this.processNextPage(data);
        }

        this.viewMoreEnabled = true;
        this.fetchingData = false;
      });
  }

  processVirtualPaging() : boolean {
    if (!this.virtualPaging || this.feedItemCache.length === 0) {
      return false;
    }

    if (this.nextPage < 0) {
      return true;
    }

    const pageSize = +this.pageSize; // Somehow this.pageSize is sometimes a string.
    const totalItems: number = this.feedItemCache.length;
    const nextStartIdx: number = pageSize * (this.nextPage - 1);
    const lastIdx: number = totalItems;
    if (nextStartIdx <= lastIdx) {
      let start: number = nextStartIdx,
          end: number = nextStartIdx + pageSize;
      end = end > lastIdx ? lastIdx : end;
      const newFeedItems: FeedItem[] = this.feedItemCache.slice(start, end);
      this.processNextPage(newFeedItems);
    }

    return true;
  }

  processNextPage(data: FeedItem[]) {
    if (this.resetInProgress) {
      this.feedItems = [];
      this.resetInProgress = false;
    }

    this.feedItems = this.feedItems.concat(data);
    this.feedItemTotal = this.feedItems.length;
    if (data.length == this.pageSize) {
      this.nextPage++;
    } else {
      this.nextPage = -1;
    }
  }

  @HostListener('document:snapPersonalMenuOpen', ['$event'])
  resetFeed(event?: Event): void {
    if (event) {
      event.preventDefault();
    }

    if (this.resetInProgress) {
      return;
    }

    this.nextPage = 0;
    this.feedItemCache = [];
    this.resetInProgress = true;

    this.getFeed();
  }

  purgeDataAndResetFeed(event?: Event): void {
    if (event) {
      event.preventDefault();
    }

    const lastPage = this.nextPage > 0 ? this.nextPage : 1;
    for (let page = 0; page < lastPage; page++) {
      this.feedService.purgeDataInLocalCache(this.sessKey, this.feedId, page, this.pageSize, this.courseId);
    }
    this.resetFeed();
  }

  decodeHtmlSpecialChars(str: string): string {
    const map = {
      '&amp;': '&',
      '&#038;': "&",
      '&lt;': '<',
      '&gt;': '>',
      '&quot;': '"',
      '&#039;': "'",
      '&#8217;': "’",
      '&#8216;': "‘",
      '&#8211;': "–",
      '&#8212;': "—",
      '&#8230;': "…",
      '&#8221;': '”'
    };

    return str.replace(/\&[\w\d\#]{2,5}\;/g, function (m) {
      return map[m];
    });
  }

  getStrings() {
    this.strings = [];
    this.stringService.getStrings([
      'viewmore',
      'errorgettingfeed'
    ]).subscribe(strings => {
      this.strings = strings;
    });
  }

  reportError(error: string) {
    this.errorReporterService.relayError(error);
  }
}
