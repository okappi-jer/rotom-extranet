<template>
  <div class="app-page">
    <div class="general-content panel flex flex-align-center">
      <div class="flex-width-50">
        <h1>Voeg levering toe</h1>
      </div>
      <div class="flex-width-50 text-right">

      </div>
    </div>

    <div class="general-content panel table-content-overflow">
      <div class="notification-error" v-if="error">
        {{ errorMessage }}
      </div>
      <div class="notification-success" v-if="success">
        {{ successMessage }}
      </div>

      <div v-if="count == 0">
        Er werden geen templates gevonden.
      </div>

      <!--
            $table->text('unique_id');
            V $table->text('BTPLOrderDeliveryDate');
            V $table->text('BTPLOrderDeliveryAt');
            V $table->text('BTPLOrderReference');
      -->

      <form v-if="count != 0" class="template-form" @submit.prevent="handleSubmit">
        <div class="form-group-wrapper highlighted">
          <div class="form-group">
            <label for="deliverydate">Leveringsdatum</label>
            <input type="date" id="deliverydate" v-model="deliverydate" placeholder="Leveringsdatum" />
          </div>
          <div class="form-group">
            <label for="delivery_at">Levering bij</label>
            <input readonly class="read-only" type="text" id="delivery_at" v-model="delivery_at" placeholder="Levering bij" />
          </div>
          <div class="form-group">
            <label for="reference">Referentie</label>
            <input type="text" id="reference" v-model="reference" placeholder="Referentie" />
          </div>
        </div>

        <div class="form-group-label-wrapper">
          <label for="">Artikel</label>
          <label for="">Kaliber</label>
          <label for="">Verpakking</label>
          <label for="">Aantal collie</label>
          <label for="">Gewicht</label>
          <label for="">Opmerking</label>
        </div>

        <div class="form-group-wrapper">
          <DeliveryInput v-for="(template, index) in templates" :key="'template-' + index" :template="template" @articleChanged="editArticle"></DeliveryInput>
          <button type="submit" class="btn-blue">Meld levering aan</button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { VueGoodTable } from 'vue-good-table';
import axios from "axios";
import DeliveryInput from "../../components/DeliveryInput";

const api_url = process.env.MIX_API_URL;

export default {
  name: 'Delivery',
  components: {
    VueGoodTable,
    DeliveryInput
  },
  mounted() {
    this.getTemplates;
  },
  data() {
    return {
      templates: null,
      count: 0,
      delivery: [],
      delivery_at: '',
      reference: '',
      deliverydate: '',
      success: false,
      error: false,
      successMessage: 'De levering werd met succes ingegeven',
      errorMessage: 'Er ging iets mis, neem contact op met Rotom'
    }
  },
  computed: {
    getTemplates(){
      if(this.$store.state.authStore.user) {
        axios.get(`${api_url}/templates`)
          .then(res => {
            this.templates = res.data.templates;
            this.delivery_at = this.$store.state.authStore.user.delivers_to;
            this.count = res.data.count;
          })
          .catch(err => {
            console.log(err);
          });
      }else{
        this.$router.push('/dashboard');
      }
    }
  },
  methods: {
    handleSubmit(){
      this.success = false;
      this.error = false;

      let formData = new FormData();
      formData.append('delivery', JSON.stringify(this.delivery));
      formData.append('delivery_date', this.deliverydate);
      formData.append('reference', this.reference);
      axios.post(`${api_url}/delivery/store`, formData)
        .then(res => {
          this.success = true;
        })
        .catch(err => {
          this.error = true;
        });
    },
    editArticle(newVal){
      //Clear array
      this.containsObject(newVal, this.delivery)

      //Add new item
      this.delivery.push( newVal );
    },
    containsObject(obj, list) {
      var i;

      for (i = 0; i < list.length; i++) {
        if (list[i]['BTPLLeverCode'] === obj['BTPLLeverCode'] && list[i]['BTPLSchema'] === obj['BTPLSchema'] && list[i]['BTPLLijnnr'] === obj['BTPLLijnnr']) {
          //list.remove(list[i]);
          Vue.delete(list, i);
        }
      }
    }
  }
}
</script>
