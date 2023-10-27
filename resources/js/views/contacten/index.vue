<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Gebruikers</h1>
      </div>
      <div class="flex-width-50 text-right">
        <a class="btn-primary" href="/#/users/create">Voeg gebruiker toe</a>
      </div>
    </div>
    <div class="general-content panel table-content-overflow">
      <div class="notification-lotnumber">
        <p>Laatst toegewezen lotnummer: <strong>{{ last_lotnumber }}</strong></p>
        <p>Volgend lotnummer: <strong>{{ next_lotnumber }}</strong></p>
      </div>

      <div v-if="!contacts">Er werden geen contacten gevonden.</div>

      <vue-good-table
        :columns="columns"
        :rows="contacts"
        v-if="contacts"
        styleClass="table vgt-responsive">

        <template slot="table-row" slot-scope="props">
          <span v-if="props.column.field == 'name'">
            <span class="block">
              <router-link :to="`/users/edit/${props.row.id}`">{{ props.row.firstname }} {{ props.row.name }}</router-link>
            </span>
          </span>
          <span v-if="props.column.field == 'supplier_code'">
            <span class="block">{{ props.row.supplier_code }}</span>
          </span>
          <span v-if="props.column.field == 'password_plain'">
            <span class="block">{{ props.row.password_plain }}</span>
          </span>
          <span v-if="props.column.field == 'email'">
            <span class="block">{{ props.row.email }}</span>
          </span>
          <span v-if="props.column.field == 'lotnumber'">
            <span class="block">{{ props.row.lotnumber }}</span>
          </span>
        </template>
      </vue-good-table>
    </div>
  </div>
</template>

<script>
import { VueGoodTable } from 'vue-good-table';
import {SET_PAGE_CTA, GET_CONTACTS, IS_LOADING} from '../../constants';
import axios from "axios";
const api_url = process.env.MIX_API_URL;

export default {
  name: 'Contacten',
  components: {
    VueGoodTable,
  },
  mounted() {
    this.getAllContacts;
    this.getLotNumberInfo;
  },
  data() {
    return {
      last_lotnumber: '-',
      next_lotnumber: '-',
      columns: [
        {label: 'Naam',           field: 'name'},
        {label: 'Code',           field: 'supplier_code'},
        {label: 'Wachtwoord',     field: 'password_plain'},
        {label: 'Login',          field: 'email'},
        {label: 'Lotnummer',      field: 'lotnumber'},
      ],
    }
  },
  computed: {
    getAllContacts() {
      if(!this.$store.state.dataStore.contacts && this.$store.state.authStore.user) {
        if(this.$store.state.authStore.user.role === "Admin"){
          this.$store.dispatch(GET_CONTACTS);
        }else{
          this.$router.push('/dashboard');
        }
      }
    },
    getLotNumberInfo(){
      axios.get(`${api_url}/contacts/get-lotnumber-info`)
              .then(res => {
                this.last_lotnumber = res.data.last_lotnumber;
                this.next_lotnumber = res.data.new_lotnumber;
              })
              .catch(err => {
                console.log(err);
              });

    },
    contacts() {
      return this.$store.state.dataStore.contacts;
    },
  }
}
</script>
