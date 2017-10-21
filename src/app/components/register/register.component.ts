import { Component, OnInit, HostListener } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { RegistrationService } from '../../services/registration.service';
import { AlertService } from '../../services/alert.service';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { Observable } from 'rxjs/Observable';
import { DatePipe } from '@angular/common';
import { CustomerService } from "../../services/customer.service";
import { CitiesService} from "../../services/cities.service";
import { Customer } from "../../interfaces/customer";
import { City} from "../../interfaces/city";
import { Localstorage } from "../../interfaces/localstorage";
import 'rxjs/add/operator/startWith';
import 'rxjs/add/operator/map';

const EMAIL_REGEX = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

@Component({
  selector: 'app-register',
  templateUrl: './register.component.html',
  styleUrls: ['../../app.component.css']
})
export class RegisterComponent implements OnInit {
  public myForm: FormGroup;
  startDate = new Date(1990, 0, 1);
  pobControl: FormControl = new FormControl('SARONNO (VA)');
  dobControl: FormControl = new FormControl(this.startDate);

  cities:City[];

  filteredCities: Observable<City[]>;

  loading = false;
  notMatchingPasswords = false;
  existingNickname = false;

  constructor(private route: ActivatedRoute,
              private registrationService: RegistrationService,
              private customerService: CustomerService,
              private citiesService: CitiesService,
              private alertService: AlertService,
              private router: Router,
              private datePipe: DatePipe) {
  }

  // if the nickname is greater or equal than 8 characters we need to check if it's been already taken
  @HostListener('window:input', ['$event'])
  onInput(e) {
    if (e.target.name === 'nickname') {
      if (e.target.value.length > 7) this.checkExistingNickname(e.target.value);
      else this.existingNickname = false;
    }
  }

  // check if the input nickname has been already taken
  checkExistingNickname(v) {
    this.customerService.checkExistingUsername(v).then(response => {
      if (response['found'] === true) this.existingNickname = true;
      else this.existingNickname = false;
    });
  }

  // form construction
  ngOnInit() {
    this.getCities();
    this.myForm = new FormGroup({
      username: new FormControl('albertino', [<any>Validators.required, <any>Validators.minLength(8)]),
      password: new FormControl('1234', [<any>Validators.required]),
      ctrlPassword: new FormControl('1234', [<any>Validators.required]),
      firstname: new FormControl('Alberto'),
      lastname: new FormControl('Detto'),
      gender: new FormControl('M'),
      dateofbirth: new FormControl(''),
      placeofbirth: new FormControl(''),
      email: new FormControl('careter33@gustr.com', [Validators.required, Validators.pattern(EMAIL_REGEX)]),
      mobile: new FormControl('12345678')
    });

    this.filteredCities = this.pobControl.valueChanges
      .startWith(null)
      .map(val => val ? this.filter(val) : this.cities);

  }

  getCities() {
    this.citiesService.getCities()
      .then(cities => {
        this.cities = cities;
      });
  }

  filter(val: string): City[] {
    return this.cities.filter(city =>
      city.city.toLowerCase().indexOf(val.toLowerCase()) === 0);
  }

  goToLogin() {
    console.log('go to login');
    this.router.navigate(['/login']);
  }

  register(v) {
    let isValid = false;
    let customer = <Customer>{};

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
      customer.placeofbirth =v.placeofbirth;
      customer.email = v.email;
      customer.mobile = v.mobile;

      this.registrationService.register(customer)
        .subscribe(
          data => {
            console.log(data);
            let ls = <Localstorage>{};
            ls.username = customer.username;
            ls.registered = '0';
            ls.subscribed = '0';
            localStorage.setItem('currentCustomer',JSON.stringify(ls));
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
}
