<template>
  <div class="max-w-md mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">New Campaign</h1>
    <form @submit.prevent="submit" class="space-y-4">
      <input v-model="title" placeholder="Title"
             class="w-full p-3 border rounded focus:outline-none focus:ring" required />

      <textarea v-model="description" placeholder="Description"
                class="w-full p-3 border rounded focus:outline-none focus:ring"></textarea>

      <input v-model.number="goal" type="number" placeholder="Goal Amount"
             class="w-full p-3 border rounded focus:outline-none focus:ring" required />

      <button type="submit" :disabled="isSubmitting"
              class="w-full py-3 bg-indigo-600 text-white rounded hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed">
        {{ isSubmitting ? 'Creating...' : 'Create Campaign' }}
      </button>
    </form>
  </div>
</template>


<script lang="ts">
import { defineComponent, ref } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';
import { useToast } from "vue-toastification"

export default defineComponent({
  setup() {
    const title = ref(''), description = ref(''), goal = ref(0);
    const router = useRouter();
    const toast = useToast();
    const isSubmitting = ref(false);

    const submit = async () => {
      if (isSubmitting.value) return; // Prevent double submission
      
      isSubmitting.value = true;
      
      try {
        await axios.post('/api/admin/campaigns', {
          title: title.value,
          description: description.value,
          goal_amount: goal.value
        });
        
        router.push('/');
        toast.success('Campaign created successfully!', {
          timeout: 2000
        });
      } catch (error) {
        console.error('Failed to create campaign:', error);
        toast.error('Failed to create campaign. Please try again.');
      } finally {
        isSubmitting.value = false;
      }
    };
    
    return { title, description, goal, submit, isSubmitting };
  }
});
</script>
