import {Injectable} from '@angular/core';
import {Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot} from '@angular/router';

@Injectable()
export class SubscribeGuard implements CanActivate {

  constructor(private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    if (localStorage.getItem('currentUser')) {
      let k = JSON.parse(localStorage.getItem('currentUser'));
      console.log(k["subscribed"]);
      if (k['subscribed'] === '0') {
        return true;

      }
    }
    this.router.navigate(['/home']);
    return false;
  }
}
