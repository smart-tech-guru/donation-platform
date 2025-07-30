<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50">
    <div class="max-w-md w-full bg-white shadow-lg rounded-lg p-8 text-center">
      <div class="mb-6">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
          <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
          </svg>
        </div>
      </div>
      
      <h1 class="text-2xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
      <p class="text-gray-600 mb-6">Thank you for your generous donation for {{campaign?.title}}. Your contribution makes a difference!</p>
      
      <div class="space-y-3">
        <router-link to="/" 
          class="w-full inline-block px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
          Back to Campaigns
        </router-link>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import axios from 'axios';
import { Campaign } from '../types';
import { useToast } from "vue-toastification";

export default defineComponent({
  setup() {
    const route = useRoute();
    const toast = useToast();
    const campaign = ref<Campaign | null>(null);
    const sessionId = ref(route.query.session_id as string);
    const campaignId = ref(route.query.campaign_id as string);
    const donationConfirmed = ref(false);

    onMounted(async () => {
      if (campaignId.value && sessionId.value) {
        try {
          // Confirm payment and create donation record
          await axios.post('/api/donations/confirm', {
            session_id: sessionId.value,
            campaign_id: campaignId.value
          });
          donationConfirmed.value = true;
          toast.success('Donation confirmed successfully!');

          // Load campaign details
          const { data } = await axios.get<Campaign>(`/api/campaigns/${campaignId.value}`);
          campaign.value = data;
        } catch (error) {
          console.error('Failed to confirm donation:', error);
          toast.error('Failed to confirm donation');
        }
      }
    });

    return {
      campaign,
      sessionId,
      campaignId,
      donationConfirmed
    };
  }
});
</script>
