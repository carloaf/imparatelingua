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
          <div class="lesson-content prose prose-lg max-w-none">
            <div v-html="lesson.content_italian" class="formatted-content"></div>
          </div>
        </div>

        <!-- Exercises Section -->
        <div v-if="lesson.exercises && lesson.exercises.length > 0" class="bg-white rounded-lg shadow-md p-8">
          <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <span class="mr-2">✏️</span>
            Exercícios ({{ completedExercises }}/{{ lesson.exercises.length }})
          </h2>

          <div class="space-y-6">
            <div
              v-for="(exercise, index) in lesson.exercises"
              :key="index"
              class="border rounded-lg p-6 transition-all"
              :class="getExerciseBorderClass(index)"
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
                <button
                  v-for="(option, optIndex) in exercise.options"
                  :key="optIndex"
                  @click="selectAnswer(index, optIndex)"
                  :disabled="exerciseAnswers[index] !== null"
                  class="w-full flex items-start p-3 rounded-lg border transition-all text-left"
                  :class="getOptionClass(index, optIndex)"
                >
                  <span class="font-semibold mr-3 min-w-[2rem]">
                    {{ String.fromCharCode(65 + optIndex) }})
                  </span>
                  <span class="flex-1">{{ option }}</span>
                  <span v-if="exerciseAnswers[index] !== null" class="ml-2">
                    {{ getOptionIcon(index, optIndex) }}
                  </span>
                </button>
              </div>

              <!-- Exercise Feedback -->
              <div v-if="exerciseAnswers[index] !== null" class="mt-4 space-y-3">
                <!-- Result Message -->
                <div 
                  class="p-4 rounded-lg border-2"
                  :class="exerciseAnswers[index] === getCorrectAnswerIndex(index) 
                    ? 'bg-green-50 border-green-200' 
                    : 'bg-red-50 border-red-200'"
                >
                  <div class="flex items-start">
                    <span 
                      class="text-2xl mr-3"
                      :class="exerciseAnswers[index] === getCorrectAnswerIndex(index) 
                        ? 'text-green-600' 
                        : 'text-red-600'"
                    >
                      {{ exerciseAnswers[index] === getCorrectAnswerIndex(index) ? '✓' : '✗' }}
                    </span>
                    <div class="flex-1">
                      <p 
                        class="font-semibold mb-1"
                        :class="exerciseAnswers[index] === getCorrectAnswerIndex(index) 
                          ? 'text-green-800' 
                          : 'text-red-800'"
                      >
                        {{ exerciseAnswers[index] === getCorrectAnswerIndex(index) 
                          ? '🎉 Correto!' 
                          : '❌ Incorreto' }}
                      </p>
                      <p 
                        :class="exerciseAnswers[index] === getCorrectAnswerIndex(index) 
                          ? 'text-green-700' 
                          : 'text-red-700'"
                      >
                        <strong>Resposta correta:</strong> {{ exercise.correct_answer }}
                      </p>
                    </div>
                  </div>
                </div>

                <!-- Explanation (if available) -->
                <div 
                  v-if="exercise.explanation" 
                  class="p-4 bg-blue-50 border border-blue-200 rounded-lg"
                >
                  <p class="font-semibold text-blue-800 mb-1">💡 Explicação:</p>
                  <p class="text-blue-700">{{ exercise.explanation }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Progress Summary -->
          <div v-if="completedExercises > 0" class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-center justify-between">
              <div>
                <p class="font-semibold text-blue-900">
                  Progresso: {{ completedExercises }}/{{ lesson.exercises.length }} exercícios
                </p>
                <p class="text-sm text-blue-700">
                  Taxa de acerto: {{ exerciseScore }}% 
                  ({{ correctAnswers }}/{{ completedExercises }} corretas)
                </p>
              </div>
              <div class="text-3xl">
                {{ completedExercises === lesson.exercises.length ? '🏆' : '📝' }}
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
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { lessonService } from '@/services/api'

const route = useRoute()
const router = useRouter()

const lesson = ref(null)
const loading = ref(true)
const error = ref(null)
const completingLesson = ref(false)

// Exercise interaction state
const exerciseAnswers = ref([]) // Array to store selected answer index for each exercise

// Computed properties for exercise statistics
const completedExercises = computed(() => {
  return exerciseAnswers.value.filter(answer => answer !== null).length
})

const correctAnswers = computed(() => {
  if (!lesson.value?.exercises) return 0
  return exerciseAnswers.value.filter((answer, index) => {
    return answer !== null && answer === getCorrectAnswerIndex(index)
  }).length
})

const exerciseScore = computed(() => {
  if (completedExercises.value === 0) return 0
  return Math.round((correctAnswers.value / completedExercises.value) * 100)
})

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

// Get the index of the correct answer for an exercise
const getCorrectAnswerIndex = (exerciseIndex) => {
  const exercise = lesson.value.exercises[exerciseIndex]
  if (!exercise?.options || !exercise?.correct_answer) return -1
  
  return exercise.options.findIndex(option => 
    option.trim().toLowerCase() === exercise.correct_answer.trim().toLowerCase()
  )
}

// Handle answer selection
const selectAnswer = (exerciseIndex, optionIndex) => {
  if (exerciseAnswers.value[exerciseIndex] !== null) return // Already answered
  
  exerciseAnswers.value[exerciseIndex] = optionIndex
}

// Get CSS classes for exercise border based on answer state
const getExerciseBorderClass = (exerciseIndex) => {
  const answer = exerciseAnswers.value[exerciseIndex]
  if (answer === null) {
    return 'border-gray-200'
  }
  const isCorrect = answer === getCorrectAnswerIndex(exerciseIndex)
  return isCorrect ? 'border-green-300 bg-green-50/30' : 'border-red-300 bg-red-50/30'
}

// Get CSS classes for option buttons
const getOptionClass = (exerciseIndex, optionIndex) => {
  const selectedAnswer = exerciseAnswers.value[exerciseIndex]
  const correctIndex = getCorrectAnswerIndex(exerciseIndex)
  
  // Not answered yet - show hover effect
  if (selectedAnswer === null) {
    return 'border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer'
  }
  
  // This is the correct answer
  if (optionIndex === correctIndex) {
    return 'border-green-500 bg-green-100 text-green-900 font-semibold'
  }
  
  // This was selected but is wrong
  if (optionIndex === selectedAnswer && optionIndex !== correctIndex) {
    return 'border-red-500 bg-red-100 text-red-900'
  }
  
  // Not selected and not correct - gray out
  return 'border-gray-200 bg-gray-50 text-gray-500 opacity-60'
}

// Get icon for options after answering
const getOptionIcon = (exerciseIndex, optionIndex) => {
  const correctIndex = getCorrectAnswerIndex(exerciseIndex)
  const selectedAnswer = exerciseAnswers.value[exerciseIndex]
  
  if (optionIndex === correctIndex) {
    return '✓'
  }
  
  if (optionIndex === selectedAnswer && optionIndex !== correctIndex) {
    return '✗'
  }
  
  return ''
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
    
    // Initialize exercise answers array
    if (lesson.value.exercises) {
      exerciseAnswers.value = new Array(lesson.value.exercises.length).fill(null)
    }
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

/* Estilos para conteúdo formatado das lições */
.lesson-content :deep(h2) {
  font-size: 1.875rem;
  font-weight: 700;
  color: #1f2937;
  margin-top: 2rem;
  margin-bottom: 1rem;
  border-bottom: 3px solid #3b82f6;
  padding-bottom: 0.5rem;
}

.lesson-content :deep(h3) {
  font-size: 1.5rem;
  font-weight: 600;
  color: #374151;
  margin-top: 1.5rem;
  margin-bottom: 0.75rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.lesson-content :deep(h4) {
  font-size: 1.25rem;
  font-weight: 600;
  color: #4b5563;
  margin-top: 1rem;
  margin-bottom: 0.5rem;
}

.lesson-content :deep(.intro) {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 1.5rem;
  border-radius: 0.75rem;
  margin-bottom: 2rem;
  font-size: 1.125rem;
  line-height: 1.75;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.lesson-content :deep(.verbs-section) {
  background: #f9fafb;
  border-left: 4px solid #3b82f6;
  padding: 1.5rem;
  margin: 1.5rem 0;
  border-radius: 0.5rem;
}

.lesson-content :deep(.verbs-section h4) {
  color: #3b82f6;
  font-size: 1.25rem;
  margin-top: 0;
}

.lesson-content :deep(.rule-box) {
  background: #eff6ff;
  border: 2px solid #3b82f6;
  padding: 1.5rem;
  border-radius: 0.75rem;
  margin: 1.5rem 0;
}

.lesson-content :deep(.rule-box strong) {
  color: #1e40af;
  font-size: 1.125rem;
}

.lesson-content :deep(.tip-box) {
  background: #fef3c7;
  border-left: 4px solid #f59e0b;
  padding: 1.5rem;
  margin: 1.5rem 0;
  border-radius: 0.5rem;
}

.lesson-content :deep(.tip-box h4) {
  color: #d97706;
  margin-top: 0;
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.lesson-content :deep(.important-box) {
  background: #fee2e2;
  border: 2px solid #ef4444;
  padding: 1.5rem;
  border-radius: 0.75rem;
  margin: 1.5rem 0;
}

.lesson-content :deep(.example) {
  background: #f0fdf4;
  border-left: 3px solid #10b981;
  padding: 1rem;
  margin: 0.75rem 0;
  font-style: italic;
  color: #047857;
  border-radius: 0.25rem;
}

.lesson-content :deep(.error) {
  background: #fee2e2;
  border-left: 3px solid #ef4444;
  padding: 1rem;
  margin: 0.75rem 0;
  color: #b91c1c;
  border-radius: 0.25rem;
}

.lesson-content :deep(.story-box) {
  background: #fef3c7;
  padding: 1.5rem;
  border-radius: 0.75rem;
  margin: 1.5rem 0;
  line-height: 1.8;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.lesson-content :deep(.comparison-table) {
  margin: 1.5rem 0;
  overflow-x: auto;
}

.lesson-content :deep(table) {
  width: 100%;
  border-collapse: collapse;
  margin: 1rem 0;
}

.lesson-content :deep(table th) {
  background: #3b82f6;
  color: white;
  padding: 0.75rem;
  text-align: left;
  font-weight: 600;
}

.lesson-content :deep(table td) {
  padding: 0.75rem;
  border-bottom: 1px solid #e5e7eb;
}

.lesson-content :deep(table tbody tr:hover) {
  background: #f9fafb;
}

.lesson-content :deep(ul) {
  list-style: none;
  padding-left: 0;
  margin: 1rem 0;
}

.lesson-content :deep(ul li) {
  padding: 0.5rem 0 0.5rem 2rem;
  position: relative;
}

.lesson-content :deep(ul li::before) {
  content: '✓';
  position: absolute;
  left: 0.5rem;
  color: #10b981;
  font-weight: bold;
}

.lesson-content :deep(.frequency-list li::before) {
  content: '📊';
}

.lesson-content :deep(strong) {
  color: #1f2937;
  font-weight: 600;
}

.lesson-content :deep(em) {
  color: #6b7280;
  font-style: italic;
}

.lesson-content :deep(u) {
  text-decoration: underline;
  text-decoration-color: #3b82f6;
  text-decoration-thickness: 2px;
}

.lesson-content :deep(p) {
  margin: 1rem 0;
  line-height: 1.75;
}

.lesson-content :deep(.timeline) {
  background: #f0fdf4;
  padding: 1.5rem;
  border-radius: 0.75rem;
  margin: 1rem 0;
  font-family: monospace;
}

.lesson-content :deep(.example-section) {
  margin: 1rem 0;
  padding-left: 1rem;
}
</style>
