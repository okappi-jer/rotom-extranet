<template>
    <header v-if="user">
        <router-link to="/" class="app-branding" v-if="user.delivers_to == 'Orca'">
            <LogoOrca />
        </router-link>

        <router-link to="/" class="app-branding" v-else>
            <Logo />
        </router-link>

        <div class="profile-link">
            <div class="res-icon" :class="{'active': isMenuOpen}" @click="toggleMenu">
                <span></span>
                <span></span>
                <span></span>
            </div>

            <div class="info">
                Welkom, <strong>{{ user.firstname }} {{ user.name }}</strong> <span class="lotnumber">Lotnummer: {{ user.lotnumber }}</span>
            </div>
        </div>

        <div class="loading-indicator" v-show="isLoading">
            Even geduld...
        </div>
    </header>
</template>

<script>
  import Logo from './Logo';
  import LogoOrca from './LogoOrca';
  import { IS_MENU_OPEN } from '../constants';

  export default {
    name: 'HeaderComponent',
    components: {
      Logo,
      LogoOrca
    },
    data() {
      return {

      }
    },
    methods: {
      toggleMenu() {
        this.$store.dispatch('appStore/' + IS_MENU_OPEN, !this.isMenuOpen);
      },
    },
    computed: {
      user() {
        return this.$store.state.authStore.user;
      },
      isMenuOpen() {
        return this.$store.state.appStore.isMenuOpen;
      },
      isLoading() {
        return this.$store.state.appStore.isLoading;
      },
      profileUrl() {
        if (this.user) {
          return this.user.id;
        }
      },
    }
  }
</script>
