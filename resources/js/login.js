import './bootstrap';
import {createApp} from 'vue'
import 'primeicons/primeicons.css'

import Login from "@/component/Login.vue";
import PrimeVue from "primevue/config";
import Aura from "@primevue/themes/aura";
import AppState from "./preset/appState.js";
import ConfirmationService from "primevue/confirmationservice";
import ToastService from "primevue/toastservice";
import DialogService from "primevue/dialogservice";

const app = createApp(Login)

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
