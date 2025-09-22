<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { DotLottieVue } from "@lottiefiles/dotlottie-vue";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <Head title="Log in" />
    <div class="flex min-h-screen items-center justify-center bg-gray-100 p-4">
        <div
            class="flex w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl"
        >
            <div class="relative hidden w-1/2 md:block">
                <div class="absolute bottom-0 left-0 w-full p-8">
                    <DotLottieVue
                        style="height: 350px; width: 350px"
                        autoplay
                        loop
                        class="mx-auto"
                        src="https://lottie.host/1eed5bb2-2f2e-4f70-ac2c-b5658c173c1b/Ad3WVY2ggk.lottie"
                    />
                    <h1 class="text-3xl font-bold tracking-tight text-blue-500">
                        Lacak Keuangan Anda, Raih Tujuan Finansial
                    </h1>
                    <p class="mt-2 text-gray-600">
                        Masuk untuk mulai mengelola pemasukan dan pengeluaran
                        Anda.
                    </p>
                </div>
            </div>

            <div
                class="flex w-full flex-col justify-center space-y-8 p-8 md:w-1/2 lg:p-12"
            >
                <div>
                    <img
                        src="images/logo.png"
                        alt=""
                        class="h-[150px] mx-auto"
                    />
                    <h2
                        class="mt-6 text-center text-3xl font-bold tracking-tight text-gray-900"
                    >
                        Selamat Datang Kembali
                    </h2>
                    <p class="mt-2 text-center text-sm text-gray-600">
                        Belum punya akun?
                        <Link
                            :href="route('register')"
                            class="font-medium text-blue-500 hover:text-indigo-500"
                        >
                            Daftar di sini
                        </Link>
                    </p>
                </div>

                <div
                    v-if="status"
                    class="mb-4 text-sm font-medium text-green-600"
                >
                    {{ status }}
                </div>

                <form @submit.prevent="submit" class="mt-8 space-y-6">
                    <div class="relative">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-5 w-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z"
                                />
                                <path
                                    d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z"
                                />
                            </svg>
                        </div>
                        <TextInput
                            id="email"
                            type="email"
                            class="block w-full rounded-md border-gray-300 pl-10"
                            placeholder="Email"
                            v-model="form.email"
                            required
                            autofocus
                            autocomplete="username"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.email" />

                    <div class="relative mt-4">
                        <div
                            class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3"
                        >
                            <svg
                                class="h-5 w-5 text-gray-400"
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </div>
                        <TextInput
                            id="password"
                            type="password"
                            class="block w-full rounded-md border-gray-300 pl-10"
                            placeholder="Password"
                            v-model="form.password"
                            required
                            autocomplete="current-password"
                        />
                    </div>
                    <InputError class="mt-2" :message="form.errors.password" />

                    <div class="flex items-center justify-between mt-4">
                        <label class="flex items-center">
                            <Checkbox
                                name="remember"
                                v-model:checked="form.remember"
                            />
                            <span class="ms-2 text-sm text-gray-600"
                                >Remember me</span
                            >
                        </label>

                        <div class="text-sm">
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="font-medium text-blue-500 hover:text-blue-400"
                            >
                                Forgot your password?
                            </Link>
                        </div>
                    </div>

                    <div class="mt-6">
                        <PrimaryButton
                            class="flex w-full justify-center"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Log in
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
