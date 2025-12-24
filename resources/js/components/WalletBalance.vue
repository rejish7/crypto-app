<template>
  <div class="bg-gray-800 rounded-2xl shadow-2xl p-6 border-2 border-gray-700">
    <div class="flex justify-between items-center mb-6">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 bg-blue-600 rounded-xl flex items-center justify-center shadow-lg">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">Wallet</h2>
      </div>
      <button
        @click="emit('refresh')"
        class="p-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all"
      >
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
        </svg>
      </button>
    </div>

    <div v-if="loading" class="flex justify-center items-center py-12">
      <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500"></div>
    </div>

    <div v-else-if="profile" class="space-y-4">
      <!-- USD Balance -->
      <div class="bg-gray-700 p-6 rounded-xl border-2 border-green-600 shadow-lg">
        <div class="flex justify-between items-center">
          <div>
            <p class="text-gray-400 text-sm font-medium mb-1">Available Balance</p>
            <span class="text-4xl font-bold text-green-400">
              ${{ Number(profile.balance || 0).toFixed(2) }}
            </span>
          </div>
          <div class="w-14 h-14 bg-green-600 rounded-xl flex items-center justify-center shadow-lg">
            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
        </div>
      </div>

      <!-- Assets -->
      <div class="space-y-3">
        <div class="flex items-center gap-2">
          <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
          </svg>
          <h3 class="text-lg font-bold text-white">My Assets</h3>
        </div>

        <div v-if="profile.assets && profile.assets.filter(a => Number(a.amount) > 0).length > 0">
          <div
            v-for="asset in profile.assets.filter(a => Number(a.amount) > 0)"
            :key="asset.id"
            class="bg-gray-700 p-4 rounded-xl border-2 border-gray-600 hover:border-blue-500 transition-all mb-3"
          >
            <div class="flex justify-between items-center mb-3">
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-blue-600 rounded-lg flex items-center justify-center shadow-lg">
                  <span class="text-white font-bold text-base">{{ asset.symbol }}</span>
                </div>
                <span class="text-white font-semibold text-lg">{{ asset.symbol }}</span>
              </div>
              <div class="text-right">
                <span class="text-2xl font-bold text-blue-400">
                  {{ Number(asset.amount || 0).toFixed(8) }}
                </span>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-3 text-sm">
              <div class="bg-gray-800 px-3 py-2 rounded-lg border border-gray-600">
                <div class="text-gray-400 text-xs mb-1">Locked</div>
                <div class="text-yellow-400 font-semibold">{{ Number(asset.locked_amount || 0).toFixed(8) }}</div>
              </div>
              <div class="bg-gray-800 px-3 py-2 rounded-lg border border-gray-600">
                <div class="text-gray-400 text-xs mb-1">Available</div>
                <div class="text-green-400 font-semibold">
                  {{ (Number(asset.amount || 0) - Number(asset.locked_amount || 0)).toFixed(8) }}
                </div>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="bg-gray-700/30 border-2 border-dashed border-gray-600 p-6 rounded-xl text-center">
          <svg class="w-12 h-12 text-gray-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
          </svg>
          <p class="text-gray-400">No assets yet</p>
        </div>
      </div>
    </div>

    <div v-else class="text-center text-gray-400">
      Loading...
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from 'vue';

defineProps({
  profile: {
    type: Object,
    default: null,
  },
  loading: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['refresh']);
</script>
