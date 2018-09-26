#!/bin/bash

java -jar ../../tools/yuicompressor-2.4.7.jar js/application.js -o js/application.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/padcontrol.js -o js/padcontrol.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/ajax/print.success.js -o js/ajax/print.success.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/ajax/home.success.js -o js/ajax/home.success.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/ajax/fave.success.js -o js/ajax/fave.success.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/ajax/search.success.js -o js/ajax/search.success.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/ajax/success.js -o js/ajax/success.min.js

java -jar ../../tools/yuicompressor-2.4.7.jar js/hustler/ajax/ajax.js -o js/hustler/ajax/ajax.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/hustler/ajax/content.js -o js/hustler/ajax/content.min.js
java -jar ../../tools/yuicompressor-2.4.7.jar js/hustler/ajax/favorites.js -o js/hustler/ajax/favorites.min.js


java -jar ../../tools/yuicompressor-2.4.7.jar css/custom.css -o css/custom.min.css
java -jar ../../tools/yuicompressor-2.4.7.jar css/mediaqueries.css -o css/mediaqueries.min.css
java -jar ../../tools/yuicompressor-2.4.7.jar css/touchcarousel.css -o css/touchcarousel.min.css
java -jar ../../tools/yuicompressor-2.4.7.jar css/touchcarousel-skin.css -o css/touchcarousel-skin.min.css
java -jar ../../tools/yuicompressor-2.4.7.jar css/photoswipe.css -o css/photoswipe.min.css


