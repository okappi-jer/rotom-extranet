<template>
    <div class="app-page">
        <div class="general-content panel flex flex-align-center">
            <div class="flex-width-50">
                <h1>Stuur nieuwe codes uit</h1>
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

            <form class="template-form" @submit.prevent="handleSubmit">
                <div class="form-group-wrapper">
                    <div class="form-group">
                        <label for="file">Excel met codes</label>
                        <input type="file" ref="file" id="file" v-on:change="handleFileUpload()">
                    </div>
                </div>

                <div class="form-group-wrapper">
                    <div class="form-group">
                        <button type="submit" class="btn-blue">Stuur codes uit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
  import axios from "axios";

  const api_url = process.env.MIX_API_URL;

  export default {
    name: 'Delivery',
    components: {
    },
    mounted() {

    },
    data() {
      return {
        file: null,
        success: false,
        error: false,
        successMessage: 'De codes werden met succes verzonden',
        errorMessage: 'Er ging iets mis, neem contact op met Rotom'
      }
    },
    computed: {

    },
    methods: {
      handleSubmit(){
        this.success = false;
        this.error = false;

        let formData = new FormData();
        formData.append('file', this.file);

        axios.post(`${api_url}/gatecontrols/store`, formData)
          .then(res => {
            this.success = true;
          })
          .catch(err => {
            this.error = true;
          });
      },
      handleFileUpload(){
        this.file = this.$refs.file.files[0];
      },
    }
  }
</script>
