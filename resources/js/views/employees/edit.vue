<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Wijzig medewerker</h1>
      </div>
      <div class="flex-width-50 text-right">
        <a class="btn-primary" href="/#/employees">Annuleer</a>

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

        <button type="submit" class="btn-blue">Bewaar gebruiker</button>
      </form>
    </div>
    <div class="panel" v-if="canDelete">
      <form class="delete-form" @submit.prevent="handleDelete">
          <p>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0M3.124 7.5A8.969 8.969 0 015.292 3m13.416 0a8.969 8.969 0 012.168 4.5" />
            </svg>

            Via onderstaande knop kan je de medewerker verwijderen en zijn toegang tot het extranet annuleren, be carefull want weg is weg! ;-)</p>
        <button type="submit" class="btn-blue">Verwijder medewerker</button>
      </form>
    </div>
  </div>
</template>

<script>
  import {GET_EMPLOYEES, IS_LOADING} from '../../constants';
import axios from 'axios';
const api_url = process.env.MIX_API_URL;

export default {
  name: 'EmployeesEdit',
  mounted() {
    this.$store.dispatch('appStore/' + IS_LOADING, true);

    const allEmployees = this.$store.state.dataStore.employees;

    if(allEmployees) {
      this.employee = allEmployees.find(e => e.id == this.$route.params.id);
      this.prefillUser();
      this.$store.dispatch('appStore/' + IS_LOADING, false);
    } else {
      this.$router.push('/employees');
    }
  },
  data() {
    return {
      newEmployee: {
        name: '',
        email: '',
        mobile: '',
      },
      activeBtn: '',
      employee: null,
      successMessage: "Medewerker werd met succes aangepast!",
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
      formData.append('mobile', this.newEmployee.mobile);
      formData.append('name', this.newEmployee.name);
      formData.append('email', this.newEmployee.email);

      axios.post(`${api_url}/employees/${this.employee.id}/update`, formData)
        .then(res => {          
          this.success = true;

          this.$store.dispatch(GET_EMPLOYEES);
          this.$router.push('/employees');
        })
        .catch(err => {
          this.error = true;
        });
    },
    handleDelete(){
      let formData = new FormData();

      axios.post(`${api_url}/employees/${this.employee.id}/delete`, formData)
        .then(res => {
          this.$store.dispatch(GET_EMPLOYEES);
          this.$router.push('/employees');
        })
        .catch(err => {
          this.error = true;
        });
    },
    prefillUser() {
      this.newEmployee.name = this.employee.name;
      this.newEmployee.mobile = this.employee.mobile;
      this.newEmployee.email = this.employee.email;

      this.canDelete = true;
      
    },
  },
}
</script>
