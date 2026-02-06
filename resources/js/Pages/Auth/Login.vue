<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref, onMounted, onBeforeUnmount } from "vue";
import { useToast } from "vue-toastification";

// Toast
const toast = useToast();

// Props
defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

// Form
const form = useForm({
    email: "",
    password: "",
    remember: false,
});

// Show password toggle
const showPassword = ref(false);

// State untuk menentukan apakah di desktop atau mobile
const isDesktop = ref(false);

// Fungsi untuk cek breakpoint
const checkBreakpoint = () => {
    isDesktop.value = window.innerWidth >= 1024; // lg breakpoint
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener("resize", checkBreakpoint);

    // Jika ada status dan di desktop, tampilkan sebagai toast
    if (status && isDesktop.value) {
        toast.info(status, { position: "top-right" });
    }
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", checkBreakpoint);
});

// Google OAuth
const isGoogleLoading = ref(false);
const loginWithGoogle = () => {
    isGoogleLoading.value = true;
    window.location.href = route("login.google");
};

// Submit form
const submit = () => {
    form.post(route("login"), {
        onSuccess: (page) => {
            // Jika ada flash success dan di desktop, tampilkan toast
            if (page.props.flash.success && isDesktop.value) {
                toast.success(page.props.flash.success, {
                    position: "top-right",
                });
            }
        },
        onError: (errors) => {
            // Jika di desktop, tampilkan semua error sebagai toast
            if (isDesktop.value) {
                Object.values(errors).forEach((err) =>
                    toast.error(err[0] || err, { position: "top-right" })
                );
            }
        },
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Status toast -->
        <div
            v-if="status"
            class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium"
        >
            {{ status }}
        </div>

        <!-- Google Login -->
        <div class="mb-8">
            <button
                @click="loginWithGoogle"
                type="button"
                :disabled="isGoogleLoading"
                class="flex w-full justify-center items-center gap-3 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition-all duration-200 hover:bg-gray-50 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
            >
                <svg class="h-5 w-5" viewBox="0 0 24 24">
                    <path
                        fill="#4285F4"
                        d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"
                    />
                    <path
                        fill="#34A853"
                        d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"
                    />
                    <path
                        fill="#FBBC05"
                        d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"
                    />
                    <path
                        fill="#EA4335"
                        d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"
                    />
                </svg>
                <span v-if="isGoogleLoading">Loading...</span>
                <span v-else>Continue with Google</span>
            </button>
        </div>

        <!-- Divider -->
        <div class="relative mb-8">
            <div class="absolute inset-0 flex items-center">
                <div class="w-full border-t border-gray-300"></div>
            </div>
            <div class="relative flex justify-center text-sm">
                <span class="bg-white px-4 text-gray-500"
                    >Or continue with email</span
                >
            </div>
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-6">
            <!-- Email -->
            <div>
                <InputLabel
                    for="email"
                    value="Email Address"
                    class="mb-2 block text-sm font-medium text-gray-700"
                />
                <TextInput
                    id="email"
                    type="email"
                    class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:ring-offset-1 focus:outline-none"
                    placeholder="name@company.com"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    :class="{
                        'border-red-300 focus:border-red-500 focus:ring-red-200':
                            form.errors.email && !isDesktop,
                    }"
                />
                <!-- InputError hanya tampil di mobile -->
                <InputError
                    v-if="!isDesktop"
                    class="mt-2 text-sm"
                    :message="form.errors.email?.[0]"
                />
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-2">
                    <InputLabel
                        for="password"
                        value="Password"
                        class="block text-sm font-medium text-gray-700"
                    />
                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm font-medium text-emerald-600 transition-colors duration-200 hover:text-emerald-500"
                    >
                        Forgot password?
                    </Link>
                </div>
                <div class="relative">
                    <TextInput
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-3 pr-10 text-gray-900 placeholder-gray-400 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:ring-offset-1 focus:outline-none"
                        placeholder="********"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        :class="{
                            'border-red-300 focus:border-red-500 focus:ring-red-200':
                                form.errors.password && !isDesktop,
                        }"
                    />
                    <!-- Toggle eye -->
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                    >
                        <svg
                            v-if="!showPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"
                            />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"
                            />
                        </svg>
                    </button>
                </div>

                <!-- InputError -->
                <InputError
                    v-if="!isDesktop"
                    class="mt-2 text-sm"
                    :message="form.errors.password"
                />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center">
                <Checkbox
                    id="remember"
                    v-model:checked="form.remember"
                    class="h-4 w-4 rounded border-gray-300 text-emerald-600 focus:ring-emerald-500"
                />
                <label for="remember" class="ml-3 block text-sm text-gray-700"
                    >Remember me</label
                >
            </div>

            <!-- Submit Button -->
            <div>
                <PrimaryButton
                    class="w-full justify-center rounded-lg bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:from-emerald-600 hover:to-emerald-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="form.processing" class="flex items-center">
                        <svg
                            class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            />
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            />
                        </svg>
                        Signing in...
                    </span>
                    <span v-else> Sign in </span>
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
