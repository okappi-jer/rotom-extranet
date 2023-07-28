<template>
  <div class="auth-container" :style="{ 'background-image': 'url(' + backgroundUrl + ')' }">
    <div class="auth-inner">
      <div class="auth-logo text-center" v-if="$route.query.deliver_at == 'orca'">
        <LogoOrca />
      </div>
      <div class="auth-logo text-center" v-else>
        <Logo />
      </div>

      <form action="" class="login-form" @submit.prevent="resetPassword">
        <p class="error text-center" v-if="error">{{ error }}</p>
        <p class="success text-center" v-if="success">{{ success }}</p>
        <div class="input-group">
          <div class="input-group-prepend">
            <span>
              <svg id="user" xmlns="http://www.w3.org/2000/svg" width="11.783" height="13.852" viewBox="0 0 11.783 13.852">
                <g transform="translate(0)">
                  <path d="M140.025,7.464h.092a2.647,2.647,0,0,0,2.022-.875,5.247,5.247,0,0,0,.9-3.583A2.927,2.927,0,0,0,141.649.4a3.189,3.189,0,0,0-1.543-.4h-.049a3.194,3.194,0,0,0-1.543.393,2.928,2.928,0,0,0-1.408,2.613,5.247,5.247,0,0,0,.9,3.583A2.636,2.636,0,0,0,140.025,7.464Zm-2.154-4.386c0-.009,0-.017,0-.023a2.086,2.086,0,0,1,2.18-2.278h.034a2.1,2.1,0,0,1,2.18,2.278.056.056,0,0,0,0,.023,4.542,4.542,0,0,1-.709,3,1.88,1.88,0,0,1-1.477.614h-.029a1.874,1.874,0,0,1-1.474-.614A4.566,4.566,0,0,1,137.871,3.078Z" transform="translate(-134.184)" fill="#0b2249"/>
                  <path d="M47.855,262.811V262.8c0-.023,0-.046,0-.072a2.25,2.25,0,0,0-1.3-2.321l-.029-.009a8.276,8.276,0,0,1-2.381-1.084.387.387,0,0,0-.445.634,8.935,8.935,0,0,0,2.619,1.2c.668.238.743.952.763,1.606a.578.578,0,0,0,0,.072,5.207,5.207,0,0,1-.06.886,11.455,11.455,0,0,1-10.117,0,4.929,4.929,0,0,1-.06-.886c0-.023,0-.046,0-.072.02-.654.095-1.368.763-1.606a9.017,9.017,0,0,0,2.619-1.2.387.387,0,0,0-.445-.634A8.185,8.185,0,0,1,37.4,260.4l-.029.009a2.254,2.254,0,0,0-1.3,2.321.576.576,0,0,1,0,.072v.009a4.4,4.4,0,0,0,.146,1.3.368.368,0,0,0,.149.181,10.85,10.85,0,0,0,5.6,1.371,10.883,10.883,0,0,0,5.6-1.371.384.384,0,0,0,.149-.181A4.615,4.615,0,0,0,47.855,262.811Z" transform="translate(-36.073 -251.808)" fill="#0b2249"/>
                </g>
              </svg>
            </span>
          </div>
          <input type="mail" class="form-control" placeholder="E-mail" v-model="loginData.email">
        </div>

        <div class="text-center">
          <button type="submit" class="cta-link">Wijzig wachtwoord</button>
        </div>
      </form>

      <div class="auth-nav">
        <router-link v-if="$route.query.deliver_at == 'orca'" to="/login?deliver_at=orca">Aanmelden</router-link>
        <router-link v-else to="/login">Aanmelden</router-link>
      </div>
    </div>
  </div>
</template>

<script>
import Logo from '../../components/Logo';
import LogoOrca from '../../components/LogoOrca';

import { LOGIN_USER } from '../../constants';

const api_url = process.env.MIX_API_URL;

export default {
  name: 'ResetPassword',
  components: {
    Logo,
    LogoOrca
  },
  mounted() {
    if(localStorage.getItem('key')) {
      this.$router.push('/dashboard');
    }
    this.csrfToken = window.Laravel.csrfToken;

    if(this.$route.query.deliver_at == 'orca'){
      this.backgroundUrl = './images/background-orca.jpeg'
    }
  },
  data() {
    return {
      csrfToken: null,
      error: null,
      success: null,
      loginData: {
        email: '',
      },
      backgroundUrl: './images/background.jpeg',
    }
  },
  methods: {
    resetPassword() {
      let formData = new FormData();
      formData.append('email', this.loginData.email);
      this.error = null;
      this.success = null;

      axios.post(`${api_url}/auth/password-reset`, formData)
        .then(res => {
          this.success = res.data.message;
        })
        .catch(err => {
          this.error = err.response.data.message;
        });
    },
  },
}
</script>
