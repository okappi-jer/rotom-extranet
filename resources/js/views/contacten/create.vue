<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Voeg gebruiker toe</h1>
      </div>
      <div class="flex-width-50 text-right">
        <a class="btn-primary" href="/#/users">Annuleer</a>
      </div>
    </div>
    <div class="panel">
      <div class="notification-error" v-if="error">
        {{ errorMessage }}
      </div>
      <form class="comment-form" @submit.prevent="handleSubmit">
        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="delivers_to">Levert aan:</label>
            <select name="delivers_to" id="delivers_to" v-model="newUser.delivers_to">
              <option value="Rotom">Rotom</option>
              <option value="Orca">Orca</option>
            </select>
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
            <input type="text" id="email" v-model="newUser.email" placeholder="Email" />
          </div>
          <div class="form-group half">
            <label for="supplier_code">Leverancierscode</label>
            <input type="text" id="supplier_code" v-model="newUser.supplier_code" placeholder="Leverancierscode" />
          </div>
        </div>

        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="role">Functie</label>
            <select name="role" id="role" v-model="newUser.role">
              <option value="User">User</option>
              <option value="Admin">Admin</option>
              <option value="DeliveryAdmin">Delivery Admin</option>
              <option value="Visitor">Visitor Admin</option>
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
  </div>
</template>

<script>
import { SET_PAGE_TITLE, SET_PAGE_CTA, IS_LOADING, GET_CONTACTS } from '../../constants';
import axios from 'axios';
const api_url = process.env.MIX_API_URL;

export default {
  name: 'ContactenCreate',
  mounted() {

  },
  data() {
    return {
      newUser: {
        name: '',
        firstname: '',
        email: '',
        supplier_code: '',
        password: '',
        role: 'User',
        company: '',
        delivers_to: ''
      },
      errorMessage: null,
      error: false,
    }
  },
  methods: {
    handleSubmit() {
      let formData = new FormData();
      formData.append('company', this.newUser.company);
      formData.append('delivers_to', this.newUser.delivers_to);
      formData.append('name', this.newUser.name);
      formData.append('firstname', this.newUser.firstname);
      formData.append('email', this.newUser.email);
      formData.append('supplier_code', this.newUser.supplier_code);
      formData.append('password', this.newUser.password);
      formData.append('role', this.newUser.role);
      formData.append('creatio_account_id', this.$store.state.authStore.user.creatio_account_id);

      this.error = null;

      axios.post(`${api_url}/contacts/store`, formData)
        .then(res => {
          this.$store.dispatch(GET_CONTACTS);
          this.$router.push('/users');
        })
        .catch(err => {
          this.error = true;
          this.errorMessage = "Gelieve alle velden correct in te vullen."
        });
    },
  },
}
</script>
