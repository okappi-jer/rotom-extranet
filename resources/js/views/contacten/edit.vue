<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Wijzig gebruiker</h1>
      </div>
      <div class="flex-width-50 text-right">
        <a class="btn-primary" href="/#/users">Annuleer</a>

      </div>
    </div>
    <div class="panel">
      <div class="notification-error" v-if="error">
        {{ errorMessage }}
      </div>
      <div class="notification-success" v-if="success">
        {{ successMessage }}
      </div>
      <form class="comment-form" @submit.prevent="handleSubmit">
        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="delivers_to">Levert aan:</label>
            <input  class="read-only" readonly type="text" id="delivers_to" v-model="newUser.delivers_to" placeholder="Levert aan" />
          </div>
          <div class="form-group half">
            <label for="company">Bedrijf:</label>
            <input type="text" id="company" v-model="newUser.company" placeholder="Bedrijfsnaam" />
          </div>
        </div>

        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="naam">Naam</label>
            <input type="text" id="naam" v-model="newUser.name" placeholder="Naam" />
          </div>

          <div class="form-group half">
            <label for="voornaam">Voornaam</label>
            <input type="text" id="voornaam" v-model="newUser.firstname" placeholder="Voornaam" />
          </div>
        </div>

        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="email">E-mail</label>
            <input class="read-only" type="text" id="email" v-model="newUser.email" placeholder="Email" readonly/>
          </div>
          <div class="form-group half">
            <label for="supplier_code">Leverancierscode</label>
            <input type="text" id="supplier_code" v-model="newUser.supplier_code" placeholder="Leverancierscode" />
          </div>
        </div>

        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="role">Functie</label>
            <select class="read-only" name="role" id="role" v-model="newUser.role" readonly>
              <option :value="newUser.role">{{ newUser.role }}</option>
            </select>
          </div>
          <div class="form-group half">
            <label for="password">Wachtwoord</label>
            <input type="text" id="password" v-model="newUser.password" placeholder="Wachtwoord" />
          </div>
        </div>
        <button type="submit" class="btn-blue">Bewaar gebruiker</button>
      </form>
    </div>
    <div class="panel" v-if="canDelete">
      <form class="delete-form" @submit.prevent="handleDelete">
          <p>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
            </svg>

            Via onderstaande knop kan je de gebruiker verwijderen en zijn toegang tot het extranet annuleren, be carefull want weg is weg! ;-)</p>
        <button type="submit" class="btn-blue">Verwijder gebruiker</button>
      </form>
    </div>
  </div>
</template>

<script>
  import {GET_CONTACTS, IS_LOADING} from '../../constants';
import axios from 'axios';
const api_url = process.env.MIX_API_URL;

export default {
  name: 'ContactenEdit',
  mounted() {
    this.$store.dispatch('appStore/' + IS_LOADING, true);

    const allContacts = this.$store.state.dataStore.contacts;

    if(allContacts) {
      this.contact = allContacts.find(e => e.id == this.$route.params.id);
      this.prefillUser();
      this.$store.dispatch('appStore/' + IS_LOADING, false);
    } else {
      this.$router.push('/users');
    }
  },
  data() {
    return {
      newUser: {
        name: '',
        firstname: '',
        password: '',
        email: '',
        supplier_code: '',
        role: '',
      },
      activeBtn: '',
      contact: null,
      successMessage: "Gebruiker werd met succes aangepast!",
      errorMessage: "Er ging iets mis, probeer later nog een keer.",
      success: false,
      error: false,
      canDelete: false,
    }
  },
  methods: {
    handleSubmit() {
      this.success = false;
      this.error = false;

      let formData = new FormData();
      formData.append('name', this.newUser.name);
      formData.append('firstname', this.newUser.firstname);
      formData.append('supplier_code', this.newUser.supplier_code);
      formData.append('password', this.newUser.password);

      axios.post(`${api_url}/contacts/${this.contact.id}/update`, formData)
        .then(res => {
          this.success = true;
        })
        .catch(err => {
          this.error = true;
        });
    },
    handleDelete(){
      let formData = new FormData();

      axios.post(`${api_url}/contacts/${this.contact.id}/delete`, formData)
        .then(res => {
          this.$store.dispatch(GET_CONTACTS);
          this.$router.push('/users');
        })
        .catch(err => {
          this.error = true;
        });
    },
    prefillUser() {
      this.newUser.name = this.contact.name;
      this.newUser.firstname = this.contact.firstname;
      this.newUser.email = this.contact.email;
      this.newUser.supplier_code = this.contact.supplier_code;
      this.newUser.role = this.contact.role;
      this.newUser.company = this.contact.company;
      this.newUser.delivers_to = this.contact.delivers_to;

      if(this.contact.id !== this.$store.state.authStore.user.id){
        this.canDelete = true;
      }
    },
  },
}
</script>
