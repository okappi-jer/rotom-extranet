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
        </template>
      </vue-good-table>
    </div>
  </div>
</template>

<script>
import { VueGoodTable } from 'vue-good-table';
import {SET_PAGE_CTA, GET_CONTACTS, IS_LOADING} from '../../constants';

export default {
  name: 'Contacten',
  components: {
    VueGoodTable,
  },
  mounted() {
    this.getAllContacts;
  },
  data() {
    return {
      columns: [
        {label: 'Naam',           field: 'name'},
        {label: 'Code',           field: 'supplier_code'},
        {label: 'Wachtwoord',     field: 'password_plain'},
        {label: 'Login',          field: 'email'},
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
    contacts() {
      return this.$store.state.dataStore.contacts;
    },
  }
}
</script>
