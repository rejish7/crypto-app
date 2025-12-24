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

          <!-- Profile Dropdown -->
          <div class="relative">
            <button
              @click="showProfileDropdown = !showProfileDropdown"
              class="flex items-center gap-3 px-4 py-2 bg-gray-700 hover:bg-gray-600 rounded-lg border border-gray-600 transition-all"
            >
              <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                  <span class="text-white font-bold text-sm">{{ user?.name?.charAt(0).toUpperCase() }}</span>
                </div>
                <span class="text-white font-medium">{{ user?.name }}</span>
              </div>
              <svg
                class="w-4 h-4 text-white transition-transform"
                :class="{ 'rotate-180': showProfileDropdown }"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown Menu -->
            <div
              v-if="showProfileDropdown"
              class="absolute right-0 mt-2 w-64 bg-gray-800 rounded-xl shadow-2xl border-2 border-gray-700 overflow-hidden z-50"
            >
              <!-- Profile Settings Button -->
              <button
                @click="$router.push('/profile'); showProfileDropdown = false"
                class="w-full px-4 py-3 flex items-center justify-center gap-2 bg-gray-800 hover:bg-blue-600 text-white font-medium transition-all border-b-2 border-gray-700"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Profile Settings
              </button>

              <!-- Load Money Button -->
              <button
                @click="showLoadModal = true; showProfileDropdown = false"
                class="w-full px-4 py-3 flex items-center justify-center gap-2 bg-gray-800 hover:bg-green-600 text-white font-medium transition-all border-b-2 border-gray-700"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Load Money
              </button>

              <!-- Logout Button -->
              <button
                @click="handleLogout"
                class="w-full px-4 py-3 flex items-center justify-center gap-2 bg-gray-800 hover:bg-red-600 text-white font-medium transition-all"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                Logout
              </button>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Load Money Modal -->
    <div v-if="showLoadModal" class="fixed inset-0 bg-black/80 flex items-center justify-center z-50">
      <div class="bg-gray-800 rounded-2xl p-6 max-w-md w-full mx-4 border-2 border-gray-700 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-2xl font-bold text-white">Deposit Funds</h3>
          <button
            @click="showLoadModal = false"
            class="p-2 text-gray-400 hover:text-white hover:bg-gray-700 rounded-lg transition"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Amount Input -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-300 mb-2">Amount (USD)</label>
          <input
            v-model.number="loadAmount"
            type="number"
            placeholder="Enter amount"
            min="1"
            max="100000"
            class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
          />
          <div class="grid grid-cols-4 gap-2 mt-3">
            <button
              v-for="quick in [100, 500, 1000, 10000]"
              :key="quick"
              @click="loadAmount = quick"
              class="px-3 py-2 bg-gray-700 hover:bg-blue-600 text-white rounded-lg text-sm font-medium transition border border-gray-600"
            >
              ${{ quick }}
            </button>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="mb-4">
          <label class="block text-sm font-semibold text-gray-300 mb-2">Payment Method</label>
          <select
            v-model="paymentMethod"
            class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
          >
            <option value="credit_card">Credit Card</option>
            <option value="bank_transfer">Bank Transfer</option>
            <option value="paypal">PayPal</option>
          </select>
        </div>

        <!-- Success Message -->
        <div v-if="loadSuccess" class="mb-4 p-3 bg-green-600/20 border border-green-600 rounded-lg">
          <p class="text-green-400 text-sm font-medium">✓ Funds loaded successfully!</p>
        </div>

        <!-- Error Message -->
        <div v-if="loadError" class="mb-4 p-3 bg-red-600/20 border border-red-600 rounded-lg">
          <p class="text-red-400 text-sm font-medium">{{ loadError }}</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3">
          <button
            @click="showLoadModal = false"
            class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-xl font-semibold transition-all"
          >
            Cancel
          </button>
          <button
            @click="handleLoadMoney"
            :disabled="loadingMoney || !loadAmount || loadAmount <= 0"
            class="flex-1 px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-all disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {{ loadingMoney ? 'Processing...' : 'Confirm Deposit' }}
          </button>
        </div>
      </div>
    </div>

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
import WalletBalance from '../components/WalletBalance.vue';
import echo from '../services/echo';
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
const showProfileDropdown = ref(false);
const showLoadModal = ref(false);
const loadAmount = ref(null);
const paymentMethod = ref('credit_card');
const loadingMoney = ref(false);
const loadSuccess = ref(false);
const loadError = ref('');

onMounted(async () => {
  const userData = localStorage.getItem('user');
  if (userData) {
    user.value = JSON.parse(userData);
  }

  await fetchProfile();
  await fetchUserOrders();
  setupPusher();

  // Close dropdown when clicking outside
  document.addEventListener('click', handleClickOutside);
});

const handleClickOutside = (event) => {
  const dropdown = event.target.closest('.relative');
  if (!dropdown) {
    showProfileDropdown.value = false;
  }
};

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
    console.log('Orders API response:', response.data);

    // Handle different response formats
    const ordersData = Array.isArray(response.data) ? response.data : (response.data.orders || []);

    // Filter only the logged-in user's orders
    userOrders.value = ordersData.filter(order => order.user_id === user.value?.id);
    console.log('Filtered user orders:', userOrders.value);
  } catch (err) {
    console.error('Failed to fetch user orders:', err);
    userOrders.value = []; // Set empty array on error
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

        // Also refresh transaction history
        if (transactionHistoryRef.value) {
          transactionHistoryRef.value.fetchTransactions();
        }
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

const handleLoadMoney = async () => {
  if (!loadAmount.value || loadAmount.value <= 0) {
    loadError.value = 'Please enter a valid amount';
    return;
  }

  loadingMoney.value = true;
  loadError.value = '';
  loadSuccess.value = false;

  try {
    await api.post('/wallet/deposit', {
      amount: loadAmount.value,
      payment_method: paymentMethod.value
    });

    loadSuccess.value = true;
    loadAmount.value = null;

    // Refresh profile to get updated balance
    await fetchProfile();

    // Close modal after 1.5 seconds
    setTimeout(() => {
      showLoadModal.value = false;
      loadSuccess.value = false;
    }, 1500);
  } catch (err) {
    loadError.value = err.response?.data?.message || 'Failed to load money. Please try again.';
  } finally {
    loadingMoney.value = false;
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

  document.removeEventListener('click', handleClickOutside);
});
</script>
