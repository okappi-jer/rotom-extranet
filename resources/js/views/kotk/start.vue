<template>
    <div class="auth-container" :style="{ 'background-image': 'url(' + backgroundUrl + ')' }">
      <div class="auth-inner auth-inner-full">              
          <div class="auth-logo text-center">
              <Logo />
          </div>
  
          <div class="gpx-block">
            <p>
              Bedankt voor uw deelname. De GPX wordt normaal gezien automatisch gedownload bij het openen van de pagina. Mocht dit toch niet het geval zijn, je kan via onderstaande knoppen alsnog de file downloaden.
            </p>
            <a class="big-button" id="w1" href="./gpx/w_4k.gpx" download>Wandeling - 5km</a>
            <a class="big-button" id="w2" href="./gpx/w_9k.gpx" download>Wandeling - 9km</a>
            <a class="big-button" id="w3" href="./gpx/w_11k.gpx" download>Wandeling - 11km</a>
            <a class="big-button" id="w4" href="./gpx/w_14k.gpx" download>Wandeling - 14km</a>
            <a class="big-button" id="w5" href="./gpx/w_19k.gpx" download>Wandeling - 18km</a>
            <a class="big-button" id="w6" href="./gpx/w_22k.gpx" download>Wandeling - 22km</a>

            <a class="big-button" id="f0" href="./gpx/f_34k.gpx" download>Fiets - 35km</a>
            <a class="big-button" id="f1" href="./gpx/f_71k.gpx" download>Fiets - 70km</a>
            <a class="big-button" id="f2" href="./gpx/f_115k.gpx" download>Fiets - 115km</a>
          </div>
        </div>
      </div>
  </template>
    
    <script>
    import { SET_PAGE_TITLE, SET_PAGE_CTA, IS_LOADING } from '../../constants';
    import axios from 'axios';
    const api_url = process.env.MIX_API_URL;
    
    export default {
      name: 'KotkStart',
      mounted() {
        this.afstand = this.$route.query.afstand;

        if(this.afstand){
          if(this.afstand == 'w1'){
            document.getElementById('w1').click();
          }
          if(this.afstand == 'w2'){
            document.getElementById('w2').click();
          }
          if(this.afstand == 'w3'){
            document.getElementById('w3').click();
          }
          if(this.afstand == 'w4'){
            document.getElementById('w4').click();
          }
          if(this.afstand == 'w5'){
            document.getElementById('w5').click();
          }
          if(this.afstand == 'w6'){
            document.getElementById('w6').click();
          }
          if(this.afstand == 'f1'){
            document.getElementById('f1').click();
          }
          if(this.afstand == 'f2'){
            document.getElementById('f2').click();
          }
          if(this.afstand == 'f0'){
            document.getElementById('f0').click();
          }
        }


    
      },
      data() {
        return {
          backgroundUrl: './images/background.jpeg',
          language: false,
          afstand: null,
          download: false,
        }
      },
      methods: {
        
      },
    }
    </script>
    