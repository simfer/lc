import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { ConfirmregistrationComponent } from './components/confirmregistration/confirmregistration.component';
import { SubscribeComponent } from './components/subscribe/subscribe.component';
import { PaypalComponent } from './components/paypal/paypal.component';
import { PaymentComponent} from './components/payment/payment.component';
import {SubscriptionpaymentComponent} from "./components/subscriptionpayment/subscriptionpayment.component";
import { AuthGuard } from './guards/auth.guard';
import {SubscribeGuard} from "./guards/subscribe.guard";

//  { path: '', component: HomeComponent,canActivate: [AuthGuard] },

const routes: Routes = [
  { path: '', component: HomeComponent, canActivate: [AuthGuard] },
  { path: 'home', component: HomeComponent, canActivate: [AuthGuard] },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent},
  { path: 'confirmregistration', component: ConfirmregistrationComponent, canActivate: [AuthGuard] },
  { path: 'subscribe', component: SubscribeComponent, canActivate: [AuthGuard, SubscribeGuard] },
  { path: 'payment', component: PaymentComponent, canActivate: [AuthGuard] },
  { path: 'subscriptionpayment', component: SubscriptionpaymentComponent, canActivate: [AuthGuard] },
  { path: 'paypal', component: PaypalComponent, canActivate: [AuthGuard] },

  // otherwise go to home
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {

}
