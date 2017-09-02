import {BrowserModule} from '@angular/platform-browser';
import {NgModule} from '@angular/core';
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


@NgModule({
  declarations: [
    AppComponent,
    LoginComponent,
    RegisterComponent,
    HomeComponent,
    AlertComponent
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
  providers: [AuthenticationService, RegistrationService, AuthGuard, AlertService],
  bootstrap: [AppComponent]
})
export class AppModule {}
