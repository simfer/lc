import { Injectable } from '@angular/core';
import { Subject }    from 'rxjs/Subject';

@Injectable()
export class LocalStorageService {

  private loginAnnouncedSource = new Subject<string>();
  private logoutAnnoucedSource = new Subject<string>();

  loginAnnounced$ = this.loginAnnouncedSource.asObservable();
  logoutAnnounced$ = this.logoutAnnoucedSource.asObservable();

  constructor() { }

  announceLogin(username: string) {
    this.loginAnnouncedSource.next(username);
  }

  announceLogout() {
    this.logoutAnnoucedSource.next(null);
  }
}
