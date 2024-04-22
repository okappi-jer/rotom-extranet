<template>
  <div class="auth-container arrival-form-container" :style="{ 'background-image': 'url(' + backgroundUrl + ')' }">
    <div class="auth-inner auth-inner-full">              
        <div class="auth-logo text-center">
            <Logo />
        </div>

        <div class="notification-error" v-if="error">
          {{ errorMessage }}
        </div>
        <form class="login-form" @submit.prevent="handleSubmit" v-if="employees">
            <div class="form-group-wrapper">
                <div class="form-group">
                    <label for="company" v-if="lang=='nl'">Bedrijf</label>
                    <label for="company" v-if="lang=='fr'">Entreprise</label>
                    <label for="company" v-if="lang=='en'">Company</label>

                    <input type="text" id="company" v-model="newArrival.company"/>
                </div>
            </div>

            <div class="form-group-wrapper">
                <div class="form-group half">
                    <label for="name" v-if="lang=='nl'">Naam</label>
                    <label for="name" v-if="lang=='fr'">Nom</label>
                    <label for="name" v-if="lang=='en'">Name</label>

                    <input type="text" id="name" v-model="newArrival.name"/>
                </div>
                <div class="form-group half">
                    <label for="firstname" v-if="lang=='nl'">Voornaam</label>
                    <label for="firstname" v-if="lang=='fr'">Prénom</label>
                    <label for="firstname" v-if="lang=='en'">Firstname</label>

                    <input type="text" id="firstname" v-model="newArrival.firstname"/>
                </div>
            </div>

            <div class="form-group-wrapper">
                <!-- <div class="form-group half">
                    <label for="email" v-if="lang=='nl'">E-mail</label>
                    <label for="email" v-if="lang=='fr'">Email</label>
                    <label for="email" v-if="lang=='en'">Email</label>

                    <input type="text" id="email" v-model="newArrival.email"/>
                </div> -->

                <div class="form-group half">
                    <label for="mobile" v-if="lang=='nl'">GSM</label>
                    <label for="mobile" v-if="lang=='fr'">Numéro mobile</label>
                    <label for="mobile" v-if="lang=='en'">Mobile</label>

                    <input type="text" id="mobile" v-model="newArrival.mobile"/>
                </div>

                <div class="form-group half">
                    <label for="contact" v-if="lang=='nl'">Intern contact</label>
                    <label for="contact" v-if="lang=='fr'">Contact interne</label>
                    <label for="contact" v-if="lang=='en'">Internal contact</label>

                    <select name="contact" id="contact" v-model="newArrival.contact">
                      <option value="">---</option>
                      <option v-for="employee in employees" :value="employee.name">{{ employee.name }}</option>
                    </select>
                </div>  
            </div>

            <div class="form-group-wrapper">
                <input type="checkbox" id="privacy" name="privacy" value="1" v-model="newArrival.conditions"/>
                <label for="privacy" v-if="lang=='nl'">Door mijn aanwezigheid te registeren bevestig ik akkoord te gaan met <span @click="showModal=true" style="text-decoration: underline;">de privacy -en de hygiënevoorwaarden.</span></label>
                <label for="privacy" v-if="lang=='fr'">En enregistrant mon arrivée, je confirme que j'accepte <span @click="showModal=true" style="text-decoration: underline;">les conditions de confidentialité et d'hygiène.</span></label>
                <label for="privacy" v-if="lang=='en'">By registering my arrival I confirm that I agree with <span @click="showModal=true" style="text-decoration: underline;">the privacy and hygiene conditions.</span></label>
            </div>

            <div class="text-center form-group-wrapper" style="margin-top: 15px;">
              <button :disabled="disable" type="submit" class="btn-blue" v-if="lang=='nl'">Registreer aankomst</button>
              <button :disabled="disable" type="submit" class="btn-blue" v-if="lang=='fr'">Enregistrer l'arrivée</button>
              <button :disabled="disable" type="submit" class="btn-blue" v-if="lang=='en'">Register arrival</button>
            </div>
            

          </form>

          <div id="modal" v-if="showModal">
            <span class="close-btn" v-if="lang=='nl'" @click="showModal=false">Sluit voorwaarden</span>
            <span class="close-btn" v-if="lang=='en'" @click="showModal=false">Close conditions</span>
            <span class="close-btn" v-if="lang=='fr'" @click="showModal=false">Fermer conditions</span>

            <iframe title="Conditions" src="modal.pdf" frameborder="1" scrolling="auto" width="100%" height="600px" ></iframe>
          </div>

          <div class="restart-container">
            <a href="/#/visitors/start" class="btn-restart">
              Restart
            </a>
          </div>
        </div>
    </div>
</template>
  
  <script>
  import { SET_PAGE_TITLE, SET_PAGE_CTA, IS_LOADING, GET_EMPLOYEES } from '../../constants';
  import axios from 'axios';
  const api_url = process.env.MIX_API_URL;
  
  export default {
    name: 'VisitorsArrive',
    mounted() {
      this.getAllEmployees;
      this.lang = this.$route.query.lang;
    },
    data() {
      return {
        newArrival: {
            name: "",
            firstname: "",
            company: "",
            contact: "",
            email: "",
            mobile: "",
            conditions: ""
        },
        errorMessage: null,
        error: false,
        backgroundUrl: './images/background.jpeg',
        lang: 'nl',
        disable: false,
        showModal: false,
      }
    },
    methods: {
      handleSubmit() {
        let formData = new FormData();
        formData.append('company', this.newArrival.company);
        formData.append('name', this.newArrival.name);
        formData.append('firstname', this.newArrival.firstname);
        formData.append('contact', this.newArrival.contact);
        formData.append('email', this.newArrival.email);
        formData.append('mobile', this.newArrival.mobile);
        formData.append('conditions', this.newArrival.conditions);
        
        this.error = null;
        this.disable = true;

        axios.post(`${api_url}/visitors/arrive`, formData)
          .then(res => {
            this.$router.push('start');    
          })
          .catch(err => {
            this.error = true;
            this.disable = false;

            if(this.lang == 'nl'){this.errorMessage = "Gelieve alle velden correct in te vullen."}
            if(this.lang == 'fr'){this.errorMessage = "Veuillez remplir tous les champs correctement."}
            if(this.lang == 'en'){this.errorMessage = "Please fill in all fields correctly."}
          });
      }
    },
    computed: {
      getAllEmployees() {
        if(!this.$store.state.dataStore.employees) {
            this.$store.dispatch(GET_EMPLOYEES);
        }
      },
      employees() {
        return this.$store.state.dataStore.employees;
      },
    }
  }
  </script>
  