<template>
  <div class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
    <div class="flex items-center gap-3 mb-6">
      <div class="w-11 h-11 bg-orange-600 rounded-xl flex items-center justify-center shadow-lg">
        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
        </svg>
      </div>
      <h2 class="text-2xl font-bold text-white">My Orders</h2>
    </div>

    <!-- Filter Tabs -->
    <div class="flex gap-2 mb-4 bg-gray-700 p-1 rounded-xl border border-gray-600">
      <button
        v-for="status in statuses"
        :key="status.value"
        @click="selectedStatus = status.value"
        :class="[
          'flex-1 px-4 py-2.5 font-bold transition-all rounded-lg',
          selectedStatus === status.value
            ? 'bg-blue-600 text-white shadow-lg'
            : 'text-gray-400 hover:text-gray-300 hover:bg-gray-600'
        ]"
      >
        {{ status.label }}
      </button>
    </div>

    <!-- Orders List -->
    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>

    <div v-else class="space-y-3 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
      <div
        v-for="order in filteredOrders"
        :key="order.id"
        class="bg-gray-700 p-4 rounded-xl border-2 border-gray-600 hover:border-blue-500 transition-all"
      >
        <div class="flex justify-between items-start mb-3">
          <div class="flex items-center gap-2">
            <div
              :class="[
                'w-9 h-9 rounded-lg flex items-center justify-center border-2',
                order.side === 'buy' ? 'bg-green-600 border-green-500' : 'bg-red-600 border-red-500'
              ]"
            >
              <svg
                class="w-5 h-5 text-white"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  v-if="order.side === 'buy'"
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"
                />
                <path
                  v-else
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2.5"
                  d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"
                />
              </svg>
            </div>
            <div>
              <span
                :class="[
                  'font-bold text-lg',
                  order.side === 'buy' ? 'text-green-400' : 'text-red-400'
                ]"
              >
                {{ order.side.toUpperCase() }}
              </span>
              <span class="text-white ml-2 font-semibold">{{ order.symbol }}/USD</span>
            </div>
          </div>

          <span
            :class="[
              'px-3 py-1 rounded-lg text-xs font-bold',
              getStatusClass(order.status)
            ]"
          >
            {{ getStatusLabel(order.status) }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-3 text-sm mb-3">
          <div class="bg-gray-800 px-3 py-2 rounded-lg border border-gray-600">
            <div class="text-gray-400 text-xs mb-1">Price</div>
            <div class="text-white font-mono font-semibold">${{ Number(order.price).toFixed(2) }}</div>
          </div>
          <div class="bg-gray-800 px-3 py-2 rounded-lg border border-gray-600">
            <div class="text-gray-400 text-xs mb-1">Amount</div>
            <div class="text-white font-mono font-semibold">{{ Number(order.amount).toFixed(8) }}</div>
          </div>
          <div class="bg-gray-800 px-3 py-2 rounded-lg border border-gray-600">
            <div class="text-gray-400 text-xs mb-1">Total</div>
            <div class="text-white font-mono font-semibold">${{ (order.price * order.amount).toFixed(2) }}</div>
          </div>
          <div class="bg-gray-800 px-3 py-2 rounded-lg border border-gray-600">
            <div class="text-gray-400 text-xs mb-1">Date</div>
            <div class="text-white text-xs">{{ formatDate(order.created_at) }}</div>
          </div>
        </div>

        <!-- Cancel Button -->
        <div v-if="order.status === 1" class="mt-3">
          <button
            @click="handleCancel(order.id)"
            class="w-full bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-xl transition-all text-sm font-bold shadow-lg border-2 border-red-500"
          >
            Cancel Order
          </button>
        </div>
      </div>

      <div v-if="filteredOrders.length === 0" class="text-center text-gray-500 py-8">
        No {{ selectedStatus === null ? '' : getStatusLabel(selectedStatus).toLowerCase() }} orders
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  orders: {
    type: Array,
    default: () => [],
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['cancel-order']);

const selectedStatus = ref(null);

const statuses = [
  { value: null, label: 'All' },
  { value: 1, label: 'Open' },
  { value: 2, label: 'Filled' },
  { value: 3, label: 'Cancelled' },
];

const filteredOrders = computed(() => {
  let filtered = props.orders;

  if (selectedStatus.value !== null) {
    filtered = filtered.filter(order => order.status === selectedStatus.value);
  }

  // Sort by created_at in descending order (latest first)
  return filtered.sort((a, b) => {
    return new Date(b.created_at) - new Date(a.created_at);
  });
});

const getStatusLabel = (status) => {
  switch (status) {
    case 1: return 'Open';
    case 2: return 'Filled';
    case 3: return 'Cancelled';
    default: return 'Unknown';
  }
};

const getStatusClass = (status) => {
  switch (status) {
    case 1: return 'bg-blue-900/50 text-blue-300';
    case 2: return 'bg-green-900/50 text-green-300';
    case 3: return 'bg-gray-600 text-gray-300';
    default: return 'bg-gray-600 text-gray-300';
  }
};

const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

const handleCancel = (orderId) => {
  if (confirm('Are you sure you want to cancel this order?')) {
    emit('cancel-order', orderId);
  }
};
</script>
