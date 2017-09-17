import {Injectable} from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class AuthenticationService {
  private headers = new Headers({'Content-Type': 'application/json'});

  constructor(private http: Http) {}
  login(username: string, password: string) {
    return this.http.post('/api/v1/customerlogin', JSON.stringify({username: username, password: password}), {headers: this.headers})
      .map((response: Response) => {
        const res = response.json();
        if (res.jwt) {
          //const user = {idcustomer:res.idcustomer, username: username, token: res.jwt};

          localStorage.setItem('currentUser', JSON.stringify(res));
        }
      });
  }

  logout() {
    localStorage.removeItem('currentUser');
  }
}
