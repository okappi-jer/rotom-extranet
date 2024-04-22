<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Voeg medewerker toe</h1>
      </div>
      <div class="flex-width-50 text-right">
        <a class="btn-primary" href="/#/employees">Annuleer</a>
      </div>
    </div>
    <div class="panel">
      <div class="notification-error" v-if="error">
        {{ errorMessage }}
      </div>
      <form class="comment-form" @submit.prevent="handleSubmit">
        <div class="form-group-wrapper">
          <div class="form-group">
            <label for="naam">Naam</label>
            <input type="text" id="naam" v-model="newEmployee.name" placeholder="Naam" />
          </div>
        </div>

        <div class="form-group-wrapper">
          <div class="form-group half">
            <label for="email">E-mail</label>
            <input type="text" id="email" v-model="newEmployee.email" placeholder="Email" />
          </div>
          <div class="form-group half">
            <label for="mobile">GSM</label>
            <input type="text" id="mobile" v-model="newEmployee.mobile" placeholder="GSM" />
          </div>
        </div>
        
        <button type="submit" class="btn-blue">Bewaar medewerker</button>
      </form>
    </div>
  </div>
</template>

<script>
import { SET_PAGE_TITLE, SET_PAGE_CTA, IS_LOADING, GET_EMPLOYEES } from '../../constants';
import axios from 'axios';
const api_url = process.env.MIX_API_URL;

export default {
  name: 'EmployeesCreate',
  mounted() {

  },
  data() {
    return {
      newEmployee: {
        name: '',
        mobile: '',
        email: '',
      },
      errorMessage: null,
      error: false,
    }
  },
  methods: {
    handleSubmit() {
      let formData = new FormData();
      formData.append('mobile', this.newEmployee.mobile);
      formData.append('name', this.newEmployee.name);
      formData.append('email', this.newEmployee.email);

      this.error = null;

      axios.post(`${api_url}/employees/store`, formData)
        .then(res => {
          this.$store.dispatch(GET_EMPLOYEES);
          this.$router.push('/employees');
        })
        .catch(err => {
          this.error = true;
          this.errorMessage = "Gelieve alle velden correct in te vullen."
        });
    },
  },
}
</script>
