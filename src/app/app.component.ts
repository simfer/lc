import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { LocalStorageService } from './services/local-storage.service';
import { Subscription }   from 'rxjs/Subscription';
import { Localstorage } from "./interfaces/localstorage";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class AppComponent {
  subscription: Subscription;
  username = '';

  constructor(
    private router: Router,
    private localStorageService: LocalStorageService
  ) {
    this.subscription = localStorageService.loginAnnounced$.subscribe(
      uname => {
        this.username = uname;
      });

    //this.subscription = localStorageService.logoutAnnounced$.subscribe(
    //  empty => {
    //    this.username = null;
    //  });

    let ls: Localstorage = JSON.parse(localStorage.getItem('currentCustomer'));

    if (ls) {
      this.username = ls.username;
    }
  }

  logout(): void {
    localStorage.removeItem('currentCustomer');
    this.username = '';
    this.router.navigate(['/login']);
  }

}

