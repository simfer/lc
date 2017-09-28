import { Component, OnInit } from '@angular/core';
import {BrowserModule} from '@angular/platform-browser';

import {AgmCoreModule} from '@agm/core';

@Component({
  selector: 'app-map',
  templateUrl: './map.component.html',
  styleUrls: ['./map.component.css']
})
export class MapComponent implements OnInit {

  title: string = 'My first AGM project';
  lat: number = 41.6550632;
  lng: number = 12.3654707;
  zoom: number = 5;
  mapTypeId: string = 'terrain';
  constructor() { }

  ngOnInit() {
  }

}
