<template>
  <div class="progress-stats">
    <h2 class="text-2xl font-bold mb-6">Suas Estatísticas</h2>

    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-600">Carregando estatísticas...</p>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <p>{{ error }}</p>
    </div>

    <div v-else-if="stats">
      <!-- Estatísticas Gerais -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="stat-card bg-blue-50 border-2 border-blue-200 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-blue-600">{{ stats.overall.total_answered }}</div>
          <div class="text-sm text-gray-600 mt-2">Questões Respondidas</div>
        </div>

        <div class="stat-card bg-green-50 border-2 border-green-200 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-green-600">{{ stats.overall.correct_answers }}</div>
          <div class="text-sm text-gray-600 mt-2">Respostas Corretas</div>
        </div>

        <div class="stat-card bg-red-50 border-2 border-red-200 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-red-600">{{ stats.overall.incorrect_answers }}</div>
          <div class="text-sm text-gray-600 mt-2">Respostas Incorretas</div>
        </div>

        <div class="stat-card bg-purple-50 border-2 border-purple-200 rounded-lg p-6 text-center">
          <div class="text-3xl font-bold text-purple-600">{{ stats.overall.accuracy }}%</div>
          <div class="text-sm text-gray-600 mt-2">Taxa de Acerto</div>
        </div>
      </div>

      <!-- Performance por Categoria -->
      <div class="mb-8">
        <h3 class="text-xl font-bold mb-4">Performance por Categoria</h3>
        <div class="space-y-3">
          <div
            v-for="category in stats.by_category"
            :key="category.name"
            class="bg-white rounded-lg shadow p-4"
          >
            <div class="flex justify-between items-center mb-2">
              <span class="font-semibold">{{ category.name }}</span>
              <span class="text-sm text-gray-600">
                {{ category.correct }}/{{ category.total }} corretas
              </span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
              <div
                class="h-3 rounded-full transition-all duration-300"
                :class="getAccuracyColor(category.accuracy)"
                :style="{ width: category.accuracy + '%' }"
              ></div>
            </div>
            <div class="text-right text-sm font-semibold mt-1"
                 :class="getAccuracyTextColor(category.accuracy)">
              {{ category.accuracy }}%
            </div>
          </div>
        </div>
      </div>

      <!-- Performance por Nível -->
      <div>
        <h3 class="text-xl font-bold mb-4">Performance por Nível</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div
            v-for="level in stats.by_level"
            :key="level.level"
            class="bg-white rounded-lg shadow p-4 text-center"
          >
            <div class="text-2xl font-bold mb-2" :class="getLevelColor(level.level)">
              {{ level.level }}
            </div>
            <div class="text-gray-600 text-sm mb-2">
              {{ level.correct }}/{{ level.total }} corretas
            </div>
            <div class="text-xl font-bold" :class="getAccuracyTextColor(level.accuracy)">
              {{ level.accuracy }}%
            </div>
          </div>
        </div>
      </div>
    </div>

    <div v-else class="text-center py-8">
      <p class="text-gray-600">Nenhuma estatística disponível ainda. Comece a responder questões!</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { progressService } from '@/services/api'

export default {
  name: 'ProgressStats',
  props: {
    userId: {
      type: Number,
      default: 1 // Temporário, depois virá da autenticação
    }
  },
  setup(props) {
    const stats = ref(null)
    const loading = ref(true)
    const error = ref(null)

    const fetchStatistics = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await progressService.getStatistics(props.userId)
        stats.value = response.data.data
      } catch (err) {
        error.value = 'Erro ao carregar estatísticas. Por favor, tente novamente.'
        console.error('Error fetching statistics:', err)
      } finally {
        loading.value = false
      }
    }

    const getAccuracyColor = (accuracy) => {
      if (accuracy >= 80) return 'bg-green-500'
      if (accuracy >= 60) return 'bg-yellow-500'
      return 'bg-red-500'
    }

    const getAccuracyTextColor = (accuracy) => {
      if (accuracy >= 80) return 'text-green-600'
      if (accuracy >= 60) return 'text-yellow-600'
      return 'text-red-600'
    }

    const getLevelColor = (level) => {
      const colors = {
        'A1': 'text-green-600',
        'A2': 'text-green-700',
        'B1': 'text-yellow-600',
        'B2': 'text-yellow-700',
        'C1': 'text-red-600',
        'C2': 'text-red-700'
      }
      return colors[level] || 'text-gray-600'
    }

    onMounted(() => {
      fetchStatistics()
    })

    return {
      stats,
      loading,
      error,
      getAccuracyColor,
      getAccuracyTextColor,
      getLevelColor
    }
  }
}
</script>

<style scoped>
.progress-stats {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}

.stat-card {
  transition: transform 0.2s;
}

.stat-card:hover {
  transform: translateY(-4px);
}
</style>
