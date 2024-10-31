<script setup>
import {onMounted, ref} from "vue";
import axios from "axios";
import {useConfirm} from "primevue/useconfirm";
import {useToast} from "primevue/usetoast";
import ConfirmDialog from "primevue/confirmdialog";
import Toast from "primevue/toast";
import Button from "primevue/button";

const auth = window.auth;
const reservations = ref([]);
const errorMessage = ref('')
const successMessage = ref('')

const logout = () => {
    axios.get('/login/logout').then(r => {
        window.open('/', '_self')
    });
}
const refresh = () => {
    axios.post('/reservation').then(r => {
        reservations.value = r.data;
    })
}
onMounted(() => {
    refresh();
    if (auth.marriage == 2) {
        toast.add({severity: 'error', summary: 'ناموفق', detail: 'این فرد مجرد می باشد!', life: 6000});
    }
})
const reservation = () => {
    axios.put('/reservation').then(r => {
        refresh();
        if (!r.data.status) {
            toast.add({severity: 'error', summary: 'ناموفق', detail: r.data.message, life: 10000});
        } else {
            toast.add({severity: 'info', summary: 'موفق', detail: r.data.message, life: 8000});
        }
    }).catch(r => {

    })
}


const confirm = useConfirm();
const toast = useToast();

const confirm1 = () => {
    confirm.require({
        message: 'message',
        header: 'شرایط و ضوابط دریافت عبادات',
        icon: 'pi pi-exclamation-triangle',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true
        },
        acceptProps: {
            label: 'Save'
        },
        accept: () => {
            toast.add({severity: 'info', summary: 'تائید درخواست', detail: 'منتظر باشید!', life: 3000});
            reservation();
        },
        reject: () => {
            toast.add({
                severity: 'error',
                summary: 'رد درخواست',
                detail: 'به دلیل قبول نکردن قوانین نوبت برای شما ثبت نشد',
                life: 8000
            });
        }
    });
};
</script>

<template>
    <Toast/>
    <ConfirmDialog>
        <template #container="{ message, acceptCallback, rejectCallback }">
            <div class="flex flex-col items-center p-8 bg-surface-0 dark:bg-surface-900 rounded">
                <span class="font-bold text-1xl block mb-6">{{ message.header }}</span>
                <p class="text-sm overflow-hidden">
                    اینجانب {{ auth.first_name }} {{ auth.last_name }} شرعا ضمانت میکنم:
                </p>
                <ol class="list-decimal">

                    <li>
                        1- حق واگذاری عبادت به دیگران را ندارم.
                    </li>
                    <li>
                        2- عبادت محوله را به نحو صحیح و کامل انجام داده و در قیامت پاسخگو خواهم بود.
                    </li>
                    <li>
                        3-بعد از انجام صحیح عبادت، حداکثر تا تاریخ تعیین شده جهت تحویل قبض عبادت به دفتر مراجعه نمایم.
                    </li>
                    <li>
                        4- دریافت وجه عبادات پس از انجام صحیح و کامل جایز بوده و هرگونه تصرف قبل از انجام آن شرعا صحیح
                        نمی‌باشد.
                    </li>
                </ol>
                <div class="flex items-center gap-2 mt-6">
                    <Button label="تائید درخواست" @click="acceptCallback" class="w-32"></Button>
                    <Button label="رد" outlined @click="rejectCallback" class="w-32"></Button>
                </div>
            </div>
        </template>
    </ConfirmDialog>

    <div class="container mx-auto mt-10">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="flex items-center mb-4">
                <div
                    @click="logout()"
                    class="w-12 h-12 rounded-full bg-amber-950 flex items-center justify-center text-white text-lg font-bold mr-4 ml-4 cursor-pointer">
                    خروج
                </div>
                <div>
                    <h2 class="text-xl font-semibold h-full overflow-y-hidden">
                        {{ auth.first_name }} {{ auth.last_name }}
                    </h2>
                    <p class="text-gray-600">کد شهریه: {{ auth.id_code }}</p>
                </div>
            </div>
            <!--            <h3 class="text-lg font-semibold mb-4">لیست نوبت ها</h3>-->
            <br>
            <div class="mb-4 text-red-500 font-bold text-center">
                <p>در صورتی که مجوز دریافت روزه برای شما ثبت شده باشد میتوانید جهت دریافت نوبت اقدام فرمائید. درغیر این
                    صورت نوبت شما لغو خواهد شد!</p>
            </div>
            <div class="mb-4">
                <button class="bg-blue-500 text-white rounded p-2 mt-2 w-full" @click="confirm1()">ثبت درخواست جدید
                </button>
            </div>
            <ul class="space-y-2">
                <template v-for="reservation in reservations">
                    <li :class="{'flex': true, 'items-center': true, 'justify-between': true, 'bg-gray-200':  reservation.is_active, 'bg-green-200':! reservation.is_active, 'p-2':true, 'rounded': true}">
                        <span v-if="! reservation.is_active">
                            نوبت شما در تاریخ
                        {{ reservation.date }}
                        با موفیقت ثبت گردید</span>
                        <span v-else>
                            نوبت شما در تاریخ
                            {{ reservation.date }}
                            ثبت و در تاریخ
                            {{ reservation.updated_at }}
                            عبادت دریافت گردید
                        </span>
                    </li>
                    <li v-if="! reservation.is_active">
                        وضعیت نوبت های گذشته
                    </li>
                </template>
            </ul>

            <div role="alert"
                 class="fixed top-4 right-4 z-50 mb-4 flex w-64 p-2 text-sm text-white bg-orange-600 rounded-md shadow-md"
                 v-if="errorMessage !== ''">
                {{ errorMessage }}
                <button
                    class="flex items-center justify-center transition-all w-6 h-6 rounded-md text-white hover:bg-white/10 active:bg-white/10 absolute top-1 right-1"
                    type="button">
                </button>
            </div>
            <div role="alert"
                 class="fixed top-4 right-4 z-50 mb-4 flex w-64 p-2 text-sm text-white bg-green-600 rounded-md shadow-md"
                 v-if="successMessage !== ''">
                {{ successMessage }}
                <button
                    class="flex items-center justify-center transition-all w-6 h-6 rounded-md text-white hover:bg-white/10 active:bg-white/10 absolute top-1 right-1"
                    type="button">
                </button>
            </div>
        </div>
    </div>
    <!--    <router-view />-->
</template>

<style scoped>
input[type=checkbox]:checked + label span:first-of-type {
    background-color: #10B981;
    border-color: #10B981;
    color: #fff;
}

input[type=checkbox]:checked + label span:nth-of-type(2) {
    text-decoration: line-through;
    color: #9CA3AF;
}

.list-decimal li {
    font-size: 15px;
    line-height: 35px;
}
</style>
