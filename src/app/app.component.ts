import {Component} from '@angular/core';
import {FormControl, Validators} from '@angular/forms';
import {Router, ActivatedRoute} from '@angular/router';

// import {Location} from "@angular/common";

// const EMAIL_REGEX = /^[a-zA-Z0-9.!#$%&’*+/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/;

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.css']
})

export class AppComponent {
  // genders = [
  //  {value: 'M', viewValue: 'Uomo'},
  //  {value: 'F', viewValue: 'Donna'}
  // ];
  // emailFormControl = new FormControl('', [
  //  Validators.required,
  //  Validators.pattern(EMAIL_REGEX)]);

  constructor(
    private route: ActivatedRoute,
    private router: Router
  ) {}

  goBack(): void {
    // this.location.back();
  }

}

