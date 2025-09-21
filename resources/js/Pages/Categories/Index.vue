<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, Link, router } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

defineProps({ categories: Array });

const form = useForm({
    name: "",
});

const submitCategory = () => {
    form.post(route("categories.store"), {
        onSucces: () => form.reset("name"),
    });
};
</script>

<template>
    <Head title="kategori" />
    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg mb-6">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                Tambah Kategori
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                Buat kategori untuk mengelompokkan transaksi
                                Anda.
                            </p>
                        </header>

                        <form
                            @submit.prevent="submitCategory"
                            class="mt-6 max-2-xl"
                        >
                            <div>
                                <InputLabel for="name" value="Nama Kategori" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <PrimaryButton
                                    class="mt-4"
                                    :disabled="form.processing"
                                    >Simpan</PrimaryButton
                                >
                            </div>
                        </form>
                    </section>
                </div>
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <section>
                        <h2 class="text-lg font-medium text-gray-900">
                            Daftar Kategori Anda
                        </h2>
                        <ul class="mt-4 space-y-2">
                            <li
                                v-for="category in categories"
                                :key="category.id"
                                class="flex justify-between items-center p-2 border rounded-md"
                            >
                                <span>{{ category.name }}</span>
                                <Link
                                    :href="
                                        route('categories.destroy', category.id)
                                    "
                                    method="delete"
                                    as="button"
                                    class="text-sm text-red-600 hover:text-red-900"
                                >
                                    Hapus
                                </Link>
                            </li>
                            <li
                                v-if="categories.length === 0"
                                class="text-center text-gray-500"
                            >
                                Belum ada kategori.
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
