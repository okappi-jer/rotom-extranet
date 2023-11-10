<template>
    <div class="app-page" v-if="isAdmin">
      <div class="general-content panel flex flex-align-center">
        <div class="flex-width-50">
          <h1>YUKI</h1>
        </div>
      </div>
  
      <div class="general-content panel table-content-overflow">
        <div class="view--dashboard">
          <div class="quick--actions">
              <div class="status-panel attention">
                  <a href="/#/yuki/open-debitors">
                    <span class="big">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>                    </span>
                    <small>Overzicht openstaande klanten</small>
                  </a>
              </div>
              <div class="status-panel">
                  <a href="/#/yuki/invoices">
                    <span class="big">
                      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </span>
                    <small>Overzicht doorgeboekte facturen</small>
                  </a>
              </div>
          </div>
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
    name: 'Yuki',
    components: {
      VueGoodTable,
    },
    mounted() {
        this.checkRole;
    },
    data() {
      return {
        isAdmin: false,
      }
    },
    computed: {
      checkRole(){
        if(this.$store.state.authStore.user && this.$store.state.authStore.user.role === "Admin") {
            this.isAdmin = true;
        }
      }
    },
    methods: {
     
    }
  }
  </script>
  