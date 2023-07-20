<template>
    <div class="app-page">
        <div class="general-content panel flex flex-align-center">
            <div class="flex-width-50">
                <h1>Aankoopborderels</h1>
            </div>
            <div class="flex-width-50 text-right">

            </div>
        </div>
        <div class="general-content panel table-content-overflow">
            <vue-good-table
                :columns="columns"
                :rows="borderels"
                :search-options="{
                 enabled: true,
                 placeholder: 'Doorzoek aankoopborderels',
                }"
                v-if="borderels"
                styleClass="table vgt-responsive">

                <template slot="table-row" slot-scope="props">
                  <span v-if="props.column.field == 'filename'">
                    <span class="block">{{ props.row.filename }}</span>
                  </span>
                  <span v-if="props.column.field == 'date'">
                    <span class="block">{{ props.row.date }}</span>
                  </span>
                  <span v-if="props.column.field == 'url'">
                    <span class="block tr-svg">
                        <a target="_blank" :href="props.row.url">
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 7.5h-.75A2.25 2.25 0 004.5 9.75v7.5a2.25 2.25 0 002.25 2.25h7.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25h-.75m-6 3.75l3 3m0 0l3-3m-3 3V1.5m6 9h.75a2.25 2.25 0 012.25 2.25v7.5a2.25 2.25 0 01-2.25 2.25h-7.5a2.25 2.25 0 01-2.25-2.25v-.75" /></svg>
                        </a>
                    </span>
                  </span>
                </template>
            </vue-good-table>
        </div>
    </div>
</template>

<script>
  import { VueGoodTable } from 'vue-good-table';
  import axios from "axios";
  import {GET_CONTACTS, IS_LOADING} from "../../constants";

  const api_url = process.env.MIX_API_URL;

  export default {
    name: 'Borderels',
    components: {
      VueGoodTable,
    },
    mounted() {
      this.getDocuments;
    },
    data() {
      return {
        borderels: null,
        columns: [
          {label: 'Naam',           field: 'filename'},
          {label: 'Datum',          field: 'date'},
          {label: 'Download',       field: 'url'},
        ],
      }
    },
    computed: {
      getDocuments(){
        if(this.$store.state.authStore.user) {
          axios.get(`${api_url}/borderels`)
            .then(res => {
              this.borderels = res.data.borderels;
            })
            .catch(err => {
              console.log(err);
            });
        }else{
          this.$router.push('/dashboard');
        }
      }
    }
  }
</script>
