import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {RegistrationService} from '../../services/registration.service';
import {AlertService} from '../../services/alert.service';
import {FormControl} from '@angular/forms';
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {

  myControl: FormControl = new FormControl();

  cities = [
    'Pomezia (RM)',
    'Moncalieri (TO)',
    'Angri (SA)',
    'Lagnasco (CN)',
    'Fondi (LT)',
    'Fiumicino (RM)',
    'Portovenere (SP)',
    'Volla (NA)',
    'Cardito (NA)'
  ];

filteredCities: Observable<string[]>;

  startDate = new Date(1990, 0, 1);
  loading = false;
  returnUrl: string;
  model: any = {
    nickname: 'martello44',
    password: '',
    password2: '',
    salt: '',
    lastname: 'Vinci',
    firstname: 'Stefano',
    gender: 'M',
    dateofbirth: null,
    placeofbirth: 'Volla (NA)',
    email: ''
    };
  constructor(private route: ActivatedRoute, private registrationService: RegistrationService,
              private alertService: AlertService, private router: Router) {
  }

  ngOnInit() {
    this.filteredCities = this.myControl.valueChanges
         .startWith(null)
         .map(val => val ? this.filter(val) : this.cities.slice());
  }

  filter(val: string): string[] {
      return this.cities.filter(option =>
        option.toLowerCase().indexOf(val.toLowerCase()) === 0);
   }

  goToLogin() {
    console.log('go to login');
    this.router.navigate(['/login']);
  }

  register() {
    this.model.dateofbirth = this.startDate.toLocaleDateString('it-IT');
    console.log(this.model);
    this.loading = true;

    this.registrationService.register(this.model)
      .subscribe(
      data => {
        console.log('navigate');
        console.log(this.returnUrl);
        this.router.navigate([this.returnUrl]);
      },
      error => {
        console.log(error);
        this.alertService.error(error);
        this.loading = false;
      });
  }
}
