import {Component, OnInit} from '@angular/core';
import { Router}  from '@angular/router';
import { Location } from "@angular/common";
import { RedeemcodeService} from "../../services/redeemcode.service";
import { AlertService} from "../../services/alert.service";
import { Category} from "../../interfaces/category";
import { CategoryService} from "../../services/category.service";
import { Redeemcode} from "../../interfaces/redeemcode";
import { DialogsService } from "../../services/dialogs.service";

@Component({
  selector: 'app-redeemcode',
  templateUrl: './redeemcode.component.html',
  styleUrls: ['../../app.component.css']
})
export class RedeemcodeComponent implements OnInit {
  currentCustomer: any;
  categories: Category[];
  codeVerified = false;
  redeemCodeSelected = false;
  selectedCategory = '';
  codeToRedeem = '15105065320913';
  loading = false;
  redeemCode: Redeemcode = {username:'',idredeemcode:0,mobile:''};

  constructor(
    private router: Router,
    private location: Location,
    private alertService: AlertService,
    private dialogService: DialogsService,
    private redeemCodeService: RedeemcodeService
  ) { }

  ngOnInit() {
    this.currentCustomer = JSON.parse(localStorage.getItem("currentCustomer"));
  }

  doRedeemCode() {
    this.dialogService.confirm('Confirm','Are you sure?').subscribe(result => {
      console.log('The dialog was closed');
      if (result) {
        this.redeemCodeService.redeemCode(this.redeemCode.idredeemcode,this.currentCustomer.idcustomer)
          .subscribe(
            data => {
              let body = data['_body'];
              let response = JSON.parse(body);
              this.categories = response;
              console.log(response);
              this.loading = false;
              this.codeVerified = false;
              this.redeemCodeSelected = false;
              this.codeToRedeem = '';
              this.redeemCode = {username:'',idredeemcode:0,mobile:''};
            },
            error => {
              console.log(error);
              this.alertService.error(error['statusText']);
              this.loading = false;
            });

      }
    });

  }

  checkRedeemCode() {
    this.loading = true;
    this.redeemCodeService.checkRedeemCode(this.codeToRedeem)
      .subscribe(
      data => {
        console.log("Code is valid!");
        this.codeVerified = true;
        this.redeemCodeService.getAvailableCategories(this.codeToRedeem)
          .subscribe(
            data => {
              let body = data['_body'];
              let response = JSON.parse(body);
              //this.redeemedCode = response;
              this.categories = response;
              console.log(response);
              this.loading = false;
            },
            error => {
              this.alertService.error(error['_body']);
              this.loading = false;
            });
      },
      error => {
        this.alertService.error(error['_body']);
        this.loading = false;
      });


  }

  categoryClick(event): void {
    this.redeemCodeSelected = true;
    console.log(event.value);
    console.log(this.codeToRedeem, this.categories[event.value].idcategory);

    this.redeemCodeService.getAvailableOrders(this.codeToRedeem,this.categories[event.value].idcategory)
      .then(response => {
        this.redeemCode = response[0];
        console.log(this.redeemCode);
      });
  }

  goBack() {
    this.location.back();
  }
}
