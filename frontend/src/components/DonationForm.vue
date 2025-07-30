<template>
  <div class="max-w-2xl w-full mx-auto py-8 px-4">
    <div v-if="loading" class="text-center py-8">
      <div class="text-gray-500">Loading campaign...</div>
    </div>
    <div v-else class="bg-white shadow-lg rounded-lg p-8">
      <div class="mb-8">
        <h1 class="text-3xl font-bold mb-4 text-gray-900">{{ campaign.title }}</h1>
        <p class="text-gray-600 text-lg leading-relaxed mb-6">{{ campaign.description }}</p>
        
        <div class="bg-gray-50 rounded-lg p-4">
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Goal Amount:</span>
            <span class="text-lg font-bold text-green-600">${{ campaign.goal_amount }}</span>
          </div>
          <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700">Current Total:</span>
            <span class="text-lg font-semibold text-blue-600">${{ campaign.donations_sum_amount || 0 }}</span>
          </div>
          <div class="flex justify-between items-center">
            <span class="text-sm font-medium text-gray-700">Total Donations:</span>
            <span class="text-sm text-gray-600">{{ campaign.donations_count }} donations</span>
          </div>
        </div>
      </div>

      <form @submit.prevent="goToCheckout" class="space-y-6">
        <div>
          <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
            Donation Amount ($)
          </label>
          <input 
            id="amount"
            v-model.number="amount" 
            type="number" 
            placeholder="Enter amount"
            min="1"
            step="0.01"
            class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg" 
            required 
          />
        </div>

        <button 
          type="submit"
          class="w-full py-3 cursor-pointer bg-orange-500 text-white text-md font-semibold rounded-lg hover:bg-orange-600 transition-colors shadow-md">
          Proceed to Payment
        </button>
      </form>
    </div>
  </div>
</template>


<script lang="ts">
import { defineComponent, onMounted, ref } from 'vue';
import { loadStripe } from '@stripe/stripe-js';
import axios from 'axios';
import { useRoute } from 'vue-router';
import { Campaign } from '../types';

export default defineComponent({
  setup() {
    const route = useRoute();
    const amount = ref(0);
    const campaign = ref<Campaign>({} as Campaign);
    const loading = ref(true);

    onMounted(async () => {
      try {
        const { data } = await axios.get<Campaign>(`/api/campaigns/${route.params.id}`);
        campaign.value = data;
      } catch (error) {
        console.error('Failed to load campaign:', error);
      } finally {
        loading.value = false;
      }
    });

    const goToCheckout = async () => {
      const stripe = await loadStripe(import.meta.env.VITE_STRIPE_KEY);
      const { data } = await axios.post<{ sessionId: string }>(
        `/api/campaigns/${campaign.value.id}/checkout`,
        { amount: amount.value }
      );
      await stripe!.redirectToCheckout({ sessionId: data.sessionId });
    };

    return { amount, campaign, goToCheckout, loading };
  }
});
</script>
