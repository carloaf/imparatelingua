<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-5xl mx-auto">
      <!-- Back Button -->
      <button
        @click="goBack"
        class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6"
      >
        <span class="mr-2">←</span> Voltar
      </button>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        <p class="mt-4 text-gray-600">Carregando lição...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Erro!</strong>
        <span class="block sm:inline"> {{ error }}</span>
      </div>

      <!-- Lesson Content -->
      <div v-else-if="lesson" class="space-y-6">
        <!-- Lesson Header -->
        <div class="bg-white rounded-lg shadow-lg p-8">
          <div class="flex items-start justify-between mb-4">
            <div class="flex-1">
              <h1 class="text-3xl font-bold text-gray-900 mb-2">
                {{ lesson.title }}
              </h1>
              <div class="flex items-center gap-4 text-sm text-gray-600">
                <span 
                  class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="getLessonTypeBadge(lesson.lesson_type)"
                >
                  {{ getLessonTypeLabel(lesson.lesson_type) }}
                </span>
                <div>⏱️ {{ lesson.estimated_time }}min</div>
                <div>📊 Dificuldade: {{ lesson.difficulty }}/5</div>
              </div>
            </div>
            <div class="text-5xl">📖</div>
          </div>

          <!-- Progress Bar -->
          <div v-if="lesson.progress">
            <div class="flex items-center justify-between text-sm mb-2">
              <span class="text-gray-600">Seu progresso</span>
              <span class="font-semibold text-primary">
                {{ lesson.progress.completion_percentage }}%
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div 
                class="bg-primary h-3 rounded-full transition-all duration-300"
                :style="{ width: `${lesson.progress.completion_percentage}%` }"
              ></div>
            </div>
          </div>
        </div>

        <!-- Lesson Content -->
        <div class="bg-white rounded-lg shadow-md p-8">
          <div class="prose max-w-none">
            <div class="text-lg leading-relaxed whitespace-pre-wrap">
              {{ lesson.content_italian }}
            </div>
          </div>
        </div>

        <!-- Exercises Section -->
        <div v-if="lesson.exercises && lesson.exercises.length > 0" class="bg-white rounded-lg shadow-md p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-2">✏️</span>
            Exercícios
          </h2>

          <div class="space-y-6">
            <div
              v-for="(exercise, index) in lesson.exercises"
              :key="index"
              class="border border-gray-200 rounded-lg p-6"
            >
              <!-- Exercise Header -->
              <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    Exercício {{ index + 1 }}
                  </h3>
                  <p class="text-gray-700 whitespace-pre-wrap">{{ exercise.question }}</p>
                </div>
                <span class="ml-4 text-2xl">{{ getExerciseIcon(exercise.type) }}</span>
              </div>

              <!-- Exercise Options (for multiple choice) -->
              <div v-if="exercise.options" class="space-y-2">
                <div
                  v-for="(option, optIndex) in exercise.options"
                  :key="optIndex"
                  class="flex items-start p-3 rounded-lg border border-gray-200 hover:bg-gray-50 cursor-pointer transition-colors"
                >
                  <span class="font-semibold text-gray-700 mr-3">
                    {{ String.fromCharCode(65 + optIndex) }})
                  </span>
                  <span class="text-gray-800">{{ option }}</span>
                </div>
              </div>

              <!-- Exercise Answer Display -->
              <div class="mt-4 p-4 bg-green-50 border border-green-200 rounded-lg">
                <div class="flex items-start">
                  <span class="text-green-600 mr-2">✓</span>
                  <div>
                    <p class="font-semibold text-green-800 mb-1">Resposta correta:</p>
                    <p class="text-green-700">{{ exercise.correct_answer }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-between items-center bg-white rounded-lg shadow-md p-6">
          <button
            @click="goBack"
            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-semibold transition-colors"
          >
            ← Voltar
          </button>

          <button
            @click="markAsComplete"
            :disabled="completingLesson"
            :class="[
              'px-6 py-3 rounded-lg font-semibold transition-colors',
              lesson.progress?.status === 'completed'
                ? 'bg-green-500 hover:bg-green-600 text-white'
                : 'bg-primary hover:bg-blue-700 text-white'
            ]"
          >
            <span v-if="completingLesson">Salvando...</span>
            <span v-else-if="lesson.progress?.status === 'completed'">✓ Concluído</span>
            <span v-else>Marcar como Concluído</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { lessonService } from '@/services/api'

const route = useRoute()
const router = useRouter()

const lesson = ref(null)
const loading = ref(true)
const error = ref(null)
const completingLesson = ref(false)

const getLessonTypeBadge = (type) => {
  const typeColors = {
    'theory': 'bg-blue-100 text-blue-800',
    'grammar': 'bg-purple-100 text-purple-800',
    'vocabulary': 'bg-green-100 text-green-800',
    'pronunciation': 'bg-pink-100 text-pink-800',
    'exercise': 'bg-orange-100 text-orange-800'
  }
  return typeColors[type] || 'bg-gray-100 text-gray-800'
}

const getLessonTypeLabel = (type) => {
  const labels = {
    'theory': 'Teoria',
    'grammar': 'Gramática',
    'vocabulary': 'Vocabulário',
    'pronunciation': 'Pronúncia',
    'exercise': 'Exercício'
  }
  return labels[type] || type
}

const getExerciseIcon = (type) => {
  const icons = {
    'pronunciation': '🗣️',
    'fill_blank': '✍️',
    'multiple_choice': '☑️',
    'translate': '🔄'
  }
  return icons[type] || '📝'
}

const goBack = () => {
  router.back()
}

const markAsComplete = async () => {
  try {
    completingLesson.value = true
    const lessonId = route.params.id
    await lessonService.complete(lessonId)
    
    // Update local state
    if (lesson.value.progress) {
      lesson.value.progress.status = 'completed'
      lesson.value.progress.completion_percentage = 100
    }
    
    // Show success message
    alert('✓ Lição concluída com sucesso!')
  } catch (err) {
    console.error('Erro ao marcar lição como concluída:', err)
    alert('Erro ao salvar progresso. Tente novamente.')
  } finally {
    completingLesson.value = false
  }
}

const loadLesson = async () => {
  try {
    loading.value = true
    error.value = null
    const lessonId = route.params.id
    const response = await lessonService.getById(lessonId)
    lesson.value = response.data
  } catch (err) {
    console.error('Erro ao carregar lição:', err)
    error.value = 'Não foi possível carregar a lição. Tente novamente mais tarde.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadLesson()
})
</script>

<style scoped>
.prose {
  color: #374151;
  line-height: 1.75;
}
</style>
