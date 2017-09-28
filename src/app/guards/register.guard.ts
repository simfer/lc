import {Injectable} from '@angular/core';
import {Router, CanActivate, ActivatedRouteSnapshot, RouterStateSnapshot} from '@angular/router';
import { Localstorage} from "../interfaces/localstorage";

@Injectable()
export class RegisterGuard implements CanActivate {

  constructor(private router: Router) {}

  canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot) {
    if (localStorage.getItem('currentCustomer')) {
      let ls: Localstorage = JSON.parse(localStorage.getItem('currentCustomer'));
      //let k = JSON.parse(localStorage.getItem('currentCustomer'));
      console.log('Registered = ' + ls["registered"]);
      if (ls['registered'] === '0') {
        return true;

      }
    }
    this.router.navigate(['/subscribe']);
    return false;
  }
}
