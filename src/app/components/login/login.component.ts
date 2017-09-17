import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {AuthenticationService} from '../../services/authentication.service';
import {AlertService} from '../../services/alert.service';
import {LocalStorageService} from '../../services/local-storage.service';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})

export class LoginComponent implements OnInit {
  model: any = {username: '', password: ''};
  loading = false;
  returnUrl: string;

  constructor(private route: ActivatedRoute,
              private authenticationService: AuthenticationService,
              private alertService: AlertService,
              private router: Router,
              private localStorageService: LocalStorageService) {
  }

  ngOnInit() {
    // reset login status
    this.authenticationService.logout();

    // get return URL from route
    this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
  }


  goToRegister() {
    this.router.navigate(['/register']);
  }

  login() {
    this.loading = true;
    this.authenticationService.login(this.model.username, this.model.password)
      .subscribe(
      data => {
        this.announce();
        this.router.navigate(['/subscribe']);
      },
      error => {
        console.log(error);
        this.alertService.error(error);
        this.loading = false;
      });
  }

  announce() {
    let currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (currentUser && currentUser.token) {
      return this.localStorageService.announceLogin(currentUser);
    }
  }
}
