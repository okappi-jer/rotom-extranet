<template>
    <div class="auth-container" :style="{ 'background-image': 'url(' + backgroundUrl + ')' }">
      <div class="auth-inner auth-inner-full">              
          <div class="auth-logo text-center">
              <Logo />
          </div>
  
          <div class="language-block" v-if="!language">
            <span class="link" @click="setLanguage('nl')">Nederlands</span>
            <span class="link" @click="setLanguage('fr')">Français</span>
            <span class="link" @click="setLanguage('en')">English</span>
          </div>

          <div class="selection-block" v-if="language">
            <span class="link" href="">
                <span v-if="language=='nl'" @click="arrival('nl')">Aankomst</span>
                <span v-if="language=='en'" @click="arrival('en')">Arrival</span>
                <span v-if="language=='fr'" @click="arrival('fr')">Arrivée</span>
            </span>
            <span class="link inverse" href="">
                <span v-if="language=='nl'" @click="departure('nl')">Vertrek</span>
                <span v-if="language=='en'" @click="departure('en')">Departure</span>
                <span v-if="language=='fr'" @click="departure('fr')">Départ</span>
            </span>
          </div>
        </div>
      </div>
  </template>
    
    <script>
    import { SET_PAGE_TITLE, SET_PAGE_CTA, IS_LOADING } from '../../constants';
    import axios from 'axios';
    const api_url = process.env.MIX_API_URL;
    
    export default {
      name: 'VisitorsStart',
      mounted() {
    
      },
      data() {
        return {
          backgroundUrl: './images/background.jpeg',
          language: false,
        }
      },
      methods: {
        setLanguage(lang){
            this.language = lang;
        },
        arrival(lang){
            this.$router.push({ path: 'arrive', query: { lang: lang } });    
        },
        departure(lang){
            this.$router.push({ path: 'departure', query: { lang: lang } });    
        }    
      },
    }
    </script>
    