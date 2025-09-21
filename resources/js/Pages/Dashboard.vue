<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

import Modal from "@/Components/Modal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";

const Props = defineProps({
    transactions: Array,
    categories: Array,
    stats: Object,
});

// loadignya lah pokoknya saat proses mambuat ransaksi
const creatingTransaction = ref(false);

const form = useForm({
    description: "",
    amount: "",
    type: "expense",
    category_id: Props.categories.length > 0 ? Props.categories[0].id : null,
    transaction_date: new Date().toISOString().slice(0, 10),
});

// handel modal

const openModal = () => {
    creatingTransaction.value = true;
};

const closeModal = () => {
    creatingTransaction.value = false;
    form.reset();
};

const handleSubmitTransaction = () => {
    form.post(route("transaction.store"), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

// format mata uang dengan mata uang negara kita tercinta

const formatCurrency = (value) => {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(value);
};
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <h3 class="text-gray-500 text-sm font-medium">
                            Pemasukan Bulan Ini
                        </h3>
                        <p class="text-3xl font-semibold text-green-600 mt-2">
                            {{ formatCurrency(stats.income) }}
                        </p>
                    </div>
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <h3 class="text-gray-500 text-sm font-medium">
                            Pengeluaran Bulan Ini
                        </h3>
                        <p class="text-3xl font-semibold text-red-600 mt-2">
                            {{ formatCurrency(stats.expense) }}
                        </p>
                    </div>
                    <div
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                    >
                        <h3 class="text-gray-500 text-sm font-medium">
                            Saldo Bulan Ini
                        </h3>
                        <p class="text-3xl font-semibold text-blue-600 mt-2">
                            {{ formatCurrency(stats.balance) }}
                        </p>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium">
                                Riwayat Transaksi Terakhir
                            </h3>
                            <PrimaryButton @click="openModal()"
                                >Tambah Transaksi</PrimaryButton
                            >
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Tanggal
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Deskripsi
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Kategori
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider"
                                        >
                                            Jumlah
                                        </th>
                                    </tr>
                                </thead>
                                <tbody
                                    class="bg-white divide-y divide-gray-200"
                                >
                                    <tr
                                        v-for="transaction in transactions"
                                        :key="transaction.id"
                                    >
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            {{ transaction.transaction_date }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"
                                        >
                                            {{ transaction.description }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                        >
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800"
                                            >
                                                {{ transaction.category_name }}
                                            </span>
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium"
                                            :class="
                                                transaction.type === 'income'
                                                    ? 'text-green-600'
                                                    : 'text-red-600'
                                            "
                                        >
                                            {{
                                                transaction.type === "income"
                                                    ? "+"
                                                    : "-"
                                            }}
                                            {{
                                                formatCurrency(
                                                    transaction.amount
                                                )
                                            }}
                                        </td>
                                    </tr>
                                    <tr v-if="transactions.length === 0">
                                        <td
                                            colspan="4"
                                            class="px-6 py-4 text-center text-gray-500"
                                        >
                                            Belum ada transaksi.
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="creatingTransaction" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">
                    Tambah Transaksi Baru
                </h2>
                <form
                    @submit.prevent="handleSubmitTransaction"
                    class="mt-6 space-y-6"
                >
                    <div>
                        <InputLabel for="description" value="Deskripsi" />
                        <TextInput
                            id="description"
                            v-model="form.description"
                            type="text"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.description"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="amount" value="Jumlah" />
                        <TextInput
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.amount"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel for="type" value="Tipe" />
                        <select
                            id="type"
                            v-model="form.type"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option value="expense">Pengeluaran</option>
                            <option value="income">Pemasukan</option>
                        </select>
                        <InputError :message="form.errors.type" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="category_id" value="Kategori" />
                        <select
                            id="category_id"
                            v-model="form.category_id"
                            class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        >
                            <option
                                v-for="category in categories"
                                :key="category.id"
                                :value="category.id"
                            >
                                {{ category.name }}
                            </option>
                        </select>
                        <InputError
                            :message="form.errors.category_id"
                            class="mt-2"
                        />
                    </div>
                    <div>
                        <InputLabel
                            for="transaction_date"
                            value="Tanggal Transaksi"
                        />
                        <TextInput
                            id="transaction_date"
                            v-model="form.transaction_date"
                            type="date"
                            class="mt-1 block w-full"
                            required
                        />
                        <InputError
                            :message="form.errors.transaction_date"
                            class="mt-2"
                        />
                    </div>

                    <div class="flex justify-end">
                        <SecondaryButton @click="closeModal">
                            Batal
                        </SecondaryButton>
                        <PrimaryButton
                            class="ms-3"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Simpan Transaksi
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
