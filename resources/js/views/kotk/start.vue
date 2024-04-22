<template>
    <div class="auth-container" :style="{ 'background-image': 'url(' + backgroundUrl + ')' }">
      <div class="auth-inner auth-inner-full">              
          <div class="auth-logo text-center">
              <Logo />
          </div>
  
          <div class="gpx-block">
            <p>
              Mijn plan is: uitleg met enkele regels, privédomeinen, school, natuurgebied. Geen afval, honden aan de leiband. Veel plezier op de wandeling.
            </p>
            <a class="big-button" href="./gpx/4-5km.gpx" download>Wandeling - 4,5km</a>
            <a class="big-button" href="./gpx/08km.gpx" download>Wandeling - 8km</a>
            <a class="big-button" href="./gpx/11km.gpx" download>Wandeling - 11km</a>
            <a class="big-button" href="./gpx/29km.gpx" download>Fiets - 29km</a>
            <a class="big-button" href="./gpx/11km.gpx" download>Fiets - 60km</a>
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
    
      },
      data() {
        return {
          backgroundUrl: './images/background.jpeg',
          language: false,
        }
      },
      methods: {
        
      },
    }
    </script>
    