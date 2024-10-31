import './bootstrap';
import {createApp} from 'vue'
import {createWebHistory, createRouter} from 'vue-router'
import Main from "@/component/Main.vue";
import Home from "@/component/Home.vue";
import PrimeVue from 'primevue/config';
import ConfirmationService from 'primevue/confirmationservice'
import DialogService from 'primevue/dialogservice'
import ToastService from 'primevue/toastservice';
import AppState from "./preset/appState.js";
import Aura from '@primevue/themes/aura';
const routes = [
    {path: '/', component: Home},
    {path: '/test', component: Home},
]

const router = createRouter({
    history: createWebHistory(), // تغییر به createWebHistory
    routes,
});
const app = createApp(Main)
app.use(router)

app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            prefix: 'p',
            darkModeSelector: '.p-dark',
            cssLayer: false,
        }
    }
});

app.use(AppState);
app.use(ConfirmationService);
app.use(ToastService);
app.use(DialogService);


app.mount('#app')

