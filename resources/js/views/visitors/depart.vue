<template>
  <div class="auth-container" :style="{ 'background-image': 'url(' + backgroundUrl + ')' }">
    <div class="auth-inner auth-inner-full">              
        <div class="auth-logo text-center">
            <Logo />
        </div>

        <div class="notification-error" v-if="error">
          {{ errorMessage }}
        </div>

        <form class="login-form" @submit.prevent="handleSubmit">
          <div class="form-group-wrapper">
            <div class="form-group half">
                <label for="name" v-if="lang=='nl'">Naam</label>
                <label for="name" v-if="lang=='fr'">Nom</label>
                <label for="name" v-if="lang=='en'">Name</label>

                <input type="text" id="name" v-model="newDepart.name"/>
            </div>
            <div class="form-group half">
                <label for="firstname" v-if="lang=='nl'">Voornaam</label>
                <label for="firstname" v-if="lang=='fr'">Prénom</label>
                <label for="firstname" v-if="lang=='en'">Firstname</label>

                <input type="text" id="firstname" v-model="newDepart.firstname"/>
            </div>
          </div>

          <div class="form-group text-center">
            <button :disabled="disable" type="submit" class="btn-blue" v-if="lang=='nl'">Registreer vertrek</button>
            <button :disabled="disable" type="submit" class="btn-blue" v-if="lang=='fr'">Enregistrer votre départ</button>
            <button :disabled="disable" type="submit" class="btn-blue" v-if="lang=='en'">Register departure</button>        
          </div>

        </form>

        <div class="restart-container">
            <a href="/#/visitors/start" class="btn-restart">
              Restart
            </a>
          </div>
      </div>
    </div>
</template>
  
  <script>
  import { SET_PAGE_TITLE, SET_PAGE_CTA, IS_LOADING } from '../../constants';
  import axios from 'axios';
  const api_url = process.env.MIX_API_URL;
  
  export default {
    name: 'VisitorsDepart',
    mounted() {
      this.lang = this.$route.query.lang;
    },
    data() {
      return {
        newDepart: {
            name: "",
            firstname: "",
        },
        errorMessage: null,
        error: false,
        backgroundUrl: './images/background.jpeg',
        lang: 'nl',
        disable: false,
      }
    },
    methods: {
      handleSubmit() {
        let formData = new FormData();
        formData.append('name', this.newDepart.name);
        formData.append('firstname', this.newDepart.firstname);

        this.error = null;
        this.disable = true;

        axios.post(`${api_url}/visitors/depart`, formData)
          .then(res => {
            this.$router.push('start');    
          })
          .catch(err => {
            this.error = true;
            this.disable = false;

            if(this.lang == 'nl'){this.errorMessage = "Uw gegevens werden niet gevonden."}
            if(this.lang == 'fr'){this.errorMessage = "Vos coordonnées n'ont pas été trouvées."}
            if(this.lang == 'en'){this.errorMessage = "Your details were not found."}          
          });
      },
    },
  }
  </script>
  