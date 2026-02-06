<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

const toast = useToast();

// State untuk menentukan apakah di desktop atau mobile
const isDesktop = ref(false);

// Fungsi untuk cek breakpoint
const checkBreakpoint = () => {
    isDesktop.value = window.innerWidth >= 1024; // lg breakpoint
};

onMounted(() => {
    checkBreakpoint();
    window.addEventListener("resize", checkBreakpoint);
});

onBeforeUnmount(() => {
    window.removeEventListener("resize", checkBreakpoint);
});

const form = useForm({
    email: "",
});

// Translate Laravel error keys
const translateError = (err) => {
    if (Array.isArray(err)) err = err[0];
    switch (err) {
        case "passwords.throttled":
            return "Tolong tunggu beberapa saat sebelum mencoba lagi.";
        case "passwords.user":
            return "Email tidak terdaftar di sistem kami.";
        default:
            return err;
    }
};

const submit = () => {
    form.post(route("password.email"), {
        onSuccess: (page) => {
            // Jika di desktop, gunakan toast
            if (isDesktop.value) {
                toast.success("Link password telah dikirim ke email!", {
                    position: "top-right",
                });
            }
            form.reset("email");
        },
        onError: (errors) => {
            if (errors.email) {
                form.errors.email = [translateError(errors.email)];
            }
            // Jika di desktop, tampilkan toast juga
            if (isDesktop.value) {
                Object.values(errors).forEach((err) =>
                    toast.error(translateError(err), { position: "top-right" })
                );
            }
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-4 text-sm text-gray-600">
            Forgot your password? No problem. Just let us know your email
            address and we will email you a password reset link that will allow
            you to choose a new one.
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <div>
                <InputLabel
                    for="email"
                    value="Email"
                    class="mb-2 block text-sm font-medium text-gray-700"
                />

                <TextInput
                    id="email"
                    type="email"
                    class="block w-full rounded-lg border border-gray-300 px-4 py-3 text-gray-900 placeholder-gray-400 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:ring-offset-1 focus:outline-none"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="name@company.com"
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

            <div class="mt-4 flex flex-col items-center space-y-4">
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
                        Sending...
                    </span>
                    <span v-else>Email Password Reset Link</span>
                </PrimaryButton>

                <a
                    href="#"
                    @click.prevent="$inertia.get(route('login'))"
                    class="text-sm font-medium text-emerald-600 hover:text-emerald-500 transition-colors duration-200"
                >
                    Back to Login
                </a>
            </div>
        </form>
    </GuestLayout>
</template>
