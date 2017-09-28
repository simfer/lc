import { Component, OnInit } from '@angular/core';
import { Router } from "@angular/router";
import { Region} from '../../interfaces/region';
import { RegionService } from '../../services/region.service';
import { Product } from "../../interfaces/product";
import { Color } from "../../interfaces/color";
import { ProductService } from "../../services/product.service";
import { ColorService } from "../../services/color.service";

@Component({
  selector: 'app-order',
  templateUrl: './order.component.html',
  styleUrls: ['../../app.component.css']
})
export class OrderComponent implements OnInit {
  regions: Region[];
  products: Product[];
  colors: Color[];

  selectedProduct = '';
  selectedColor = '';
  selectedRegions = '';

  constructor(
    private router: Router,
    private regionService: RegionService,
    private productService: ProductService,
    private colorService: ColorService
  ) { }

  ngOnInit() {
    this.getRegions();
    this.getProducts();
    this.getColors();
  }

  getProducts() {
    this.productService.getProducts()
      .then(products => {
        this.products = products;
      });
  }
  getColors() {
    this.colorService.getColors()
      .then(colors => {
        this.colors = colors;
      });
  }
  getRegions() {
    this.regionService.getRegions()
      .then(regions => {
        this.regions = regions;
      });
  }

  orderNow() {
    let regions = [];

    for (var i=0;i<this.selectedRegions.length;i++) {
      regions.push({region: this.regions[this.selectedRegions[i]].idregion,regionDescription:this.regions[this.selectedRegions[i]].description});
    }

    let order = {
      product: this.products[this.selectedProduct].idproduct,
      productDescription: this.products[this.selectedProduct].description,
      color: this.colors[this.selectedColor].idcolor,
      colorDescription: this.colors[this.selectedColor].description,
      totalAmount: this.products[this.selectedProduct].price,
      quantity: this.products[this.selectedProduct].quantity,
      regions: regions
    };

    console.log(order);

    localStorage.setItem('currentOrder', JSON.stringify(order));
    this.router.navigate(['/ordersummary']);

  }
}
