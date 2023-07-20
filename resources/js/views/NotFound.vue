<template>
  <div class="not-found">
    <h1 class="h1">404 - Niet gevonden</h1>
    <p>Whoops, deze pagina kon niet gevonden worden.</p>
    <router-link to="/dashboard">Keer terug naar dashboard</router-link>
  </div>
</template>

<script>
export default {
  name: 'NotFound',
}
</script>
