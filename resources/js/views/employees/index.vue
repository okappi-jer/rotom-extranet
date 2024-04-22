<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Medewerkers</h1>
      </div>
      <div class="flex-width-50 text-right">
        <a class="btn-primary" href="/#/employees/create">Voeg medewerker toe</a>
      </div>
    </div>
    <div class="general-content panel table-content-overflow">
      <div v-if="!employees">Er werden geen medewerkers gevonden.</div>

      <vue-good-table
        :columns="columns"
        :rows="employees"
        v-if="employees"
        styleClass="table vgt-responsive">

        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'name'">
            <span class="block">
              <router-link :to="`/employees/edit/${props.row.id}`">{{ props.row.name }}</router-link>
            </span>
          </span>
          <span v-if="props.column.field == 'email'">
            <span class="block">{{ props.row.email }}</span>
          </span>
          <span v-if="props.column.field == 'mobile'">
            <span class="block">{{ props.row.mobile }}</span>
          </span>
        </template>
      </vue-good-table>
    </div>
  </div>
</template>

<script>
import { VueGoodTable } from 'vue-good-table';
import {SET_PAGE_CTA, GET_EMPLOYEES, IS_LOADING} from '../../constants';
import axios from "axios";
const api_url = process.env.MIX_API_URL;

export default {
  name: 'Employees',
  components: {
    VueGoodTable,
  },
  mounted() {
    this.getAllEmployees;
  },
  data() {
    return {
      columns: [
        {label: 'Naam',           field: 'name'},
        {label: 'E-mail',         field: 'email'},
        {label: 'GSM',            field: 'mobile'},
      ],
    }
  },
  computed: {
    getAllEmployees() {
      if(!this.$store.state.dataStore.employees && this.$store.state.authStore.user) {
        if(this.$store.state.authStore.user.role === "Admin"){
          this.$store.dispatch(GET_EMPLOYEES);
        }else{
          this.$router.push('/dashboard');
        }
      }
    },
    employees() {
      return this.$store.state.dataStore.employees;
    },
  }
}
</script>
