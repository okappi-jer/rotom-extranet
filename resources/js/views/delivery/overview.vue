<template>
    <div class="app-page">
        <div class="general-content panel flex flex-align-center">
            <div class="flex-width-50">
                <h1>Overzicht leveringen</h1>
            </div>
            <div class="flex-width-50 text-right">
                <a class="btn-primary" href="/#/delivery">Voeg levering toe</a>
            </div>
        </div>
        <div class="general-content panel table-content-overflow">
            <div v-if="!deliveries">Er werden geen leveringen gevonden.</div>
            <vue-good-table
                    :columns="columns"
                    :rows="deliveries"
                    :search-options="{
                      enabled: true,
                      placeholder: 'Zoek leveringen...',
                    }"
                    :pagination-options="{
                      enabled: true, 
                      perPage: 20,
                    }"
                    v-if="deliveries"
                    styleClass="table vgt-responsive">

                <template slot="table-row" slot-scope="props">
                  <span v-if="props.column.field == 'unique_id'">
                    <span class="block">
                      <router-link :to="`/delivery/${props.row.unique_id}`">{{ props.row.unique_id }}</router-link>
                    </span>
                  </span>
                   <span v-if="props.column.field == 'lotnumber'">
                    <span class="block">{{ props.row.lotnumber }}</span>
                  </span>
                  <span v-if="props.column.field == 'created_at'">
                    <span class="block">{{ props.row.created_at }}</span>
                  </span>
                </template>
            </vue-good-table>
        </div>
    </div>
</template>

<script>
  import { VueGoodTable } from 'vue-good-table';
  import { SET_PAGE_CTA, GET_DELIVERIES, IS_LOADING } from '../../constants';
  import axios from "axios";

  const api_url = process.env.MIX_API_URL;

  export default {
    name: 'DeliveryOverview',
    components: {
      VueGoodTable,
    },
    mounted() {
      this.getAllDeliveries;
    },
    data() {
      return {
        columns: [
          { label: 'ID Levering', field: 'unique_id' },
          { label: 'Lotnummer', field: 'lotnumber' },
          { label: 'Creatiedatum', field: 'created_at' },
        ],
        deliveries: null,
      };
    },
    computed: {
      getAllDeliveries() {
        if (this.$store.state.authStore.user) {
          axios.get(`${api_url}/delivery/all`)
            .then(res => {
              this.deliveries = res.data.data;
            })
            .then(() => { dispatch('appStore/' + IS_LOADING, false, { root: true }); })
            .catch(err => {
              console.log(err);
            });
        } else {
          this.$router.push('/dashboard');
        }
      },
    },
  };
</script>
