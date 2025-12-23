<template>
  <div class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 bg-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">Order Book</h2>
      </div>

      <div class="flex items-center gap-3">
        <!-- Symbol Dropdown -->
        <select
          v-model="selectedSymbol"
          class="px-4 py-2.5 rounded-xl bg-gray-700 text-white border-2 border-gray-600 focus:border-blue-500 focus:outline-none text-sm font-semibold transition"
        >
          <option value="BTC">BTC/USD</option>
          <option value="ETH">ETH/USD</option>
        </select>

        <!-- Refresh Button -->
        <button
          @click="fetchOrderBook"
          class="p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
          </svg>
        </button>
      </div>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>

    <div v-else class="space-y-4">
      <!-- Sell Orders (Asks) -->
      <div>
        <div class="flex items-center gap-2 mb-3">
          <div class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></div>
          <h3 class="text-sm font-bold text-red-400 uppercase tracking-wider">Sell Orders</h3>
        </div>
        <div class="space-y-1.5 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
          <div
            v-for="order in sellOrders"
            :key="order.id"
            class="flex justify-between items-center bg-red-900/20 p-3 rounded-lg hover:bg-red-900/30 transition-all border-2 border-red-900/30"
          >
            <span class="text-red-400 font-mono font-semibold">${{ Number(order.price).toFixed(2) }}</span>
            <span class="text-gray-300 font-mono text-sm">{{ Number(order.amount).toFixed(8) }}</span>
            <span class="text-gray-400 text-xs bg-gray-800 px-2 py-1 rounded border border-gray-700">${{ (order.price * order.amount).toFixed(2) }}</span>
          </div>

          <div v-if="sellOrders.length === 0" class="text-center text-gray-500 py-8 border-2 border-dashed border-gray-700 rounded-lg">
            No sell orders
          </div>
        </div>
      </div>

      <!-- Spread -->
      <div class="border-y-2 border-gray-600 py-3 bg-gray-700 rounded-lg">
        <div class="text-center">
          <span class="text-gray-400 text-sm">Spread </span>
          <span class="text-white font-bold text-lg">{{ spread }}</span>
        </div>
      </div>

      <!-- Buy Orders (Bids) -->
      <div>
        <div class="flex items-center gap-2 mb-3">
          <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
          <h3 class="text-sm font-bold text-green-400 uppercase tracking-wider">Buy Orders</h3>
        </div>
        <div class="space-y-1.5 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
          <div
            v-for="order in buyOrders"
            :key="order.id"
            class="flex justify-between items-center bg-green-900/20 p-3 rounded-lg hover:bg-green-900/30 transition-all border-2 border-green-900/30"
          >
            <span class="text-green-400 font-mono font-semibold">${{ Number(order.price).toFixed(2) }}</span>
            <span class="text-gray-300 font-mono text-sm">{{ Number(order.amount).toFixed(8) }}</span>
            <span class="text-gray-400 text-xs bg-gray-800 px-2 py-1 rounded border border-gray-700">${{ (order.price * order.amount).toFixed(2) }}</span>
          </div>

          <div v-if="buyOrders.length === 0" class="text-center text-gray-500 py-8 border-2 border-dashed border-gray-700 rounded-lg">
            No buy orders
          </div>
        </div>
      </div>
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
import { ref, computed, onMounted, onUnmounted, watch } from 'vue';
import api from '../services/api';

const selectedSymbol = ref('BTC');
const orders = ref([]);
const loading = ref(false);
let refreshInterval = null;

const sellOrders = computed(() => {
  return orders.value
    .filter(order => order.side === 'sell' && order.status === 1)
    .sort((a, b) => a.price - b.price)
    .slice(0, 10);
});

const buyOrders = computed(() => {
  return orders.value
    .filter(order => order.side === 'buy' && order.status === 1)
    .sort((a, b) => b.price - a.price)
    .slice(0, 10);
});

const spread = computed(() => {
  if (sellOrders.value.length > 0 && buyOrders.value.length > 0) {
    const lowestSell = sellOrders.value[0].price;
    const highestBuy = buyOrders.value[0].price;
    return `$${(lowestSell - highestBuy).toFixed(2)}`;
  }
  return 'N/A';
});

const fetchOrderBook = async (showLoading = true) => {
  if (showLoading) {
    loading.value = true;
  }
  try {
    const response = await api.get('/orders', {
      params: { symbol: selectedSymbol.value }
    });
    orders.value = response.data;
  } catch (err) {
    console.error('Failed to fetch order book:', err);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchOrderBook();

  // Auto-refresh every 10 seconds 
  refreshInterval = setInterval(() => fetchOrderBook(false), 10000);
});

onUnmounted(() => {
  if (refreshInterval) {
    clearInterval(refreshInterval);
  }
});

watch(selectedSymbol, () => {
  fetchOrderBook();
});
</script>
