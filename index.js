import Vue from 'vue';
import VueResource from 'vue-resource';

import app from './App.vue';

Vue.config.devtools = true;
Vue.config.silent = false;

Vue.component('App', app);
Vue.use(VueResource);

new Vue({
    el: '#app',

});