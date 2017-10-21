import {Injectable} from '@angular/core';
import {Http, Headers, Response} from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class SendmailService {

  private headers = new Headers({'Content-Type': 'application/json'});

  constructor(private http: Http) {}

  sendMail(idcustomer: string) {
    return this.http.get('server/api/v1/sendmail/' + idcustomer, {headers: this.headers})
      .map((response: Response) => {
        const res = response.json();
        console.log('send link to user');
        console.log(res);
      });
  }

}
