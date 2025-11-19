<template>
  <div class="exam-list">
    <h2 class="text-2xl font-bold mb-6">Exames Disponíveis</h2>
    
    <div v-if="loading" class="text-center py-8">
      <p class="text-gray-600">Carregando exames...</p>
    </div>

    <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
      <p>{{ error }}</p>
    </div>

    <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div 
        v-for="exam in exams" 
        :key="exam.id"
        class="relative bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow p-6 cursor-pointer border-2 border-transparent hover:border-blue-500"
        @click="selectExam(exam)"
      >
        <button
          type="button"
          class="absolute top-3 right-3 text-gray-400 hover:text-red-500 transition-colors"
          title="Excluir exame"
          :disabled="deletingExamId === exam.id"
          @click.stop="deleteExam(exam)"
        >
          <span v-if="deletingExamId === exam.id" class="text-xs">...</span>
          <span v-else aria-hidden="true">✕</span>
        </button>
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <span :class="levelClass(exam.level)" class="px-3 py-1 rounded-full text-sm font-semibold">
              {{ exam.level }}
            </span>
            <span v-if="exam.is_official" class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-semibold flex items-center gap-1">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
              </svg>
              CILS
            </span>
          </div>
          <span class="text-gray-500 text-sm">{{ exam.session || exam.year }}</span>
        </div>
        
        <h3 class="text-xl font-bold mb-2">{{ exam.name }}</h3>
        <p class="text-gray-600 text-sm mb-4">{{ exam.description }}</p>
        
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500">
            <i class="fas fa-question-circle mr-1"></i>
            {{ exam.questions?.length || 0 }} questões
          </span>
          <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition-colors">
            Começar
          </button>
        </div>
      </div>
    </div>

    <div v-if="!loading && exams.length === 0" class="text-center py-8">
      <p class="text-gray-600">Nenhum exame disponível no momento.</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { examService } from '@/services/api'

export default {
  name: 'ExamList',
  setup() {
    const router = useRouter()
    const exams = ref([])
    const loading = ref(true)
    const error = ref(null)
    const deletingExamId = ref(null)

    const fetchExams = async () => {
      try {
        loading.value = true
        error.value = null
        const response = await examService.getAll()
        exams.value = response.data.data
      } catch (err) {
        error.value = 'Erro ao carregar exames. Por favor, tente novamente.'
        console.error('Error fetching exams:', err)
      } finally {
        loading.value = false
      }
    }

    const selectExam = (exam) => {
      router.push({ name: 'exam', params: { id: exam.id } })
    }

    const deleteExam = async (exam) => {
      if (!exam?.id) return
      const confirmed = window.confirm(`Excluir o exame "${exam.name}" e todas as suas questões?`)
      if (!confirmed) {
        return
      }

      try {
        deletingExamId.value = exam.id
        await examService.delete(exam.id)
        exams.value = exams.value.filter(item => item.id !== exam.id)
      } catch (err) {
        console.error('Error deleting exam:', err)
        alert('Não foi possível excluir o exame. Tente novamente.')
      } finally {
        deletingExamId.value = null
      }
    }

    const levelClass = (level) => {
      const classes = {
        'A1': 'bg-green-100 text-green-800',
        'A2': 'bg-green-200 text-green-900',
        'B1': 'bg-yellow-100 text-yellow-800',
        'B2': 'bg-yellow-200 text-yellow-900',
        'C1': 'bg-red-100 text-red-800',
        'C2': 'bg-red-200 text-red-900'
      }
      return classes[level] || 'bg-gray-100 text-gray-800'
    }

    onMounted(() => {
      fetchExams()
    })

    return {
      exams,
      loading,
      error,
      selectExam,
      levelClass,
      deleteExam,
      deletingExamId
    }
  }
}
</script>

<style scoped>
.exam-list {
  max-width: 1200px;
  margin: 0 auto;
  padding: 2rem;
}
</style>
