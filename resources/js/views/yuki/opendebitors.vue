<template>
    <div class="app-page" v-if="isAdmin">
      <div class="general-content panel flex flex-align-center">
        <div class="flex-width-50">
          <h1>YUKI - Openstaande klanten</h1>
        </div>
        <div class="flex-width-50 text-right">
            <button class="btn btn-primary" :disabled="disable" @click="syncAgain">{{ syncButton }}</button>
        </div>
      </div>
  
      <div class="general-content panel table-content-overflow" id="yuki">
        <vue-good-table
            v-if="outstandings.length > 0"
            :columns="columns"
            :rows="outstandings"
            :searchOptions="{
                enabled: true,
                placeholder: 'Zoek...',
                searchOnColumns: ['invoice_from', 'invoice_to', 'amount_outstanding']
            }"
            :paginationOptions="{
                enabled: true,
                //perPageDropdown: [10, 20, 50],
                perPage: 20,
                dropdownAllowAll: true,
                //nextLabel: '>>',  
                //prevLabel: '<<',
            }"
            styleClass="table vgt-responsive">
            
            <template slot="table-row" slot-scope="props">
                <span v-if="props.column.field == 'invoice_to'">
                <span class="block">{{ props.row.invoice_to }}</span>
                </span>
                <span v-if="props.column.field == 'invoice_from'"> 
                <span class="block">{{ props.row.invoice_from }}</span>
                </span>
                <span v-if="props.column.field == 'amount_outstanding'">
                <span class="block">€ {{ parseFloat(props.row.amount_outstanding).toFixed(2) }}</span>
                </span>
                <span v-if="props.column.field == 'amount_due'">
                <span class="block">€ {{ parseFloat(props.row.amount_due).toFixed(2) }}</span>
                </span>
            </template>
            </vue-good-table>

            <div v-else>
                Geen openstaande klanten gevonden.
            </div>
      </div>
    </div>

    <div v-else>
        <p>Admins only</p>
    </div>
  </template>
  
  <script>
  import { VueGoodTable } from 'vue-good-table';
  import axios from "axios";
  
  const api_url = process.env.MIX_API_URL;
  
  export default {
    name: 'OpenDebitors',
    components: {
      VueGoodTable,
    },
    mounted() {
        this.checkRole();
    },
    data() {
      return {
        isAdmin: false,
        outstandings: [],
        syncButton: "Sync saldos",
        disable: false,
        columns: [
            {label: 'Factuur van',          field: 'invoice_from'},
            {label: 'Factuur naar',         field: 'invoice_to'},
            {
              label: 'Totaal bedrag',        
              field: 'amount_outstanding',    
              type: 'number'
            },
            {
              label: 'Vervallen bedrag',     
              field: 'amount_due',
              type: 'number'
            },
        ],
      }
    },
    computed: {
      
    },
    methods: {
        checkRole(){
            if(this.$store.state.authStore.user && this.$store.state.authStore.user.role === "Admin") {
                this.isAdmin = true;
                this.getOpenDebitors();
            }
        },
        getOpenDebitors(){
            axios.get(`${api_url}/yuki/show-outstanding-debitors`)
            .then(res => {
                this.outstandings = res.data.data;
            })
            .catch(err => {
                console.log(err);
            });
        },
        syncAgain() {
          let formData = new FormData();
          
          this.error = null;
          this.disable = true;
          this.syncButton = "Even geduld ..."

          axios.post(`${api_url}/yuki/get-outstanding-debitors`, formData)
            .then(res => {
              this.getOpenDebitors;
              this.syncButton = 'Sync saldos';
            })
            .catch(err => {
              this.error = true;
              this.disable = false;

              this.errorMessage = "Er ging iets mis, probeer later even opnieuw."
          });
        },      
    }
  }
  </script>
  