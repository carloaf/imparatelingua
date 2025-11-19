<template>
  <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-7xl mx-auto">
      <!-- Header -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">
          📚 Cursos de Italiano
        </h1>
        <p class="text-xl text-gray-600">
          Aprenda italiano de forma estruturada e progressiva
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="text-center py-12">
        <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-primary"></div>
        <p class="mt-4 text-gray-600">Carregando cursos...</p>
      </div>

      <!-- Error State -->
      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
        <strong class="font-bold">Erro!</strong>
        <span class="block sm:inline"> {{ error }}</span>
      </div>

      <!-- Courses Grid -->
      <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <div
          v-for="course in courses"
          :key="course.id"
          class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300"
        >
          <!-- Course Image -->
          <div class="h-48 bg-gradient-to-br from-blue-400 to-indigo-600 relative">
            <div class="absolute inset-0 flex items-center justify-center">
              <div class="text-center text-white">
                <div class="text-6xl mb-2">🇮🇹</div>
                <div class="text-2xl font-bold">{{ course.level }}</div>
              </div>
            </div>
          </div>

          <!-- Course Content -->
          <div class="p-6">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">
              {{ course.title }}
            </h2>
            <p class="text-gray-600 mb-4">
              {{ course.description }}
            </p>

            <!-- Course Stats -->
            <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
              <div class="flex items-center">
                <span class="mr-2">📖</span>
                <span>{{ course.total_lessons }} lições</span>
              </div>
              <div class="flex items-center">
                <span class="mr-2">⏱️</span>
                <span>{{ course.estimated_time }}min</span>
              </div>
            </div>

            <!-- Level Badge -->
            <div class="mb-4">
              <span 
                class="inline-block px-3 py-1 rounded-full text-sm font-semibold"
                :class="getLevelBadgeClass(course.level)"
              >
                Nível {{ course.level }}
              </span>
            </div>

            <!-- Action Button -->
            <router-link
              :to="`/courses/${course.id}`"
              class="block w-full text-center bg-primary hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200"
            >
              Iniciar Curso
            </router-link>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div v-if="!loading && !error && courses.length === 0" class="text-center py-12">
        <div class="text-6xl mb-4">📚</div>
        <h3 class="text-2xl font-bold text-gray-900 mb-2">Nenhum curso disponível</h3>
        <p class="text-gray-600">Novos cursos serão adicionados em breve!</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { courseService } from '@/services/api'

const courses = ref([])
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

const loadCourses = async () => {
  try {
    loading.value = true
    error.value = null
    const response = await courseService.getAll()
    courses.value = response.data
  } catch (err) {
    console.error('Erro ao carregar cursos:', err)
    error.value = 'Não foi possível carregar os cursos. Tente novamente mais tarde.'
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadCourses()
})
</script>

<style scoped>
/* Adiciona animações suaves */
.hover\:shadow-xl {
  transition: box-shadow 0.3s ease;
}
</style>
