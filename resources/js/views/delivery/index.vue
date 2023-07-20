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
      <div>Er werden geen leveringen gevonden.</div>
    </div>
  </div>
</template>

<script>
import { VueGoodTable } from 'vue-good-table';
import {GET_CONTACTS} from "../../constants";

export default {
  name: 'Delivery',
  components: {
    VueGoodTable,
  },
  mounted() {

  },
  data() {
    return {

    }
  },
  computed: {

  }
}
</script>
