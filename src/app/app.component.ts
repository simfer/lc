import {Component} from '@angular/core';
import {Router} from '@angular/router';
import { LocalStorageService } from './services/local-storage.service';
import { Subscription }   from 'rxjs/Subscription';
import { Localstorage} from "./interfaces/localstorage";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class AppComponent {
  subscription: Subscription;
  //currentCustomer = null;
  username = '';

  constructor(
    private router: Router,
    private localStorageService: LocalStorageService
  ) {
    //this.subscription = localStorageService.loginAnnounced$.subscribe(
    //  uname => {
    //    this.username = uname;
    //  });
    let ls: Localstorage = JSON.parse(localStorage.getItem('currentCustomer'));

    if (ls) {
      this.username = ls.username;
    }

    //this.subscription = localStorageService.logoutAnnounced$.subscribe(
    //  empty => {
    //    this.currentCustomer = null;
    //  });

    //when the app refresh or initialized
    //this.currentCustomer = JSON.parse(localStorage.getItem('currentCustomer'));

  }

  logout(): void {
    localStorage.removeItem('currentCustomer');
    this.username = '';
    this.router.navigate(['/login']);
  }

}

