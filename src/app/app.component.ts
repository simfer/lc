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
  currentUser = null;
  username = '';

  constructor(
    //private route: ActivatedRoute,
    private router: Router,
    private localStorageService: LocalStorageService
  ) {
    this.subscription = localStorageService.loginAnnounced$.subscribe(
      currentUser => {
        this.currentUser = currentUser;
        this.username = currentUser['username'];
      });

    //this.subscription = localStorageService.logoutAnnounced$.subscribe(
    //  empty => {
    //    this.currentUser = null;
    //  });

    //when the app refresh or initialized
    //this.currentUser = JSON.parse(localStorage.getItem('currentUser'));

  }

  logout(): void {
    localStorage.removeItem('currentUser');
    this.currentUser = null;

    this.router.navigate(['/login']);
  }

}

