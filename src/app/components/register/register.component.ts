import {Component, OnInit} from '@angular/core';
import {Router, ActivatedRoute} from '@angular/router';
import {RegistrationService} from '../../services/registration.service';
import {AlertService} from '../../services/alert.service';
import { FormGroup, FormControl, FormBuilder, Validators } from '@angular/forms';
import {Observable} from 'rxjs/Observable';
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';
import { DatePipe } from '@angular/common';

const EMAIL_REGEX = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
})
export class RegisterComponent implements OnInit {
  public myForm: FormGroup;
  pobControl: FormControl = new FormControl();
  dobControl: FormControl = new FormControl();
  startDate = new Date(1990, 0, 1);

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

  loading = false;
  notMatchingPasswords = false;
  returnUrl: string;

  constructor(private route: ActivatedRoute, private registrationService: RegistrationService,
              private alertService: AlertService, private router: Router, private datePipe: DatePipe) {
  }

  ngOnInit() {
    this.myForm = new FormGroup({
      username: new FormControl('', [<any>Validators.required, <any>Validators.minLength(8)]),
      password: new FormControl('', [<any>Validators.required]),
      ctrlPassword: new FormControl('', [<any>Validators.required]),
      firstname: new FormControl(''),
      lastname: new FormControl(''),
      gender: new FormControl(''),
      dateofbirth: new FormControl(''),
      placeofbirth: new FormControl(''),
      email: new FormControl('', [Validators.required, Validators.pattern(EMAIL_REGEX)])
    });

    this.filteredCities = this.pobControl.valueChanges
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

register2(v) {
  let isValid = false;
  const customer = {username: '', password: '', lastname: '', firstname: '', gender: '',
    dateofbirth: '', placeofbirth: '', email: ''};

  this.notMatchingPasswords = true;
  v.placeofbirth = this.pobControl.value;
  v.dateofbirth = this.datePipe.transform(this.startDate, 'yyyy-MM-dd');
  if ((v.password) && (v.password === v.ctrlPassword)) {
    this.notMatchingPasswords = false;
    isValid = true;
    console.log(v);
    console.log('register');

    customer.username = v.username;
    customer.password = v.password;
    customer.lastname = v.lastname;
    customer.firstname = v.firstname;
    customer.gender = v.gender;
    customer.dateofbirth = v.dateofbirth;
    customer.placeofbirth = v.placeofbirth;
    customer.email = v.email;

    this.registrationService.register(customer)
      .subscribe(
      data => {
        this.router.navigate(['confirmregistration']);
      },
      error => {
        console.log(error);
        this.alertService.error(error);
        this.loading = false;
      });
  }
  return isValid;
}

  /* register() {
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
   */
}
