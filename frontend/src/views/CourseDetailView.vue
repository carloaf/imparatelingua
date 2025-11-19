<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
      <!-- Back Button -->
      <router-link
        to="/courses"
        class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6"
      >
        <span class="mr-2">←</span> Voltar aos cursos
      </router-link>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        <p class="mt-4 text-gray-600">Carregando curso...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
        <strong class="font-bold">Erro!</strong>
        <span class="block sm:inline"> {{ error }}</span>
      </div>

      <!-- Course Content -->
      <div v-else-if="course">
        <!-- Course Header -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
          <div class="flex items-start justify-between">
            <div>
              <h1 class="text-4xl font-bold text-gray-900 mb-2">
                {{ course.title }}
              </h1>
              <p class="text-xl text-gray-600 mb-4">
                {{ course.description }}
              </p>
              <div class="flex items-center gap-4">
                <span 
                  class="inline-block px-4 py-2 rounded-full text-sm font-semibold"
                  :class="getLevelBadgeClass(course.level)"
                >
                  Nível {{ course.level }}
                </span>
                <div class="text-gray-600">
                  📖 {{ course.lessons?.length || 0 }} lições
                </div>
                <div class="text-gray-600">
                  ⏱️ {{ getTotalTime() }}min
                </div>
              </div>
            </div>
            <div class="text-6xl">🇮🇹</div>
          </div>
        </div>

        <!-- Lessons List -->
        <div class="space-y-4">
          <h2 class="text-2xl font-bold text-gray-900 mb-4">Lições</h2>
          
          <div
            v-for="(lesson, index) in course.lessons"
            :key="lesson.id"
            class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden"
          >
            <div class="p-6">
              <div class="flex items-start justify-between">
                <div class="flex-1">
                  <!-- Lesson Header -->
                  <div class="flex items-center gap-3 mb-2">
                    <span class="text-2xl font-bold text-gray-400">
                      {{ index + 1 }}
                    </span>
                    <h3 class="text-xl font-bold text-gray-900">
                      {{ lesson.title }}
                    </h3>
                    <span 
                      class="px-3 py-1 rounded-full text-xs font-semibold"
                      :class="getLessonTypeBadge(lesson.lesson_type)"
                    >
                      {{ getLessonTypeLabel(lesson.lesson_type) }}
                    </span>
                  </div>

                  <!-- Lesson Info -->
                  <div class="flex items-center gap-4 text-sm text-gray-600 mb-3">
                    <div>⏱️ {{ lesson.estimated_time }}min</div>
                    <div>📊 Dificuldade: {{ lesson.difficulty }}/5</div>
                  </div>

                  <!-- Progress Bar -->
                  <div v-if="lesson.progress" class="mb-3">
                    <div class="flex items-center justify-between text-sm mb-1">
                      <span class="text-gray-600">Progresso</span>
                      <span class="font-semibold text-primary">
                        {{ lesson.progress.completion_percentage }}%
                      </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-primary h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${lesson.progress.completion_percentage}%` }"
                      ></div>
                    </div>
                  </div>

                  <!-- Status Badge -->
                  <div v-if="lesson.progress">
                    <span 
                      class="inline-block px-3 py-1 rounded-full text-xs font-semibold"
                      :class="getStatusBadge(lesson.progress.status)"
                    >
                      {{ getStatusLabel(lesson.progress.status) }}
                    </span>
                  </div>
                </div>

                <!-- Action Button -->
                <router-link
                  :to="`/lesson/${lesson.id}`"
                  class="ml-4 bg-primary hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors duration-200"
                >
                  {{ lesson.progress?.status === 'completed' ? 'Revisar' : 'Iniciar' }}
                </router-link>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!course.lessons || course.lessons.length === 0" class="text-center py-12 bg-white rounded-lg">
            <div class="text-6xl mb-4">📚</div>
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Nenhuma lição disponível</h3>
            <p class="text-gray-600">As lições serão adicionadas em breve!</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute } from 'vue-router'
import { courseService } from '@/services/api'

const route = useRoute()
const course = ref(null)
const loading = ref(true)
const error = ref(null)

const getLevelBadgeClass = (level) => {
  const levelColors = {
    'A1': 'bg-green-100 text-green-800',
    'A2': 'bg-blue-100 text-blue-800',
    'B1': 'bg-yellow-100 text-yellow-800',
    'B2': 'bg-orange-100 text-orange-800',
    'C1': 'bg-red-100 text-red-800',
    'C2': 'bg-purple-100 text-purple-800'
  }
  return levelColors[level] || 'bg-gray-100 text-gray-800'
}

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

const getStatusBadge = (status) => {
  const statusColors = {
    'not_started': 'bg-gray-100 text-gray-800',
    'in_progress': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800'
  }
  return statusColors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    'not_started': 'Não iniciado',
    'in_progress': 'Em progresso',
    'completed': 'Concluído'
  }
  return labels[status] || status
}

const getTotalTime = () => {
  if (!course.value?.lessons) return 0
  return course.value.lessons.reduce((total, lesson) => total + (lesson.estimated_time || 0), 0)
}

const loadCourse = async () => {
  try {
    loading.value = true
    error.value = null
    const courseId = route.params.id
    const response = await courseService.getById(courseId)
    course.value = response.data
  } catch (err) {
    console.error('Erro ao carregar curso:', err)
    error.value = 'Não foi possível carregar o curso. Tente novamente mais tarde.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCourse()
})
</script>
