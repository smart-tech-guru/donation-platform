<template>
  <div class="max-w-6xl w-full mx-auto py-8 px-4">
    <div class="mb-6">
      <router-link to="/" 
        class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Campaigns
      </router-link>
    </div>
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Loading campaign details...</div>
    </div>
    <div v-else>
      <!-- Campaign Info -->
      <div class="bg-white shadow-lg rounded-lg p-8 mb-8">
        <h1 class="text-3xl font-bold mb-4 text-gray-900">{{ campaign.title }}</h1>
        <p class="text-gray-600 text-lg mb-6">{{ campaign.description }}</p>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="bg-green-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-700 mb-1">Goal Amount</h3>
            <p class="text-2xl font-bold text-green-600">${{ campaign.goal_amount }}</p>
          </div>
          <div class="bg-blue-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-700 mb-1">Current Total</h3>
            <p class="text-2xl font-bold text-blue-600">${{ campaign.donations_sum_amount || 0 }}</p>
          </div>
          <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="text-sm font-medium text-gray-700 mb-1">Total Donations</h3>
            <p class="text-2xl font-bold text-gray-600">{{ campaign.donations_count }}</p>
          </div>
        </div>
      </div>

      <!-- Donation History -->
      <div class="bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-xl font-semibold text-gray-900">Donation History</h2>
        </div>
        <div v-if="donations.length === 0" class="text-center py-8 text-gray-500">
          No donations yet.
        </div>
        <table v-else class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Name</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User Email</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-for="(donation, index) in donations" :key="donation.id" class="hover:bg-gray-50">
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ index + 1 }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ donation.user.name }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ donation.user.email }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-green-600">${{ donation.amount }}</td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(donation.created_at) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { Campaign, Donation } from '../types';

export default defineComponent({
  setup() {
    const route = useRoute();
    const campaign = ref<Campaign>({} as Campaign);
    const donations = ref<Donation[]>([]);
    const loading = ref(true);

    onMounted(async () => {
      try {
        const { data } = await axios.get<{ campaign: Campaign, donations: Donation[] }>(`/api/admin/campaigns/${route.params.id}`);
        campaign.value = data.campaign;
        donations.value = data.donations || [];
      } catch (error) {
        console.error('Failed to load campaign details:', error);
      } finally {
        loading.value = false;
      }
    });

    const formatDate = (dateString: string) => {
      return new Date(dateString).toLocaleDateString();
    };

    return { campaign, donations, loading, formatDate };
  }
});
</script>