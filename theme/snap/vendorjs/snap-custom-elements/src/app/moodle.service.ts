import {Injectable} from '@angular/core';

import {Observable, of} from 'rxjs';

import {HttpClient, HttpHeaders} from '@angular/common/http';

import {catchError, map, tap} from 'rxjs/operators';
import {MoodleRes} from "./moodle.res";
import {ErrorReporterService} from "./error-reporter.service";

@Injectable({
  providedIn: 'root'
})
export class MoodleService {

  protected moodleAjaxUrl = '/lib/ajax/service.php';  // URL to Moodle ajax api.

  protected httpOptions = {
    headers: new HttpHeaders({ 'Content-Type': 'application/json' })
  };

  public wwwRoot: string;
  public sessKey: string;

  constructor(
    private http: HttpClient,
    private errorReporterService: ErrorReporterService
  ) {
  }

  service(methodName: string, args: object): Observable<any> {
    let errorRes : MoodleRes[] = [{
      error: "No session key present",
      data: undefined
    }];
    if (!this.sessKey) {
      return of(errorRes);
    }

    errorRes = [{
      error: "No www root present",
      data: undefined
    }];
    if (!this.wwwRoot) {
      return of(errorRes);
    }

    let body = [{
      index: 0,
      methodname: methodName,
      args: args
    }];

    return this.http.post<MoodleRes[]>(`${this.wwwRoot}${this.moodleAjaxUrl}?sesskey=${this.sessKey}`, body, this.httpOptions)
      .pipe(
        tap(_ => this.log(`Consuming Moodle service ${methodName}`)),
        catchError(this.handleError<MoodleRes[]>(`Moodle service ${methodName}`, errorRes))
      );
  }

  services(methodName: string, args: object[]): Observable<any> {
    let errorRes : MoodleRes[] = [{
      error: "No session key present",
      data: undefined
    }];
    if (!this.sessKey) {
      return of(errorRes);
    }

    errorRes = [{
      error: "No www root present",
      data: undefined
    }];
    if (!this.wwwRoot) {
      return of(errorRes);
    }

    let body = [];

    for (let i = 0; i < args.length; i++) {
      body.push({
        index: i,
        methodname: methodName,
        args: args[i]
      });
    }

    return this.http.post<MoodleRes[]>(`${this.wwwRoot}${this.moodleAjaxUrl}?sesskey=${this.sessKey}`, body, this.httpOptions)
      .pipe(
        tap(_ => this.log(`Consuming Moodle service ${methodName}`)),
        catchError(this.handleError<MoodleRes[]>(`Moodle service ${methodName}`, errorRes))
      );
  }

  private log(message: string) {}

  /**
   * Handle Http operation that failed.
   * Let the app continue.
   * @param operation - name of the operation that failed
   * @param result - optional value to return as the observable result
   */
  private handleError<T>(operation = 'operation', result?: T) {
    return (error: any): Observable<T> => {

      // TODO: send the error to remote logging infrastructure
      this.errorReporterService.relayError(error);

      // TODO: better job of transforming error for user consumption
      this.log(`${operation} failed: ${error.message}`);

      // Let the app keep running by returning an empty result.
      return of(result as T);
    };
  }

  public extractData(response: any) : any {
    if (!response.length) {
      // Single response with error arrived.
      let singleMoodleRes: MoodleRes = response;
      if (singleMoodleRes.error) {
        this.errorReporterService.relayError(singleMoodleRes);
        return null;
      }

      return singleMoodleRes.data;
    }

    let multiMoodleRes: MoodleRes[] = response;

    if (multiMoodleRes[0].error) {
      this.errorReporterService.relayError(multiMoodleRes[0]);
      return null;
    }

    return multiMoodleRes[0].data;
  }
}
