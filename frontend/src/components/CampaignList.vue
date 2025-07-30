<template>
  <div class="max-w-6xl w-full mx-auto py-8 px-4">
    <h1 class="text-3xl font-bold mb-8 text-center">Campaigns</h1>
    
    <!-- Search Bar -->
    <div class="mb-6">
      <div class="max-w-md mx-auto">
        <div class="relative">
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            placeholder="Search campaigns..."
            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
          />
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
          </div>
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute inset-y-0 right-0 pr-3 flex items-center"
          >
            <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Loading campaigns...</div>
    </div>
    <div v-else-if="campaigns.length === 0" class="text-center text-gray-500 py-6">
      {{ searchQuery ? 'No campaigns found matching your search.' : 'No campaigns found.' }}
    </div>
    <div v-else class="bg-white shadow-lg rounded-lg overflow-hidden">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaign</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Goal Amount</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Total</th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Donations</th>
            <th class="px-6 py-3 text-xs font-medium text-gray-500 uppercase tracking-wider w-20 text-center">Action</th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <tr v-for="c in campaigns" :key="c.id" class="hover:bg-gray-50">
            <td class="px-6 py-4 whitespace-nowrap">
              <h3 class="text-lg font-semibold text-gray-900">{{ c.title }}</h3>
            </td>
            <td class="px-6 py-4">
              <p class="text-gray-600 max-w-xs truncate">{{ c.description || 'No description provided.' }}</p>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm font-semibold text-green-600">${{ c.goal_amount }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm font-semibold text-blue-600">${{ c.donations_sum_amount || 0 }}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
              <span class="text-sm text-gray-500">{{ c.donations_count }} donations</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center w-20">
              <router-link v-if="user?.role === 'admin'" :to="`/campaign/${c.id}`"
                class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors text-sm">
                View
              </router-link>
              <router-link v-else :to="`/donate/${c.id}`"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors text-sm">
                Donate
              </router-link>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div v-if="user?.role === 'admin'" class="flex justify-center mt-10">
      <router-link to="/new"
        class="inline-block px-8 py-2.5 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 transition-colors font-medium">
        + New Campaign
      </router-link>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref, watch } from 'vue';
import axios from 'axios';
import { Campaign } from '../types';
import { user } from '../composables/useAuth';

export default defineComponent({
  setup() {
    const campaigns = ref<Campaign[]>([]);
    const searchQuery = ref('');
    const loading = ref(false);
    let searchTimeout: number;
    
    const fetchCampaigns = async (search = '') => {
      loading.value = true;
      try {
        const params = search ? { search } : {};
        const { data } = await axios.get<{ data: Campaign[] }>('/api/campaigns', { params });
        campaigns.value = data.data || [];
      } catch (e) {
        campaigns.value = [];
      } finally {
        loading.value = false;
      }
    };

    const handleSearch = () => {
      clearTimeout(searchTimeout);
      searchTimeout = setTimeout(() => {
        fetchCampaigns(searchQuery.value);
      }, 300); // Debounce search
    };

    const clearSearch = () => {
      searchQuery.value = '';
      fetchCampaigns();
    };
    
    onMounted(() => {
      fetchCampaigns();
    });
    
    return { 
      campaigns, 
      user, 
      searchQuery, 
      loading,
      handleSearch,
      clearSearch
    };
  }
});
</script>
