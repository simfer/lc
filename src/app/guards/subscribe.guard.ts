import {Injectable} from '@angular/core';
import {Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot} from '@angular/router';

@Injectable()
export class SubscribeGuard implements CanActivate {

  constructor(private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    if (localStorage.getItem('currentCustomer')) {
      let k = JSON.parse(localStorage.getItem('currentCustomer'));
      console.log(k["subscribed"]);
      if (k['subscribed'] === '0') {
        return true;

      }
    }
    this.router.navigate(['/home']);
    return false;
  }
}
