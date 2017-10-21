#!/usr/bin/env bash
ng build --prod
rm -rf ~/Sites/lovechallenge/
cp -r lovechallenge ~/Sites/
cd ~/Sites/lovechallenge/
sed -i '.original' 's/href="\/"/href="\/lovechallenge\/"/g' index.html
