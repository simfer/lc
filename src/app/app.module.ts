import {BrowserModule,DomSanitizer} from '@angular/platform-browser';
import {NgModule, LOCALE_ID} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {AppRoutingModule} from './app-routing.module';
import {HttpModule} from '@angular/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
import {AgmCoreModule} from '@agm/core';

import {
  MdTabsModule,
  MdToolbarModule,
  MdProgressSpinnerModule,
  MdInputModule,
  MdDatepickerModule,
  MdNativeDateModule,
  MdButtonModule,
  MdAutocompleteModule,
  MdRadioModule,
  MdIconModule,
  MdIconRegistry,
  MdSnackBarModule,
  MdDialogModule,
  MdGridListModule,
  MdSelectModule,
  MdListModule,
  MdTableModule
} from '@angular/material';

// COMPONENTS
import { AppComponent } from './app.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { HomeComponent } from './components/home/home.component';
import { AlertComponent } from './components/alert/alert.component';
import { ConfirmregistrationComponent } from './components/confirmregistration/confirmregistration.component';
import { SubscribeComponent } from './components/subscribe/subscribe.component';
import { PaypalComponent } from './components/paypal/paypal.component';
import { PaymentComponent } from './components/payment/payment.component';
import { SubscriptionpaymentComponent } from './components/subscriptionpayment/subscriptionpayment.component';
import { MapComponent } from './components/map/map.component';
import { AlertdialogComponent } from './components/alertdialog/alertdialog.component';
import { OrderComponent } from './components/order/order.component';
import { OrdersummaryComponent } from './components/ordersummary/ordersummary.component';
import { OrdersListComponent } from './components/orders-list/orders-list.component';
import { RedeemcodeComponent } from './components/redeemcode/redeemcode.component';

// SERVICES
import { Location } from "@angular/common";
import { AuthenticationService } from './services/authentication.service';
import { RegistrationService } from './services/registration.service';
import { AlertService } from './services/alert.service';
import { AuthGuard } from './guards/auth.guard';
import { SubscribeGuard } from './guards/subscribe.guard';
import { RegisterGuard} from './guards/register.guard';
import { LocalStorageService } from './services/local-storage.service'
import { DialogsService } from "./services/dialogs.service";
import { RegionService } from "./services/region.service";
import { ProductService} from "./services/product.service";
import { CustomerService} from "./services/customer.service";
import { ColorService} from "./services/color.service";
import { OrderService} from "./services/order.service";
import { SendmailService} from "./services/sendmail.service";
import { RedeemcodeService} from "./services/redeemcode.service";

// PIPES
import { DatePipe } from '@angular/common';


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    HomeComponent,
    AlertComponent,
    ConfirmregistrationComponent,
    SubscribeComponent,
    PaypalComponent,
    PaymentComponent,
    SubscriptionpaymentComponent,
    MapComponent,
    AlertdialogComponent,
    OrderComponent,
    OrdersummaryComponent,
    OrdersListComponent,
    RedeemcodeComponent
  ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    FormsModule,
    ReactiveFormsModule,
    HttpModule,
    AppRoutingModule,
    MdTabsModule,
    MdToolbarModule,
    MdProgressSpinnerModule,
    MdInputModule,
    MdDatepickerModule,
    MdNativeDateModule,
    MdButtonModule,
    MdAutocompleteModule,
    MdRadioModule,
    MdIconModule,
    MdSnackBarModule,
    MdDialogModule,
    MdGridListModule,
    MdSelectModule,
    MdListModule,
    MdTableModule,
    AgmCoreModule.forRoot({apiKey:'xxxxxxxx'})
  ],
  providers: [
    {provide: LOCALE_ID, useValue: 'it-IT'},
    Location,
    AuthenticationService,
    RegistrationService,
    AuthGuard,
    SubscribeGuard,
    RegisterGuard,
    AlertService,
    DatePipe,
    LocalStorageService,
    DialogsService,
    RegionService,
    ProductService,
    CustomerService,
    ColorService,
    OrderService,
    SendmailService,
    RedeemcodeService
  ],
  entryComponents: [ AlertdialogComponent ],
  bootstrap: [AppComponent]
})
export class AppModule {
  constructor(mdIconRegistry: MdIconRegistry, domSanitizer: DomSanitizer) {
    mdIconRegistry.addSvgIconSet(domSanitizer.bypassSecurityTrustResourceUrl('./assets/mdi.svg')); // Or whatever path you placed mdi.svg at
  }
}
