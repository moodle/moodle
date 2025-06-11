import { TestBed } from '@angular/core/testing';

import { MoodleService } from './moodle.service';
import {of} from "rxjs";
import {ErrorReporterService} from "./error-reporter.service";
import {MockErrorReporterService} from "./error-reporter.service.spec";
import {HttpClient} from "@angular/common/http";

export class MockMoodleService {

}
export class MockHttpClient {

}
describe('MoodleService', () => {
  beforeEach(() => TestBed.configureTestingModule({
    providers: [
      {
        provide: ErrorReporterService,
        useClass: MockErrorReporterService
      },
      {
        provide: HttpClient,
        useClass: MockHttpClient
      }
    ]
  }));

  it('should be created', () => {
    const service: MoodleService = TestBed.get(MoodleService);
    expect(service).toBeTruthy();
  });
});
