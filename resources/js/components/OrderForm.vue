<template>
  <div class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-11 h-11 bg-purple-600 rounded-xl flex items-center justify-center shadow-lg">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-white">Place Limit Order</h2>
    </div>

    <form @submit.prevent="handleSubmit">
      <!-- Symbol Selection -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-300 mb-2">Symbol</label>
        <select
          v-model="form.symbol"
          class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
        >
          <option value="BTC">BTC/USD</option>
          <option value="ETH">ETH/USD</option>
        </select>
      </div>

      <!-- Side Selection -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-300 mb-2">Side</label>
        <div class="grid grid-cols-2 gap-3">
          <button
            type="button"
            @click="form.side = 'buy'"
            :class="[
              'py-3 px-4 rounded-xl font-bold transition-all border-2',
              form.side === 'buy'
                ? 'bg-green-600 text-white border-green-500 shadow-lg'
                : 'bg-gray-700 text-gray-300 border-gray-600 hover:bg-gray-600'
            ]"
          >
            Buy
          </button>
          <button
            type="button"
            @click="form.side = 'sell'"
            :class="[
              'py-3 px-4 rounded-xl font-bold transition-all border-2',
              form.side === 'sell'
                ? 'bg-red-600 text-white border-red-500 shadow-lg'
                : 'bg-gray-700 text-gray-300 border-gray-600 hover:bg-gray-600'
            ]"
          >
            Sell
          </button>
        </div>
      </div>

      <!-- Price Input -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-300 mb-2">Price (USD)</label>
        <input
          v-model.number="form.price"
          type="number"
          step="0.01"
          placeholder="0.00"
          class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
          required
        />
      </div>

      <!-- Amount Input -->
      <div class="mb-4">
        <label class="block text-sm font-semibold text-gray-300 mb-2">Amount ({{ form.symbol }})</label>
        <input
          v-model.number="form.amount"
          type="number"
          step="0.00000001"
          placeholder="0.00000000"
          class="w-full px-4 py-3 rounded-xl bg-gray-700 text-white border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
          required
        />
      </div>

      <!-- Total Display -->
      <div class="mb-4 p-4 bg-blue-900/30 border-2 border-blue-700 rounded-xl">
        <div class="flex justify-between items-center mb-2">
          <span class="text-gray-300 font-medium">Total:</span>
          <span class="font-bold text-xl text-white">${{ totalAmount.toFixed(2) }}</span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-400">Commission (1.5%):</span>
          <span class="text-yellow-400 font-medium">${{ commission.toFixed(2) }}</span>
        </div>
      </div>

      <!-- Error Message -->
      <div v-if="error" class="mb-4 p-3 bg-red-900/50 border-2 border-red-500 rounded-xl text-red-200 text-sm flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ error }}</span>
      </div>

      <!-- Success Message -->
      <div v-if="success" class="mb-4 p-3 bg-green-900/50 border-2 border-green-500 rounded-xl text-green-200 text-sm flex items-start gap-2">
        <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <span>{{ success }}</span>
      </div>

      <!-- Submit Button -->
      <button
        type="submit"
        :disabled="loading"
        :class="[
          'w-full font-bold py-4 px-4 rounded-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-lg border-2',
          form.side === 'buy'
            ? 'bg-green-600 hover:bg-green-700 text-white border-green-500'
            : 'bg-red-600 hover:bg-red-700 text-white border-red-500'
        ]"
      >
        {{ loading ? 'Placing Order...' : `Place ${form.side.toUpperCase()} Order` }}
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import api from '../services/api';

const emit = defineEmits(['order-placed']);

const form = ref({
  symbol: 'BTC',
  side: 'buy',
  price: 0,
  amount: 0,
});

const loading = ref(false);
const error = ref('');
const success = ref('');

const totalAmount = computed(() => {
  return form.value.price * form.value.amount;
});

const commission = computed(() => {
  return totalAmount.value * 0.015;
});

const handleSubmit = async () => {
  error.value = '';
  success.value = '';
  loading.value = true;

  try {
    await api.post('/orders', {
      symbol: form.value.symbol,
      side: form.value.side,
      price: form.value.price,
      amount: form.value.amount,
    });

    success.value = 'Order placed successfully!';

    // Reset form
    form.value.price = 0;
    form.value.amount = 0;

    emit('order-placed');

    setTimeout(() => {
      success.value = '';
    }, 3000);
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to place order. Please try again.';
  } finally {
    loading.value = false;
  }
};
</script>
