import { TestBed } from '@angular/core/testing';

import { StringService } from './string.service';
import {of} from "rxjs";
import {ErrorReporterService} from "./error-reporter.service";
import {MockErrorReporterService} from "./error-reporter.service.spec";
import {MoodleService} from "./moodle.service";
import {MockMoodleService} from "./moodle.service.spec";

export class MockStringService {
  getStrings(stringIds: string[]) {
    return of(stringIds)
  }
}

describe('StringService', () => {
  beforeEach(() => TestBed.configureTestingModule({
  }));
});
