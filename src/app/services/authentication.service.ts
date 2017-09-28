import {Injectable} from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';
import { Localstorage } from "../interfaces/localstorage";

@Injectable()
export class AuthenticationService {
  private headers = new Headers({'Content-Type': 'application/json'});

  constructor(private http: Http) {}
  login(username: string, password: string) {
    return this.http.post('/api/v1/customerlogin', JSON.stringify({username: username, password: password}), {headers: this.headers})
      .map((response: Response) => {
        const ls: Localstorage = response.json();
        if (ls.jwt) {
          localStorage.setItem('currentCustomer', JSON.stringify(ls));
        }
      });
  }

  logout() {
    localStorage.removeItem('currentCustomer');
  }
}
