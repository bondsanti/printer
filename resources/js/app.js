require('./bootstrap'); //ok

import {createApp} from 'vue';
import router from './router';
import App from './components/App.vue';
import HighchartsVue from 'highcharts-vue'

createApp(App).use(router).use(HighchartsVue).mount('#appvue');
