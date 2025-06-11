import {BrowserModule} from '@angular/platform-browser';
import {CUSTOM_ELEMENTS_SCHEMA, Injector, NgModule} from '@angular/core';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';

import {AppComponent} from './app.component';
import {FeedComponent} from './feed/feed.component';
import {createCustomElement} from "@angular/elements";
import {HttpClientModule} from "@angular/common/http";
import {FeedErrorModalComponent} from "./feed-error-modal/feed-error-modal.component";

@NgModule({
  declarations: [
    AppComponent,
    FeedComponent,
    FeedErrorModalComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    HttpClientModule,
  ],
  providers: [],
  schemas: [CUSTOM_ELEMENTS_SCHEMA],
})
export class AppModule {
  constructor(private injector: Injector) {
  }

  ngDoBootstrap() {
    // FeedComponent custom element.
    const feedCE = createCustomElement(FeedComponent, {injector: this.injector});
    customElements.define('snap-feed', feedCE);
    const modErrCE = createCustomElement(FeedErrorModalComponent, {injector: this.injector});
    customElements.define('feed-error-modal', modErrCE);
  }
}
