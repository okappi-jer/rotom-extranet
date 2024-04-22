<template>
    <div class="app-page">
      <div class="general-content panel flex flex-align-center">
        <div class="flex-width-50">
          <h1>Bezoekersregister</h1>
        </div>
        <div class="flex-width-50 text-right">
            <a class="btn-primary" @click="closeAll">Sluit alle open bezoekers</a>
        </div>
      </div>
      <div class="general-content panel table-content-overflow">
        <div v-if="!visitors">Er werden geen bezoekers gevonden.</div>
  
        <vue-good-table
          :columns="columns"
          :rows="visitors"
          v-if="visitors"
          styleClass="table vgt-responsive">
  
          <template slot="table-row" slot-scope="props">
            <span v-if="props.column.field == 'company'">
              <span class="block">{{ props.row.company }}</span>
            </span>
            <span v-if="props.column.field == 'name'">
              <span class="block">{{ props.row.name }}</span>
            </span>
            <span v-if="props.column.field == 'firstname'">
              <span class="block">{{ props.row.firstname }}</span>
            </span>
            <span v-if="props.column.field == 'email'">
              <span class="block">{{ props.row.email }}</span>
            </span>
            <span v-if="props.column.field == 'mobile'">
              <span class="block">{{ props.row.mobile }}</span>
            </span>
            <span v-if="props.column.field == 'contact'">
              <span class="block">{{ props.row.contact }}</span>
            </span>
            <span v-if="props.column.field == 'arrival'">
              <span v-if="returnTimezone(props.row.arrival) == 60" class="block">{{ moment(props.row.arrival).add(1, 'hours').format('DD/MM/yyyy HH:mm') }}</span>
              <span v-if="returnTimezone(props.row.arrival) == 120" class="block">{{ moment(props.row.arrival).add(2, 'hours').format('DD/MM/yyyy HH:mm') }}</span>
            </span>
            <span v-if="props.column.field == 'departure'">
              <span v-if="returnTimezone(props.row.departure) == 60" class="block">{{ moment(props.row.departure).add(1, 'hours').format('DD/MM/yyyy HH:mm') }}</span>
              <span v-if="returnTimezone(props.row.departure) == 120" class="block">{{ moment(props.row.departure).add(2, 'hours').format('DD/MM/yyyy HH:mm') }}</span>
            </span>
          </template>
        </vue-good-table>
      </div>
    </div>
  </template>
  
  <script>
  import { VueGoodTable } from 'vue-good-table';
  import {SET_PAGE_CTA, GET_VISITORS, IS_LOADING} from '../../constants';
  import axios from "axios";
  import moment from "moment";
  const api_url = process.env.MIX_API_URL;
  
  export default {
    name: 'Visitors',
    components: {
      VueGoodTable,
    },
    mounted() {
      this.getAllVisitors;

      //Set default to Paris
      //moment.tz.setDefault('Europe/Paris');
    },
    data() {
      return {
        columns: [
          {label: 'Bedrijf',       field: 'company'},
          {label: 'Naam',          field: 'name'},
          {label: 'Voornaam',      field: 'firstname'},
          {label: 'E-mail',        field: 'email'},
          {label: 'Gsm',           field: 'mobile'},
          {label: 'Contact',       field: 'contact'},
          {label: 'Aankomst',      field: 'arrival'},
          {label: 'Vertrek',       field: 'departure'},
        ],
        moment: moment
      }
    },
    computed: {
      getAllVisitors() {
        if(!this.$store.state.dataStore.visitors && this.$store.state.authStore.user) {
          if(this.$store.state.authStore.user.role === "Admin" || this.$store.state.authStore.user.role === "Visitor"){
            this.$store.dispatch(GET_VISITORS);
          }else{
            this.$router.push('/dashboard');
          }
        }
      },
      visitors() {
        return this.$store.state.dataStore.visitors;
      },
    },
    methods: {
      closeAll() {
        let formData = new FormData();
        
        this.error = null;
        this.disable = true;

        axios.post(`${api_url}/visitors/closeAll`, formData)
          .then(res => {
            this.$store.dispatch(GET_VISITORS);
          })
          .catch(err => {
            this.error = true;
            this.disable = false;

            this.errorMessage = "Er ging iets mis, probeer later even opnieuw."
        });
      },
      returnTimezone($datetime){
        const dateTime = moment($datetime);
        const offset = dateTime.utcOffset();

        return offset;
      }
    },
  }
  </script>
  