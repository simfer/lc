import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";

@Component({
  selector: 'app-confirmregistration',
  templateUrl: './confirmregistration.component.html',
  styleUrls: ['../../app.component.css']
})
export class ConfirmregistrationComponent implements OnInit {

  constructor(private router: Router) { }

  ngOnInit() {
  }
  logout() {
    this.router.navigate(['/login']);
  }

  resendConfirmationEmail() {
    console.log("resend");
  }
}
