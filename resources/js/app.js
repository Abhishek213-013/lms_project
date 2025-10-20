import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';

// Import global CSS
import '../css/app.css';

const app = createApp(App);

// Use plugins
app.use(router);
app.use(store);

// Add global properties
app.config.globalProperties.$http = window.axios;

app.mount('#app');