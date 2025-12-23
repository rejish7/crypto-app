<template>
  <div class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-11 h-11 bg-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-white">Transaction History</h2>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>

    <div v-else-if="transactions.length > 0" class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
      <div
        v-for="transaction in transactions"
        :key="transaction.id"
        class="bg-gray-700 p-4 rounded-xl border-2 border-gray-600 hover:border-emerald-500 transition-all"
      >
        <div class="flex justify-between items-start">
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
              <div
                :class="{
                  'bg-green-600 border-green-500': transaction.type === 'deposit',
                  'bg-red-600 border-red-500': transaction.type === 'withdrawal',
                  'bg-blue-600 border-blue-500': transaction.type === 'trade',
                }"
                class="w-9 h-9 rounded-lg flex items-center justify-center border-2"
              >
                <svg
                  class="w-5 h-5 text-white"
                  fill="none"
                  stroke="currentColor"
                  viewBox="0 0 24 24"
                >
                  <path
                    v-if="transaction.type === 'deposit'"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M12 4v16m8-8H4"
                  />
                  <path
                    v-else-if="transaction.type === 'withdrawal'"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M20 12H4"
                  />
                  <path
                    v-else
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2.5"
                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                  />
                </svg>
              </div>
              <div>
                <span
                  :class="{
                    'text-green-400': transaction.type === 'deposit',
                    'text-red-400': transaction.type === 'withdrawal',
                    'text-blue-400': transaction.type === 'trade',
                  }"
                  class="font-bold capitalize"
                >
                  {{ transaction.type }}
                </span>
                <span
                  v-if="transaction.status !== 'completed'"
                  :class="{
                    'bg-yellow-600': transaction.status === 'pending',
                    'bg-red-600': transaction.status === 'failed',
                  }"
                  class="ml-2 px-2 py-0.5 text-xs rounded-lg text-white font-semibold"
                >
                  {{ transaction.status }}
                </span>
              </div>
            </div>
            <p class="text-sm text-gray-300 mb-1">{{ transaction.description }}</p>
            <div class="flex items-center gap-2 text-xs text-gray-400">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ formatDate(transaction.created_at) }}</span>
            </div>
          </div>
          <div class="text-right ml-4">
            <p
              :class="{
                'text-green-400': transaction.type === 'deposit',
                'text-red-400': transaction.type === 'withdrawal',
              }"
              class="text-xl font-bold"
            >
              {{ transaction.type === 'deposit' ? '+' : '-' }}${{ Number(transaction.amount).toFixed(2) }}
            </p>
            <p v-if="transaction.payment_method" class="text-xs text-gray-400 mt-1 capitalize bg-gray-800 px-2 py-1 rounded border border-gray-600">
              {{ transaction.payment_method.replace('_', ' ') }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-12 border-2 border-dashed border-gray-700 rounded-xl bg-gray-700">
      <svg class="w-16 h-16 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
      </svg>
      <p class="text-gray-400 text-lg">No transactions yet</p>
    </div>
  </div>
</template>

<style scoped>
.custom-scrollbar::-webkit-scrollbar {
  width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: rgba(55, 65, 81, 0.3);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: rgba(96, 165, 250, 0.5);
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: rgba(96, 165, 250, 0.7);
}
</style>

<script setup>
import { ref, onMounted, defineExpose } from 'vue';
import api from '../services/api';

const transactions = ref([]);
const loading = ref(true);

const fetchTransactions = async () => {
  loading.value = true;
  try {
    const response = await api.get('/transactions');
    transactions.value = response.data;
  } catch (err) {
    console.error('Failed to fetch transactions:', err);
  } finally {
    loading.value = false;
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

onMounted(() => {
  fetchTransactions();
});

defineExpose({
  fetchTransactions
});
</script>
