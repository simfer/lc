import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import {MdSnackBar} from "@angular/material";
import {SendmailService} from "../../services/sendmail.service";
import {AlertService} from "../../services/alert.service";

@Component({
  selector: 'app-confirmregistration',
  templateUrl: './confirmregistration.component.html',
  styleUrls: ['../../app.component.css']
})
export class ConfirmregistrationComponent implements OnInit {

  constructor(
    private router: Router,
    private alertService: AlertService,
    private snackBar: MdSnackBar,
    private sendMailService: SendmailService
  ) { }

  ngOnInit() {
  }

  logout() {
    this.router.navigate(['/login']);
  }

  resendConfirmationEmail() {
    const customer = JSON.parse(localStorage.getItem("currentCustomer"));

    this.sendMailService.sendMail(customer.idcustomer)
      .subscribe(
        data => {
          let snackBarRef = this.snackBar.open('Email inviata con successo!',null,{
            extraClasses: ['snackbar-class'],
            duration: 5000
          });
        },
        error => {
          console.log(error);
          this.alertService.error(error);
        });
  }
}
