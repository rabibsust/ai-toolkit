<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-100">Analysis History</h1>
        <p class="text-gray-300 mt-2">Review your past code analyses and track improvements</p>
      </div>

      <!-- History List -->
      <div class="space-y-4">
        <div v-if="analyses.length === 0" class="text-center py-12">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-100">No analyses yet</h3>
          <p class="text-gray-200">Start analyzing code to build your history</p>
          <Button @click="$inertia.visit('/')" class="mt-4">
            Analyze Code
          </Button>
        </div>

        <div v-else class="grid gap-4">
          <Card v-for="analysis in analyses" :key="analysis.id" class="hover:shadow-lg transition-shadow cursor-pointer" @click="viewAnalysis(analysis.id)">
            <CardContent class="p-6">
              <div class="flex items-center justify-between">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-100">{{ analysis.file_name }}</h3>
                  <p class="text-sm text-gray-300 mt-1">{{ formatDate(analysis.created_at) }}</p>
                </div>
                <div class="flex items-center space-x-4">
                  <div class="text-right">
                    <div class="text-2xl font-bold text-blue-400">{{ analysis.score }}/10</div>
                    <div class="text-xs text-gray-400">Quality Score</div>
                  </div>
                  <div :class="getScoreColor(analysis.score)" class="w-3 h-3 rounded-full"></div>
                </div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>

      <!-- Analysis Details Modal -->
        <Dialog v-model:open="showDetailsModal">
            <DialogContent class="w-full max-w-[95vw] sm:max-w-md md:max-w-xl lg:max-w-4xl xl:max-w-6xl mx-0 sm:mx-auto max-h-[90vh] overflow-hidden p-0 bg-gray-900">
                <!-- Custom Header with better padding -->
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                    <h2 class="text-lg sm:text-xl font-semibold text-gray-900 truncate">{{ selectedAnalysis?.file_name }}</h2>
                    <p class="text-xs sm:text-sm text-gray-600 mt-1">
                        Analysis from {{ selectedAnalysis ? formatDate(selectedAnalysis.created_at) : '' }}
                    </p>
                    </div>
                    <button @click="showDetailsModal = false" class="ml-4 text-gray-400 hover:text-gray-600 flex-shrink-0">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    </button>
                </div>
                </div>

                <!-- Scrollable Content with proper padding -->
                <div class="overflow-y-auto max-h-[calc(90vh-80px)]">
                <div v-if="selectedAnalysis" class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                    <!-- Score Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-4 sm:p-6">
                    <div class="flex items-center justify-between">
                        <div class="min-w-0 flex-1">
                        <h3 class="text-base sm:text-lg font-semibold text-blue-900">Code Quality Score</h3>
                        <p class="text-xs sm:text-sm text-blue-700 mt-1">Based on Laravel best practices</p>
                        </div>
                        <div class="text-right flex-shrink-0 ml-4">
                        <div class="text-2xl sm:text-3xl font-bold text-blue-600">{{ selectedAnalysis.score }}</div>
                        <div class="text-xs sm:text-sm text-blue-600">/10</div>
                        </div>
                    </div>

                    <!-- Score Bar -->
                    <div class="mt-4">
                        <div class="bg-blue-200 rounded-full h-2">
                        <div
                            class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                            :style="{ width: (selectedAnalysis.score * 10) + '%' }"
                        ></div>
                        </div>
                    </div>
                    </div>

                    <!-- Suggestions Section -->
                    <div v-if="selectedAnalysis.suggestions && selectedAnalysis.suggestions.length > 0">
                    <h3 class="text-base sm:text-lg font-semibold mb-4 flex items-center text-gray-100">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Suggestions
                    </h3>

                    <div class="space-y-3 sm:space-y-4">
                        <div
                        v-for="(suggestion, index) in selectedAnalysis.suggestions"
                        :key="index"
                        class="bg-white border text-gray-900 border-gray-200 rounded-lg p-3 sm:p-4 shadow-sm"
                        >
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 w-5 h-5 sm:w-6 sm:h-6 bg-yellow-100 rounded-full flex items-center justify-center mt-0.5">
                            <span class="text-xs font-semibold text-yellow-600">{{ index + 1 }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                            <div class="prose prose-sm max-w-none break-words" v-html="formatSuggestion(suggestion)"></div>
                            </div>
                        </div>
                        </div>
                    </div>
                    </div>

                    <!-- Original Code Section -->
                    <div>
                    <h3 class="text-base sm:text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.316 3.051a1 1 0 01.633 1.265l-4 12a1 1 0 11-1.898-.632l4-12a1 1 0 011.265-.633zM5.707 6.293a1 1 0 010 1.414L3.414 10l2.293 2.293a1 1 0 11-1.414 1.414l-3-3a1 1 0 010-1.414l3-3a1 1 0 011.414 0zm8.586 0a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 11-1.414-1.414L16.586 10l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                        Original Code
                    </h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                        <pre class="bg-gray-900 text-gray-100 p-3 sm:p-4 overflow-x-auto text-xs sm:text-sm leading-relaxed"><code>{{ selectedAnalysis.code }}</code></pre>
                    </div>
                    </div>

                    <!-- Full Analysis Section -->
                    <div class="pb-8">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Detailed Analysis
                    </h3>
                    <div class="bg-gray-50 border border-gray-200 rounded-lg overflow-hidden">
                        <div class="p-3 sm:p-4 max-h-150 overflow-y-auto">
                        <pre class="whitespace-pre-wrap text-xs sm:text-sm text-gray-700 font-mono leading-relaxed break-words">{{ selectedAnalysis.analysis }}</pre>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </DialogContent>
        </Dialog>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref } from 'vue'
import { Card, CardContent } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog'
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

// Reactive data
const showDetailsModal = ref(false)
const selectedAnalysis = ref<any>(null)

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

const viewAnalysis = async (id: number) => {
  try {
    const response = await fetch(`/api/analysis/${id}`)
    const data = await response.json()

    if (data.status === 'success') {
      selectedAnalysis.value = data.analysis
      showDetailsModal.value = true
    } else {
      alert('Failed to load analysis details')
    }
  } catch (error) {
    console.error('Error loading analysis:', error)
    alert('Error loading analysis details')
  }
}

const formatSuggestion = (suggestion: string) => {
  const formatted = suggestion
    // Convert ```php code blocks to HTML with responsive styling
    .replace(/```php\n([\s\S]*?)\n```/g, '<div class="mt-2 mb-2 sm:mt-3 sm:mb-3"><pre class="bg-gray-900 text-gray-100 p-2 sm:p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm font-mono leading-relaxed">$1</code></pre></div>')
    // Convert any remaining ``` code blocks
    .replace(/```\n([\s\S]*?)\n```/g, '<div class="mt-2 mb-2 sm:mt-3 sm:mb-3"><pre class="bg-gray-900 text-gray-100 p-2 sm:p-4 rounded-lg overflow-x-auto"><code class="text-xs sm:text-sm font-mono leading-relaxed">$1</code></pre></div>')
    // Convert inline code with backticks
    .replace(/`([^`]+)`/g, '<code class="bg-gray-100 text-gray-800 px-1 sm:px-2 py-0.5 sm:py-1 rounded text-xs sm:text-sm font-mono break-all">$1</code>')
    // Convert newlines to line breaks (but not inside code blocks)
    .replace(/\n(?![^<]*<\/pre>)/g, '<br>')

  return formatted
}
</script>
