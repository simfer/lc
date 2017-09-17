import {BrowserModule,DomSanitizer} from '@angular/platform-browser';
import {NgModule, LOCALE_ID} from '@angular/core';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';
import {AppRoutingModule} from './app-routing.module';
import {HttpModule} from '@angular/http';
import {BrowserAnimationsModule} from '@angular/platform-browser/animations';
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
  MdSnackBarModule
} from '@angular/material';

import {AppComponent} from './app.component';
import {LoginComponent} from './components/login/login.component';
import {RegisterComponent} from './components/register/register.component';
import {HomeComponent} from './components/home/home.component';
import { AlertComponent } from './components/alert/alert.component';

import {AuthenticationService} from './services/authentication.service';
import {RegistrationService} from './services/registration.service';
import { AlertService } from './services/alert.service';
import { AuthGuard } from './guards/auth.guard';
import {SubscribeGuard} from "./guards/subscribe.guard";
import {LocalStorageService} from './services/local-storage.service'

import { DatePipe } from '@angular/common';
import { ConfirmregistrationComponent } from './components/confirmregistration/confirmregistration.component';
import { SubscribeComponent } from './components/subscribe/subscribe.component';
import { PaypalComponent } from './components/paypal/paypal.component';
import { PaymentComponent } from './components/payment/payment.component';
import { SubscriptionpaymentComponent } from './components/subscriptionpayment/subscriptionpayment.component';


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
    SubscriptionpaymentComponent
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
    MdSnackBarModule
  ],
  providers: [
    {provide: LOCALE_ID, useValue: 'it-IT'},
    AuthenticationService,
    RegistrationService,
    AuthGuard,
    SubscribeGuard,
    AlertService,
    DatePipe,
    LocalStorageService
  ],
  bootstrap: [AppComponent]
})
export class AppModule {
  constructor(mdIconRegistry: MdIconRegistry, domSanitizer: DomSanitizer) {
    mdIconRegistry.addSvgIconSet(domSanitizer.bypassSecurityTrustResourceUrl('./assets/mdi.svg')); // Or whatever path you placed mdi.svg at
  }
}
