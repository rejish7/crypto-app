<template>
  <div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <nav class="bg-gray-800 border-b-2 border-blue-600 shadow-xl">
      <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" />
              </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Crypto App</h1>
          </div>

          <div class="flex items-center gap-4">
            <div class="px-4 py-2 bg-gray-700 rounded-lg border border-gray-600">
              <span class="text-white font-medium">{{ user?.name }}</span>
            </div>
            <button
              @click="handleLogout"
              class="bg-red-600 hover:bg-red-700 text-white px-6 py-2.5 rounded-lg font-medium shadow-lg transition-all"
            >
              Logout
            </button>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Wallet & Order Form -->
        <div class="space-y-6">
          <WalletBalance :profile="profile" :loading="loadingProfile" @refresh="handleRefresh" />
          <OrderForm @order-placed="handleOrderPlaced" />
        </div>

        <!-- Middle Column: OrderBook -->
        <div>
          <OrderBook />
        </div>

        <!-- Right Column: My Orders & Transactions -->
        <div class="space-y-6">
          <OrdersList :orders="userOrders" :loading="loadingOrders" @cancel-order="handleCancelOrder" />
          <TransactionHistory ref="transactionHistoryRef" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';
import echo from '../services/echo';
import WalletBalance from '../components/WalletBalance.vue';
import OrderForm from '../components/OrderForm.vue';
import OrderBook from '../components/OrderBook.vue';
import OrdersList from '../components/OrdersList.vue';
import TransactionHistory from '../components/TransactionHistory.vue';

const router = useRouter();
const user = ref(null);
const profile = ref(null);
const userOrders = ref([]);
const selectedSymbol = ref('BTC');
const transactionHistoryRef = ref(null);
const loadingProfile = ref(false);
const loadingOrders = ref(true);

onMounted(async () => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  }

  await fetchProfile();
  await fetchUserOrders();
  setupPusher();
});

const fetchProfile = async () => {
  loadingProfile.value = true;
  try {
    const response = await api.get('/profile');
    profile.value = response.data;
  } catch (err) {
    console.error('Failed to fetch profile:', err);
  } finally {
    loadingProfile.value = false;
  }
};

const fetchUserOrders = async () => {
  loadingOrders.value = true;
  try {
    // Fetch all user orders without symbol filter
    const response = await api.get('/orders');
    // Filter only the logged-in user's orders
    userOrders.value = response.data.filter(order => order.user_id === user.value?.id);
  } catch (err) {
    console.error('Failed to fetch user orders:', err);
  } finally {
    loadingOrders.value = false;
  }
};

const setupPusher = () => {
  if (user.value) {
    echo.private(`user.${user.value.id}`)
      .listen('OrderMatched', (e) => {
        console.log('Order matched:', e);
        fetchProfile();
        fetchUserOrders();
      });
  }
};

const handleOrderPlaced = () => {
  fetchProfile();
  fetchUserOrders();
};

const handleRefresh = () => {
  fetchProfile();
  if (transactionHistoryRef.value) {
    transactionHistoryRef.value.fetchTransactions();
  }
};

const handleCancelOrder = async (orderId) => {
  try {
    await api.post(`/orders/${orderId}/cancel`);
    fetchProfile();
    fetchUserOrders();
  } catch (err) {
    console.error('Failed to cancel order:', err);
  }
};

const handleLogout = () => {
  if (confirm('Are you sure you want to logout?')) {
    localStorage.removeItem('token');
    localStorage.removeItem('user');
    router.push('/login');
  }
};

onUnmounted(() => {
  if (user.value) {
    echo.leave(`user.${user.value.id}`);
  }
});
</script>
