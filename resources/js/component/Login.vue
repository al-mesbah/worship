<script setup>
import {ref} from "vue";
import Toast from "primevue/toast";
import {useToast} from "primevue/usetoast";
import sync from 'primeicons/raw-svg/sync.svg'
import key from 'primeicons/raw-svg/key.svg'

const toast = useToast();
const captcha = ref(window.captcha);
const form = ref({
    code: '',
    number: '',
    captcha: '',
    sms: ''
});
const generate_captcha = () => {
    axios.get('/login/captcha').then(r => {
        captcha.value = r.data.url;
        form.value.captcha = ''
    })
}
const errors = ref({});
const first_login = ref(true)
const readonly = ref(true)

const login_code = () => {
    axios.put('/login', form.value).then(r => {
        if (r.data.status) {
            toast.add({
                severity: 'info',
                summary: 'موفق',
                detail: 'تا ثانیه های دیگر وارد پنل کاربری خواهید شد!',
                life: 3000
            });
            window.open('/', '_self')
        } else
            toast.add({severity: 'error', summary: 'ناموفق', detail: r.data.message, life: 10000});
    }).catch(r => {
        errors.value = r.response.data.errors;
    })
}
const login = () => {
    readonly.value = false;
    axios.post('/login', form.value).then(r => {
        generate_captcha();
        if (r.data.status) {
            toast.add({severity: 'info', summary: 'موفق', detail: 'کد تائید را وارد نمائید!', life: 70000});
            first_login.value = false;
        } else {
            readonly.value = true;
            toast.add({severity: 'error', summary: 'ناموفق', detail: r.data.message, life: 10000});
        }
    }).catch(r => {
        generate_captcha();
        first_login.value = true;
        readonly.value = true;
        errors.value = r.response.data.errors;
    })
}
</script>

<template>
    <Toast/>
    <div
        style="background-color: rgba(238, 238, 238, 0.7); border: 1px solid rgb(221, 221, 221); padding: 10px; margin: 110px auto auto"
        id="main_app">
        <section class="header-title">
            <h4 style="margin: 15px 0 20px; text-align: center; color: gray;">سامانه ثبت نوبت عبادات </h4>
        </section>
        <div
            style="background-color: rgba(247, 247, 247, 0.7); padding: 5px; border: 1px dashed rgb(221, 221, 221);    direction: rtl;">
            <div id="nav-tabContent" class="tab-content">
                <div id="nav_login" role="tabpanel" aria-labelledby="nav_login_tab"
                     class="tab-pane fade show active">
                    <div class="space-20"></div>
                    <div class="panel-body">
                        <div id="login_form_area">
                            <div class="text-center" style="color: red; margin-bottom: 3px;"><strong
                                id="login_error_message"></strong></div>
                            <div class="text-center" style="color: red; margin-bottom: 3px;"><strong
                                id="login_check_code_error_message"></strong></div>
                            <form id="login_form_check_username_id" method="post" autocomplete="off"
                                  class="form-horizontal" @submit.prevent="login" v-if="first_login">
                                <div class="form-group row fg_username"><label
                                    for="username"
                                    class="col-sm-4 col-form-label"><span
                                    class="more_info"></span> <span
                                    class="red_star">* <i
                                    class="icon-user"></i></span> <span
                                    class="label_title">کد عبادت:</span></label>
                                    <div class="col-sm-8"><input type="text"
                                                                 id="username" name="username"
                                                                 autocomplete="on"
                                                                 v-model="form.code"
                                                                 :readonly="! readonly"
                                                                 autofocus="autofocus"
                                                                 class="field_username form-control ltr input"
                                                                 aria-required="true"
                                                                 aria-invalid="true">
                                        <div class="col-sm-12 messages"
                                             style="margin-top: 5px; color: red; font-size: 12px;"

                                        ><span
                                            v-if="errors.code">کد عبادت ضروری است.</span></div>
                                    </div>
                                </div>
                                <div class="form-group row fg_password"><label
                                    for="password"
                                    class="col-sm-4 col-form-label"><span
                                    class="more_info"></span> <span
                                    class="red_star">* <i
                                    class="icon-lock2"></i></span> <span
                                    class="label_title">شماره تماس:</span></label>
                                    <div class="col-sm-8"><input type="tel"
                                                                 id="password" name="password"
                                                                 autofocus="autofocus"
                                                                 class="field_password form-control ltr input"
                                                                 aria-required="true"
                                                                 :readonly="! readonly"
                                                                 v-model="form.number"
                                                                 aria-invalid="true">
                                        <div class="col-sm-12 messages"
                                             style="margin-top: 5px; color: red; font-size: 12px;">
                                            <span v-if="errors.number">شماره تماس ضروری است.</span></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="form-group row fg_captcha_code"><label
                                        for="captcha" class="col-sm-4 col-form-label"><span
                                        class="more_info"></span> <span
                                        class="red_star">* </span> <span
                                        class="label_title">کد امنیتی:</span></label>
                                        <div class="col-sm-8"><input type="text"
                                                                     maxlength="6" id="captcha"
                                                                     name="captcha"
                                                                     autocomplete="off"
                                                                     :readonly="! readonly"
                                                                     v-model="form.captcha"
                                                                     class="field_login_captcha_code form-control ltr input"
                                                                     aria-required="false"
                                                                     aria-invalid="false">
                                            <div class="space-2"></div>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <button type="button"
                                                            class="btn btn-flat captcha_refresh"
                                                            @click="generate_captcha()"
                                                            style="border: 1px solid rgb(160, 160, 160);">
                                                        <img :src="sync" alt="" style="width: 25px;">
                                                    </button>
                                                </div>
                                                <div class="form-control"
                                                     style="padding: 1px 0 1px 1px; background: rgb(160, 160, 160); position: relative;">
                                                    <img id="captcha_image_login_captcha_code" alt=""
                                                         :src="captcha"
                                                         class="captcha_image"
                                                         style="height: 40px; width: 100%; border-radius: 3px 0 0 3px;">
                                                    <!----></div>
                                            </div>
                                            <div class="col-sm-12 messages"
                                                 style="margin-top: 5px; color: red; font-size: 12px;">
                                                <span v-if="errors.captcha">کد امنیتی نادرست است.</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-group col-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-info text-white">
                                                        <img :src="key" alt="" style="width: 25px;">
                                            </span></div>
                                        <button type="submit"
                                                class="form-control btn bg-info text-white btn-block"
                                        >دریافت کد تائید
                                        </button>
                                    </div>
                                    <div class="col-sm-12 messages"
                                         style="text-align: center; margin-top: 10px; color: red; font-size: 12px;">
                                        <!----></div> <!----></div>
                            </form>
                            <form id="check_code" method="post" autocomplete="off"
                                  class="form-horizontal flex flex-col-reverse" @submit.prevent="login_code"
                                  v-if="! first_login">
                                <p class="text-center">کد تائید پیامک شده را وارد نمائید</p>
                                <div class="form-group row fg_username  justify-center text-center flex"
                                     style="justify-content: center">

                                    <div class="col-sm-8">
                                        <input type="text"
                                               id="username" name="username"
                                               autocomplete="on"
                                               v-model="form.sms"
                                               autofocus="autofocus"
                                               class="field_username form-control ltr input"
                                               aria-required="true"
                                               aria-invalid="true">
                                        <div class="col-sm-12 messages"
                                             style="margin-top: 5px; color: red; font-size: 12px;"

                                        ><span
                                            v-if="errors.sms">کد تائید ضروری است.</span></div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="input-group col-12">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-info text-white">
                                            <i class="fas fa-key"></i></span></div>
                                        <button type="submit"
                                                class="form-control btn bg-info text-white btn-block"
                                        >ورود به سامانه
                                        </button>
                                    </div>
                                    <div class="col-sm-12 messages"
                                         style="text-align: center; margin-top: 10px; color: red; font-size: 12px;">
                                        <!----></div> <!----></div>
                            </form>
                            <div class="space-10"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div
            style="margin-top: 10px; text-align: center; color: rgb(93, 93, 93); font-weight: bolder; text-shadow: rgb(185, 184, 184) -1px 1px 8px; font-size: 14px;">
            <p dir="rtl">در صورت بروز هرگونه مشکل در ایتا با نام کاربری
                <a href="https://eitaa.com/e_imamhadi">@e_imamhadi</a>
                پیگیری نمایید</p>
            <p>طراحی و توسعه توسط علیرضا شهبازپور</p>
        </div>
    </div>
</template>

<style scoped>
.form-group {
    display: flex;
}

#progressive-background {
    width: 100%;
    height: 100vh;
}

#main_app {
    width: calc(100% - 20%);
    max-width: 480px;
}

.header-title, .header-title * {
    line-height: 10px;
}
</style>
