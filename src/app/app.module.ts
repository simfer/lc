import {BrowserModule} from '@angular/platform-browser';
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
  MdAutocompleteModule
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

import { DatePipe } from '@angular/common';
import { ConfirmregistrationComponent } from './components/confirmregistration/confirmregistration.component';


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    HomeComponent,
    AlertComponent,
    ConfirmregistrationComponent
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
    MdAutocompleteModule
  ],
  providers: [
    {provide: LOCALE_ID, useValue: 'it-IT'},
    AuthenticationService,
    RegistrationService,
    AuthGuard,
    AlertService,
    DatePipe],
  bootstrap: [AppComponent]
})
export class AppModule {}
