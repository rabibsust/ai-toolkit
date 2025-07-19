<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-900">Analysis History</h1>
        <p class="text-gray-600 mt-2">Review your past code analyses and track improvements</p>
      </div>

      <!-- History List -->
      <div class="space-y-4">
        <div v-if="analyses.length === 0" class="text-center py-12">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900">No analyses yet</h3>
          <p class="text-gray-500">Start analyzing code to build your history</p>
          <Button @click="$inertia.visit('/')" class="mt-4">
            Analyze Code
          </Button>
        </div>

        <div v-else class="grid gap-4">
          <Card v-for="analysis in analyses" :key="analysis.id" class="hover:shadow-lg transition-shadow cursor-pointer" @click="viewAnalysis(analysis.id)">
            <CardContent class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900">{{ analysis.file_name }}</h3>
                  <p class="text-sm text-gray-500 mt-1">{{ formatDate(analysis.created_at) }}</p>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="text-right">
                    <div class="text-2xl font-bold text-blue-600">{{ analysis.score }}/10</div>
                    <div class="text-xs text-gray-500">Quality Score</div>
                  </div>
                  <div :class="getScoreColor(analysis.score)" class="w-3 h-3 rounded-full"></div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import AppLayout from '@/layouts/AppLayout.vue'

// Props from backend
defineProps<{
  analyses: Array<{
    id: number
    file_name: string
    score: number
    created_at: string
  }>
}>()

// Methods
const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const getScoreColor = (score: number) => {
  if (score >= 8) return 'bg-green-500'
  if (score >= 6) return 'bg-yellow-500'
  return 'bg-red-500'
}

const viewAnalysis = (id: number) => {
  // For now, just show an alert. We can add a detailed view later
  alert(`Viewing analysis #${id} - Coming soon!`)
}
</script>
