import {Component} from '@angular/core';
import {Router} from '@angular/router';
import { LocalStorageService } from './services/local-storage.service';
import { Subscription }   from 'rxjs/Subscription';


@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class AppComponent {
  subscription: Subscription;
  currentCustomer = null;
  username = '';

  constructor(
    //private route: ActivatedRoute,
    private router: Router,
    private localStorageService: LocalStorageService
  ) {
    this.subscription = localStorageService.loginAnnounced$.subscribe(
      currentCustomer => {
        this.currentCustomer = currentCustomer;
        this.username = currentCustomer['username'];
      });

    //this.subscription = localStorageService.logoutAnnounced$.subscribe(
    //  empty => {
    //    this.currentCustomer = null;
    //  });

    //when the app refresh or initialized
    //this.currentCustomer = JSON.parse(localStorage.getItem('currentCustomer'));

  }

  logout(): void {
    localStorage.removeItem('currentCustomer');
    this.currentCustomer = null;

    this.router.navigate(['/login']);
  }

}

