<script setup>
import { ref, watch, computed, onMounted, onBeforeUnmount } from "vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toastification";

// Toast
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

// Props dari backend
const props = defineProps({
    email: { type: String, required: true },
    token: { type: String, required: true },
    status: { type: String },
});

// Form
const form = useForm({
    token: props.token,
    email: props.email,
    password: "",
    password_confirmation: "",
});

// Toggle password visibility
const showPassword = ref(false);
const showPasswordConfirm = ref(false);

// Password strength
const passwordStrength = computed(() => {
    const p = form.password;
    if (p.length === 0) return 0;
    let score = 0;
    if (p.length >= 8) score += 30;
    if (/[A-Z]/.test(p)) score += 20;
    if (/[0-9]/.test(p)) score += 20;
    if (/[^A-Za-z0-9]/.test(p)) score += 30;
    return Math.min(score, 100);
});

const passwordStrengthColor = computed(() => {
    if (passwordStrength.value < 40) return "bg-red-500";
    if (passwordStrength.value < 70) return "bg-yellow-400";
    return "bg-green-500";
});

const passwordStrengthLabel = computed(() => {
    if (passwordStrength.value < 40) return "Weak";
    if (passwordStrength.value < 70) return "Medium";
    return "Strong";
});

// Submit form
const submit = () => {
    form.post(route("password.store"), {
        onSuccess: (page) => {
            // Jika di desktop, tampilkan toast untuk success
            if (isDesktop.value && page.props.flash.success) {
                toast.success(page.props.flash.success, {
                    position: "top-right",
                });
            }
        },
        onError: (errors) => {
            // Jika di desktop, tampilkan error sebagai toast
            if (isDesktop.value) {
                Object.values(errors).forEach((err) =>
                    toast.error(err[0] || err, { position: "top-right" })
                );
            }
        },
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};

// Watch optional status
watch(
    () => props.status,
    (value) => {
        // Jika ada status dan di desktop, tampilkan sebagai toast
        if (value && isDesktop.value) {
            toast.success(value, { position: "top-right" });
        }
    },
    { immediate: true }
);
</script>

<template>
    <GuestLayout>
        <Head title="Reset Password" />

        <!-- Status message - hanya tampil di mobile -->
        <div
            v-if="props.status && !isDesktop"
            class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm font-medium"
        >
            {{ props.status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6">
            <!-- Email -->
            <div>
                <InputLabel
                    for="email"
                    value="Email"
                    class="mb-2 block text-sm font-medium text-gray-700"
                />
                <div class="mt-1">
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
                </div>
                <!-- InputError hanya tampil di mobile -->
                <InputError
                    v-if="!isDesktop"
                    class="mt-2 text-sm"
                    :message="form.errors.email?.[0]"
                />
            </div>

            <!-- Password -->
            <div>
                <InputLabel
                    for="password"
                    value="Password"
                    class="mb-2 block text-sm font-medium text-gray-700"
                />
                <div class="relative mt-1">
                    <TextInput
                        :type="showPassword ? 'text' : 'password'"
                        id="password"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 text-gray-900 placeholder-gray-400 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:ring-offset-1 focus:outline-none"
                        placeholder="********"
                        v-model="form.password"
                        required
                        autocomplete="new-password"
                        :class="{
                            'border-red-300 focus:border-red-500 focus:ring-red-200':
                                form.errors.password && !isDesktop,
                        }"
                    />
                    <button
                        type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        :class="{ 'text-gray-600': showPassword }"
                        aria-label="Toggle password visibility"
                    >
                        <svg
                            v-if="!showPassword"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5"
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
                            class="w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"
                            />
                        </svg>
                    </button>
                </div>
                <!-- InputError hanya tampil di mobile -->
                <InputError
                    v-if="!isDesktop"
                    class="mt-2 text-sm"
                    :message="form.errors.password?.[0]"
                />

                <!-- Password strength indicator -->
                <div
                    class="mt-3 h-2 w-full bg-gray-200 rounded-full overflow-hidden"
                >
                    <div
                        class="h-2 rounded-full transition-all duration-300"
                        :class="passwordStrengthColor"
                        :style="{ width: passwordStrength + '%' }"
                    ></div>
                </div>
                <p class="mt-2 text-sm text-gray-500">
                    Password strength:
                    <span class="font-medium">{{ passwordStrengthLabel }}</span>
                </p>
            </div>

            <!-- Confirm Password -->
            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="mb-2 block text-sm font-medium text-gray-700"
                />
                <div class="relative mt-1">
                    <TextInput
                        :type="showPasswordConfirm ? 'text' : 'password'"
                        id="password_confirmation"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-3 pr-12 text-gray-900 placeholder-gray-400 transition-all duration-200 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-200 focus:ring-offset-1 focus:outline-none"
                        placeholder="********"
                        v-model="form.password_confirmation"
                        required
                        autocomplete="new-password"
                        :class="{
                            'border-red-300 focus:border-red-500 focus:ring-red-200':
                                form.errors.password_confirmation && !isDesktop,
                        }"
                    />
                    <button
                        type="button"
                        @click="showPasswordConfirm = !showPasswordConfirm"
                        class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-gray-600 transition-colors duration-200"
                        :class="{ 'text-gray-600': showPasswordConfirm }"
                        aria-label="Toggle confirm password visibility"
                    >
                        <svg
                            v-if="!showPasswordConfirm"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="w-5 h-5"
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
                            class="w-5 h-5"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88"
                            />
                        </svg>
                    </button>
                </div>
                <!-- InputError hanya tampil di mobile -->
                <InputError
                    v-if="!isDesktop"
                    class="mt-2 text-sm"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex flex-col items-center space-y-4">
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
                        Resetting...
                    </span>
                    <span v-else>Reset Password</span>
                </PrimaryButton>

                <!-- Back to Login -->
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
