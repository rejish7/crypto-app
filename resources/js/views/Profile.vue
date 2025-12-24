<template>
  <div class="min-h-screen bg-gray-900">
    <!-- Header -->
    <nav class="bg-gray-800 border-b-2 border-blue-600 shadow-xl">
      <div class="container mx-auto px-6 py-4">
        <div class="flex justify-between items-center">
          <div class="flex items-center gap-3">
            <button
              @click="$router.push('/dashboard')"
              class="p-2 hover:bg-gray-700 rounded-lg transition-all"
            >
              <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
              </svg>
            </button>
            <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
              <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">Profile Settings</h1>
          </div>
        </div>
      </div>
    </nav>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-8">
      <div class="max-w-4xl mx-auto">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Sidebar -->
          <div class="space-y-2">
            <button
              @click="activeTab = 'personal'"
              class="w-full px-4 py-3 flex items-center gap-3 rounded-lg font-medium transition-all"
              :class="activeTab === 'personal' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Personal Info
            </button>
            <button
              @click="activeTab = 'security'"
              class="w-full px-4 py-3 flex items-center gap-3 rounded-lg font-medium transition-all"
              :class="activeTab === 'security' ? 'bg-blue-600 text-white' : 'bg-gray-800 text-gray-300 hover:bg-gray-700'"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
              </svg>
              Security
            </button>
          </div>

          <!-- Content Area -->
          <div class="md:col-span-2">
            <!-- Personal Info Tab -->
            <div v-if="activeTab === 'personal'" class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
              <h2 class="text-2xl font-bold text-white mb-6">Personal Information</h2>

              <div v-if="successMessage" class="mb-4 p-4 bg-green-600/20 border border-green-600 rounded-lg">
                <p class="text-green-400 font-medium">{{ successMessage }}</p>
              </div>

              <div v-if="errorMessage" class="mb-4 p-4 bg-red-600/20 border border-red-600 rounded-lg">
                <p class="text-red-400 font-medium">{{ errorMessage }}</p>
              </div>

              <form @submit.prevent="updatePersonalInfo" class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Full Name</label>
                  <input
                    v-model="personalForm.name"
                    type="text"
                    required
                    class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
                  />
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Email Address</label>
                  <input
                    v-model="personalForm.email"
                    type="email"
                    required
                    class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
                  />
                </div>

                <div class="pt-4">
                  <button
                    type="submit"
                    :disabled="loadingPersonal"
                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-all shadow-lg"
                  >
                    {{ loadingPersonal ? 'Updating...' : 'Update Information' }}
                  </button>
                </div>
              </form>
            </div>

            <!-- Security Tab -->
            <div v-if="activeTab === 'security'" class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
              <h2 class="text-2xl font-bold text-white mb-6">Change Password</h2>

              <div v-if="successMessage" class="mb-4 p-4 bg-green-600/20 border border-green-600 rounded-lg">
                <p class="text-green-400 font-medium">{{ successMessage }}</p>
              </div>

              <div v-if="errorMessage" class="mb-4 p-4 bg-red-600/20 border border-red-600 rounded-lg">
                <p class="text-red-400 font-medium">{{ errorMessage }}</p>
              </div>

              <form @submit.prevent="updatePassword" class="space-y-4">
                <div>
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Current Password</label>
                  <div class="relative">
                    <input
                      v-model="securityForm.current_password"
                      :type="showCurrentPassword ? 'text' : 'password'"
                      required
                      class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
                    />
                    <button
                      type="button"
                      @click="showCurrentPassword = !showCurrentPassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                    >
                      <svg v-if="!showCurrentPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-300 mb-2">New Password</label>
                  <div class="relative">
                    <input
                      v-model="securityForm.new_password"
                      :type="showNewPassword ? 'text' : 'password'"
                      required
                      minlength="8"
                      class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
                    />
                    <button
                      type="button"
                      @click="showNewPassword = !showNewPassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                    >
                      <svg v-if="!showNewPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                  </div>
                  <p class="text-xs text-gray-400 mt-1">Minimum 8 characters</p>
                </div>

                <div>
                  <label class="block text-sm font-semibold text-gray-300 mb-2">Confirm New Password</label>
                  <div class="relative">
                    <input
                      v-model="securityForm.new_password_confirmation"
                      :type="showConfirmPassword ? 'text' : 'password'"
                      required
                      class="w-full px-4 py-3 bg-gray-700 text-white rounded-xl border-2 border-gray-600 focus:border-blue-500 focus:outline-none transition"
                    />
                    <button
                      type="button"
                      @click="showConfirmPassword = !showConfirmPassword"
                      class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white"
                    >
                      <svg v-if="!showConfirmPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                      <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    </button>
                  </div>
                </div>

                <div class="pt-4">
                  <button
                    type="submit"
                    :disabled="loadingSecurity"
                    class="w-full px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white rounded-xl font-semibold transition-all shadow-lg"
                  >
                    {{ loadingSecurity ? 'Updating...' : 'Change Password' }}
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '../services/api';

const router = useRouter();
const activeTab = ref('personal');
const successMessage = ref('');
const errorMessage = ref('');
const loadingPersonal = ref(false);
const loadingSecurity = ref(false);

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const personalForm = ref({
  name: '',
  email: '',
});

const securityForm = ref({
  current_password: '',
  new_password: '',
  new_password_confirmation: '',
});

onMounted(async () => {
  // Load data from localStorage first as fallback
  const userData = localStorage.getItem('user');
  if (userData) {
    const user = JSON.parse(userData);
    personalForm.value.name = user.name || '';
    personalForm.value.email = user.email || '';
  }

  // Then fetch fresh data from API
  await fetchUserData();
});

const fetchUserData = async () => {
  try {
    const response = await api.get('/profile');

    // Handle different possible response structures
    const userData = response.data.user || response.data;

    if (userData.name) {
      personalForm.value.name = userData.name;
    }
    if (userData.email) {
      personalForm.value.email = userData.email;
    }
  } catch (err) {
    console.error('Failed to fetch user data:', err);
  }
};

const updatePersonalInfo = async () => {
  loadingPersonal.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    const response = await api.put('/profile', personalForm.value);

    // Update localStorage
    const userData = JSON.parse(localStorage.getItem('user') || '{}');
    userData.name = personalForm.value.name;
    userData.email = personalForm.value.email;
    localStorage.setItem('user', JSON.stringify(userData));

    successMessage.value = 'Personal information updated successfully!';

    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to update personal information';
    setTimeout(() => {
      errorMessage.value = '';
    }, 5000);
  } finally {
    loadingPersonal.value = false;
  }
};

const updatePassword = async () => {
  if (securityForm.value.new_password !== securityForm.value.new_password_confirmation) {
    errorMessage.value = 'New passwords do not match';
    setTimeout(() => {
      errorMessage.value = '';
    }, 3000);
    return;
  }

  loadingSecurity.value = true;
  successMessage.value = '';
  errorMessage.value = '';

  try {
    await api.put('/profile/password', {
      current_password: securityForm.value.current_password,
      new_password: securityForm.value.new_password,
      new_password_confirmation: securityForm.value.new_password_confirmation,
    });

    successMessage.value = 'Password changed successfully!';

    // Clear form
    securityForm.value = {
      current_password: '',
      new_password: '',
      new_password_confirmation: '',
    };

    setTimeout(() => {
      successMessage.value = '';
    }, 3000);
  } catch (err) {
    errorMessage.value = err.response?.data?.message || 'Failed to change password';
    setTimeout(() => {
      errorMessage.value = '';
    }, 5000);
  } finally {
    loadingSecurity.value = false;
  }
};
</script>
