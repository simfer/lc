import {Injectable} from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class RegistrationService {
  // token = 'Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzUxMiJ9.eyJpYXQiOjE1MDQxOTE0ODUsImp0aSI6IlhxbmxPNW5yd2NLempDK3YzSW1CR2FuK0xDY3kzbzdaNmw1eTFNcTNcL28wPSIsImlzcyI6Imh0dHA6XC9cL2xvY2FsaG9zdDo4MDgwXC9sb3ZlY2hhbGxlbmdlXC9zZXJ2ZXIiLCJuYmYiOjE1MDQxOTE0ODUsImV4cCI6MTUwNDc5NjI4NSwiZGF0YSI6eyJ1c2VySWQiOiIxIiwidXNlck5hbWUiOiJhZG1pbiJ9fQ.2UBtH7LgRHnCagmshMwajLoubnjaGHSS7QsZEPjMHmgIFhoNLktl7eJn2_pEE2WVNdDt4pHniXqvLPXp-wO9LA';
  // private headers = new Headers({'Content-Type': 'application/json', 'Authorization': this.token});
  private headers = new Headers({'Content-Type': 'application/json'});

  constructor(private http: Http) {}

  register(customer: any) {
     return this.http.post('/api/v1/customerregister', customer, {headers: this.headers})
      .map((response: Response) => {
        const res = response.json();
        console.log('send link to user');
        console.log(res);
     });
  }

}
