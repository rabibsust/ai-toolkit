<template>
  <AppLayout>
    <div class="p-6 space-y-6">
      <!-- Header -->
      <div class="border-b pb-4">
        <h1 class="text-3xl font-bold text-gray-100">AI Code Analyzer</h1>
        <p class="text-gray-300 mt-2">Analyze your Laravel controllers with AI-powered insights</p>
      </div>

      <!-- Main Content -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Input Section -->
        <Card>
          <CardHeader>
            <CardTitle>Code Input</CardTitle>
            <CardDescription>Paste your Laravel controller code below</CardDescription>
          </CardHeader>
          <CardContent class="space-y-4">
            <!-- AI Provider Selection -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <Label for="provider-select">AI Provider</Label>
                <select
                  id="provider-select"
                  v-model="selectedProvider"
                  @change="onProviderChange"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white text-gray-900 mt-1"
                >
                  <option v-for="(provider, key) in availableProviders" :key="key" :value="key">
                    {{ provider.name }}
                  </option>
                </select>
              </div>

              <div v-if="Object.keys(availableModels).length > 0">
                <Label for="model-select">Model</Label>
                <select
                  id="model-select"
                  v-model="selectedModel"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white text-gray-900 mt-1"
                >
                  <option v-for="(model, key) in availableModels" :key="key" :value="key">
                    {{ model.name }} - ${{ model.cost_per_request.toFixed(4) }}
                  </option>
                </select>
                <p class="text-xs text-gray-500 mt-1">{{ selectedModelInfo?.description }}</p>
              </div>
            </div>

            <!-- Analysis Options -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label for="focus-area">Focus Area</Label>
                <select
                  id="focus-area"
                  v-model="analysisOptions.focus"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white text-gray-900 mt-1"
                >
                  <option value="general">General Analysis</option>
                  <option value="security">Security Focus</option>
                  <option value="performance">Performance Focus</option>
                  <option value="best-practices">Best Practices</option>
                </select>
              </div>

              <div>
                <Label for="detail-level">Detail Level</Label>
                <select
                  id="detail-level"
                  v-model="analysisOptions.detail"
                  class="w-full px-3 py-2 text-sm border border-gray-300 rounded-md focus:border-blue-500 focus:ring-2 focus:ring-blue-200 bg-white text-gray-900 mt-1"
                >
                  <option value="brief">Brief</option>
                  <option value="standard">Standard</option>
                  <option value="detailed">Detailed</option>
                </select>
              </div>
            </div>

            <!-- Code Input -->
            <div>
              <Label for="code-input">Controller Code</Label>
              <textarea
                id="code-input"
                v-model="codeInput"
                class="w-full h-64 p-3 border rounded-md font-mono text-sm mt-4"
                placeholder="<?php

class UserController extends Controller
{
    public function index()
    {
        return view('users.index');
    }
}"
              ></textarea>
            </div>

            <Button @click="analyzeCode" :disabled="isAnalyzing || !codeInput.trim()" class="w-full">
              <span v-if="isAnalyzing">Analyzing with {{ currentProviderName }}...</span>
              <span v-else>Analyze Code with {{ currentProviderName }}</span>
            </Button>
          </CardContent>
        </Card>

        <!-- Results Section -->
        <Card>
          <CardHeader>
            <CardTitle>Analysis Results</CardTitle>
            <CardDescription v-if="!analysis">Results will appear here after analysis</CardDescription>
          </CardHeader>
          <CardContent>
            <!-- Loading State -->
            <div v-if="isAnalyzing" class="flex items-center justify-center h-64">
              <div class="text-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-2 text-gray-600">AI is analyzing your code...</p>
              </div>
            </div>

            <!-- Error State -->
            <div v-else-if="error" class="bg-red-50 border border-red-200 rounded-md p-4">
              <h3 class="text-red-800 font-medium">Analysis Error</h3>
              <p class="text-red-600 mt-1">{{ error }}</p>
            </div>

            <!-- Results -->
            <div v-else-if="analysis" class="space-y-6">
                <!-- Score Card -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6">
                    <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-blue-900">Code Quality Score</h3>
                        <p class="text-sm text-blue-700 mt-1">Based on Laravel best practices</p>
                    </div>
                    <div class="text-right">
                        <div class="text-3xl font-bold text-blue-600">{{ analysis.score }}</div>
                        <div class="text-sm text-blue-600">/10</div>
                    </div>
                    </div>

                    <!-- Score Bar -->
                    <div class="mt-4">
                    <div class="bg-blue-200 rounded-full h-2">
                        <div
                        class="bg-blue-600 h-2 rounded-full transition-all duration-500"
                        :style="{ width: (analysis.score * 10) + '%' }"
                        ></div>
                    </div>
                    </div>
                </div>

                <!-- Suggestions Cards -->
                <div v-if="analysis.suggestions && analysis.suggestions.length > 0">
                    <h3 class="text-lg font-semibold text-gray-100 mb-4 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        Improvement Suggestions
                    </h3>

                    <div class="grid gap-4">
                        <div
                        v-for="(suggestion, index) in analysis.suggestions"
                        :key="index"
                        class="bg-white border text-gray-900 border-gray-200 rounded-lg p-4 shadow-sm hover:shadow-md transition-shadow"
                        >
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0 w-6 h-6 bg-yellow-100 rounded-full flex items-center justify-center mt-0.5">
                                    <span class="text-xs font-semibold text-yellow-600">{{ index + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="prose prose-sm max-w-none" v-html="formatSuggestion(suggestion)"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detailed Analysis -->
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden">
                    <div class="bg-gray-50 px-6 py-3 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm0 4a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            </svg>
                            Detailed Analysis
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="bg-gray-50 rounded-md p-4 max-h-96 overflow-y-auto">
                            <pre class="whitespace-pre-wrap text-sm text-gray-700 font-mono leading-relaxed">{{ analysis.analysis }}</pre>
                        </div>
                    </div>
                </div>

                <!-- File Name Input - Styled -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-lg p-6">
                    <div class="flex items-center space-x-3 mb-3">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        <Label for="file-name" class="text-sm font-semibold text-gray-700">Save Analysis</Label>
                    </div>

                    <Input
                        id="file-name"
                        v-model="fileName"
                        class="w-full px-4 py-3 text-sm border-2 border-gray-300 rounded-lg focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-200 !bg-white shadow-sm text-gray-900"
                        placeholder="Enter a name for this analysis (e.g., UserController Review)"
                    />
                    <p class="text-xs text-gray-900 mt-2">Give your analysis a memorable name to find it later</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <Button @click="clearAnalysis" variant="outline" class="flex-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Analyze New Code
                    </Button>
                    <Button @click="saveAnalysis" class="flex-1">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Save Analysis
                    </Button>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center text-gray-500 h-64 flex items-center justify-center">
              <div>
                <p>No analysis yet</p>
                <p class="text-sm">Paste your code and click "Analyze Code" to get started</p>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AppLayout from '@/layouts/AppLayout.vue'

const props = defineProps<{
  recentAnalyses: Array<any>,
  availableProviders: Record<string, any>  // Add this line
}>()

// Reactive data
const codeInput = ref('')
const analysis = ref(null)
const isAnalyzing = ref(false)
const error = ref('')
const selectedProvider = ref('')
const selectedModel = ref('')
const analysisOptions = ref({
  focus: 'general',
  detail: 'standard'
})

const availableModels = computed(() => {
  if (!selectedProvider.value) {
    console.log('No provider selected')
    return {}
  }

  const provider = props.availableProviders[selectedProvider.value]
  if (!provider) {
    console.log('Provider not found:', selectedProvider.value)
    return {}
  }

  console.log('Provider models:', provider.models)
  return provider.models || {}
})


const selectedModelInfo = computed(() => {
  if (!selectedModel.value || !availableModels.value[selectedModel.value]) {
    return null
  }
  return availableModels.value[selectedModel.value]
})

const currentProviderName = computed(() => {
  if (!selectedProvider.value || !props.availableProviders[selectedProvider.value]) {
    return 'AI'
  }
  return props.availableProviders[selectedProvider.value].name
})

const onProviderChange = () => {
  // Reset model selection when provider changes
  const models = availableModels.value
  selectedModel.value = Object.keys(models)[0] || ''
}


// Methods
const analyzeCode = async () => {
  if (!codeInput.value.trim()) return

  isAnalyzing.value = true
  error.value = ''
  analysis.value = null

  try {
    const response = await fetch('/api/analyze-code', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        code: codeInput.value,
        provider: selectedProvider.value,
        model: selectedModel.value,
        options: analysisOptions.value
      })
    })

    const data = await response.json()

    if (data.status === 'success') {
      analysis.value = data
    } else {
      error.value = data.message || 'Analysis failed'
    }
  } catch (err) {
    error.value = 'Network error occurred'
    console.error('Analysis error:', err)
  } finally {
    isAnalyzing.value = false
  }
}

const clearAnalysis = () => {
  analysis.value = null
  error.value = ''
  codeInput.value = ''
}

const fileName = ref('')

const saveAnalysis = async () => {
  if (!analysis.value) return

  const name = fileName.value.trim() || 'Untitled Analysis'

  // Fix: Ensure suggestions is always an array
  const suggestionsArray = analysis.value.suggestions || []

  try {
    const response = await fetch('/api/save-analysis', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        code: codeInput.value,
        analysis: analysis.value.analysis,
        suggestions: suggestionsArray,  // Use the fixed array
        score: analysis.value.score,
        file_name: name
      })
    })

    const data = await response.json()

    if (data.status === 'success') {
      // Better success notification
      showSuccessMessage('Analysis saved successfully!')
      fileName.value = ''
    } else {
      console.error('Save failed:', data)
      alert('Failed to save analysis: ' + (data.message || 'Unknown error'))
    }
  } catch (err) {
    alert('Error saving analysis')
    console.error('Save error:', err)
  }
}

// Add a success message function
const showSuccessMessage = (message: string) => {
  // Simple success alert for now (we can make this prettier later)
  const successDiv = document.createElement('div')
  successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50'
  successDiv.textContent = message
  document.body.appendChild(successDiv)

  setTimeout(() => {
    document.body.removeChild(successDiv)
  }, 3000)
}

const formatSuggestion = (suggestion: string) => {
  // Convert markdown-style code blocks to HTML
  const formatted = suggestion
    // Convert ```php code blocks to HTML
    .replace(/```php\n([\s\S]*?)\n```/g, '<pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mt-2 mb-2"><code class="text-sm">$1</code></pre>')
    // Convert any remaining ``` code blocks
    .replace(/```\n([\s\S]*?)\n```/g, '<pre class="bg-gray-900 text-gray-100 p-4 rounded-lg overflow-x-auto mt-2 mb-2"><code class="text-sm">$1</code></pre>')
    // Convert inline code with backticks
    .replace(/`([^`]+)`/g, '<code class="bg-gray-100 px-1 py-0.5 rounded text-sm">$1</code>')
    // Convert newlines to line breaks
    .replace(/\n/g, '<br>')

  return formatted
}

onMounted(() => {
  console.log('onMounted - availableProviders:', props.availableProviders)

  const providers = Object.keys(props.availableProviders)
  console.log('Available provider keys:', providers)

  if (providers.length > 0) {
    selectedProvider.value = providers[0]
    console.log('Selected provider set to:', selectedProvider.value)
    onProviderChange()
  } else {
    console.log('No providers available')
  }
})
</script>
