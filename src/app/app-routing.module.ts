import { NgModule } from '@angular/core';
import { Routes, RouterModule } from '@angular/router';

import { HomeComponent } from './components/home/home.component';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { ConfirmregistrationComponent } from './components/confirmregistration/confirmregistration.component';
import { SubscribeComponent } from './components/subscribe/subscribe.component';
import { PaypalComponent } from './components/paypal/paypal.component';

import { AuthGuard } from './guards/auth.guard';

//  { path: '', component: HomeComponent,canActivate: [AuthGuard] },

const routes: Routes = [
  { path: '', component: HomeComponent, canActivate: [AuthGuard] },
  { path: 'home', component: HomeComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent},
  { path: 'confirmregistration', component: ConfirmregistrationComponent},
  { path: 'subscribe', component: SubscribeComponent},
  { path: 'paypal', component: PaypalComponent},

  // otherwise go to home
  { path: '**', redirectTo: '' }
];

@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule {

}
