<template>
    <div class="app-page">
        <div class="general-content panel flex flex-align-center">
            <div class="flex-width-50">
                <h1>Details levering</h1>
            </div>
            <div class="flex-width-50 text-right">
                <a class="btn-primary" href="/#/delivery/overview">Sluit</a>

            </div>
        </div>
        <div class="panel">
            <div class="general-content panel table-content-overflow">
                <div v-if="!details">Er werden geen details gevonden.</div>
                <vue-good-table
                        :columns="columns"
                        :rows="details"
                        v-if="details"
                        styleClass="table vgt-responsive">

                    <template slot="table-row" slot-scope="props">
                      <span v-if="props.column.field == 'BTPLArtikelCode'">
                        <span class="block">{{ props.row.BTPLArtikelCode }}</span>
                      </span>
                      <span v-if="props.column.field == 'BTPLTekst'">
                        <span class="block">{{ props.row.BTPLTekst }}</span>
                      </span>
                      <span v-if="props.column.field == 'BTPLArticleCollie'">
                        <span class="block">{{ props.row.BTPLArticleCollie }}</span>
                      </span>
                      <span v-if="props.column.field == 'BTPLArticlePallet'">
                        <span class="block">{{ props.row.BTPLArticlePallet }} {{ props.row.BTPLPalletCode }}</span>
                      </span>
                      <span v-if="props.column.field == 'BTPLArticleWeight'">
                        <span class="block">{{ props.row.BTPLArticleWeight }}</span>
                      </span>
                    </template>
                </vue-good-table>
            </div>

        </div>
    </div>
</template>

<script>
  import {IS_LOADING} from '../../constants';
  import axios from 'axios';
  const api_url = process.env.MIX_API_URL;

  export default {
    name: 'DeliveryDetail',
    mounted() {
      //this.$store.dispatch('appStore/' + IS_LOADING, true);
      this.fetchDetails();
    },
    data() {
      return {
        details: null,
        columns: [
          {label: 'Code',           field: 'BTPLArtikelCode'},
          {label: 'Artikel',        field: 'BTPLTekst'},
          {label: '# Collie',       field: 'BTPLArticleCollie'},
          {label: '# Pallet',       field: 'BTPLArticlePallet'},
          {label: 'Gewicht',      field: 'BTPLArticleWeight'},
        ],
      }
    },
    methods: {
      fetchDetails() {
        axios.get(`${api_url}/delivery/${this.$route.params.id}`)
          .then(res => {
            this.details = res.data.data;
          })
          .then(() => { dispatch('appStore/' + IS_LOADING, false, { root: true }); })
          .catch(err => {
            console.log(err);
          });
      },
    },
  }
</script>
