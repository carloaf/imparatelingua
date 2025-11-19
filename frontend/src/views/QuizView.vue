<template>
  <div class="quiz-view">
    <div class="container mx-auto px-4 py-8">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6 flex items-center justify-between">
        <div>
          <h2 class="text-2xl font-bold">{{ exam?.name }}</h2>
          <p class="text-gray-600">{{ exam?.description }}</p>
        </div>
        <div class="text-right">
          <div class="text-sm text-gray-600">Progresso</div>
          <div class="text-2xl font-bold">{{ currentQuestionIndex + 1 }} / {{ questions.length }}</div>
        </div>
      </div>

      <!-- Progress Bar -->
      <div class="w-full bg-gray-200 rounded-full h-3 mb-6">
        <div
          class="bg-blue-500 h-3 rounded-full transition-all duration-300"
          :style="{ width: progressPercentage + '%' }"
        ></div>
      </div>

      <!-- Section Navigation -->
      <div v-if="sections.length" class="flex flex-wrap gap-3 mb-6">
        <button
          v-for="section in sections"
          :key="section.name"
          @click="goToSection(section.startIndex)"
          class="px-4 py-2 rounded-lg text-sm font-semibold border transition-colors"
          :class="section.isCurrent
            ? 'bg-blue-600 text-white border-blue-600'
            : 'bg-white text-gray-700 border-gray-200 hover:border-blue-400'"
        >
          <span class="block">{{ section.name }}</span>
          <span class="block text-xs font-normal">{{ section.answered }}/{{ section.total }} respondidas</span>
        </button>
      </div>

      <!-- Loading -->
      <div v-if="loading" class="text-center py-12">
        <p class="text-gray-600 text-lg">Carregando questões...</p>
      </div>

      <!-- Error -->
      <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <p>{{ error }}</p>
      </div>

      <!-- Question -->
      <div v-else-if="currentQuestion">
        <QuestionCard
          :key="`question-${currentQuestion.id}-${currentQuestionIndex}`"
          :question="currentQuestion"
          :question-index="currentQuestionIndex"
          :saved-answer="currentSavedAnswer"
          @answer="handleAnswer"
        />

        <!-- Navigation -->
        <div class="flex justify-between items-center mt-6">
          <button
            @click="previousQuestion"
            :disabled="currentQuestionIndex === 0"
            class="px-6 py-3 rounded-lg font-semibold transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="currentQuestionIndex === 0 ? 'bg-gray-300 text-gray-600' : 'bg-gray-200 text-gray-800 hover:bg-gray-300'"
          >
            ← Anterior
          </button>

          <button
            v-if="currentQuestionIndex < questions.length - 1"
            @click="nextQuestion"
            class="px-6 py-3 rounded-lg font-semibold transition-colors bg-blue-500 text-white hover:bg-blue-600"
          >
            {{ isCurrentAnswered ? 'Próxima' : 'Pular' }} →
          </button>

          <button
            v-else
            @click="finishQuiz"
            class="bg-green-500 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-600 transition-colors"
          >
            Finalizar Quiz ✓
          </button>
        </div>
      </div>

      <!-- No questions -->
      <div v-else class="text-center py-12">
        <p class="text-gray-600 text-lg">Nenhuma questão disponível para este exame.</p>
        <router-link to="/exams" class="text-blue-500 hover:underline mt-4 inline-block">
          Voltar para exames
        </router-link>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { examService, questionService } from '@/services/api'
import QuestionCard from '@/components/QuestionCard.vue'

export default {
  name: 'QuizView',
  components: {
    QuestionCard
  },
  setup() {
    const route = useRoute()
    const router = useRouter()
    
    const exam = ref(null)
    const questions = ref([])
    const currentQuestionIndex = ref(0)
    const loading = ref(true)
    const error = ref(null)
    const userAnswers = ref({})

    const getSectionName = (question) => {
      if (!question) return 'Seção'
      return question?.category?.name || question?.category_name || question?.category || 'Seção'
    }

    const currentQuestion = computed(() => {
      return questions.value[currentQuestionIndex.value]
    })

    const currentSavedAnswer = computed(() => {
      const question = currentQuestion.value
      if (!question) return null
      return userAnswers.value[question.id] || null
    })

    const isCurrentAnswered = computed(() => Boolean(currentSavedAnswer.value))

    const progressPercentage = computed(() => {
      if (questions.value.length === 0) return 0
      return ((currentQuestionIndex.value + 1) / questions.value.length) * 100
    })

    const sections = computed(() => {
      if (!questions.value.length) return []

      const grouped = []
      const sectionMap = new Map()
      const currentIndex = currentQuestionIndex.value

      questions.value.forEach((question, index) => {
        const name = getSectionName(question)

        if (!sectionMap.has(name)) {
          const section = {
            name,
            startIndex: index,
            endIndex: index,
            questionIndices: [index]
          }
          sectionMap.set(name, section)
          grouped.push(section)
        } else {
          const section = sectionMap.get(name)
          section.endIndex = index
          section.questionIndices.push(index)
        }
      })

      return grouped.map(section => {
        const answeredCount = section.questionIndices.reduce((count, questionIndex) => {
          const question = questions.value[questionIndex]
          return count + (question && userAnswers.value[question.id] ? 1 : 0)
        }, 0)

        return {
          name: section.name,
          startIndex: section.startIndex,
          endIndex: section.endIndex,
          total: section.questionIndices.length,
          answered: answeredCount,
          isCurrent: currentIndex >= section.startIndex && currentIndex <= section.endIndex
        }
      })
    })

    const fetchExamQuestions = async () => {
      try {
        loading.value = true
        error.value = null
        const examId = route.params.id
        const response = await examService.getQuestions(examId)
        exam.value = response.data.data.exam
        questions.value = response.data.data.questions
      } catch (err) {
        error.value = 'Erro ao carregar questões. Por favor, tente novamente.'
        console.error('Error fetching questions:', err)
      } finally {
        loading.value = false
      }
    }

    const handleAnswer = async (answerData) => {
      userAnswers.value = {
        ...userAnswers.value,
        [answerData.questionId]: answerData
      }

      // Enviar resposta para API
      try {
        await questionService.answer(answerData.questionId, {
          answer_id: answerData.answerId,
          user_id: 1 // Temporário
        })
      } catch (err) {
        console.error('Error submitting answer:', err)
      }
    }

    const nextQuestion = () => {
      if (currentQuestionIndex.value < questions.value.length - 1) {
        currentQuestionIndex.value++
      }
    }

    const previousQuestion = () => {
      if (currentQuestionIndex.value > 0) {
        currentQuestionIndex.value--
      }
    }

    const goToSection = (startIndex) => {
      console.log('goToSection called with:', startIndex)
      if (typeof startIndex !== 'number') return
      if (startIndex < 0 || startIndex >= questions.value.length) return
      
      currentQuestionIndex.value = startIndex
      console.log('currentQuestionIndex set to:', currentQuestionIndex.value)
      console.log('currentQuestion:', currentQuestion.value)
    }

    const finishQuiz = () => {
      const answers = Object.values(userAnswers.value)

      if (!answers.length) {
        alert('Responda pelo menos uma questão antes de finalizar o quiz.')
        return
      }

      const correct = answers.filter(a => a.isCorrect).length
      const total = answers.length
      const accuracy = total ? ((correct / total) * 100).toFixed(1) : '0.0'

      alert(`Quiz finalizado!\n\nAcertos: ${correct}/${total}\nPrecisão: ${accuracy}%`)
      router.push('/')
    }

    onMounted(() => {
      fetchExamQuestions()
    })

    return {
      exam,
      questions,
      currentQuestion,
      currentQuestionIndex,
      loading,
      error,
      sections,
      isCurrentAnswered,
      currentSavedAnswer,
      progressPercentage,
      handleAnswer,
      nextQuestion,
      previousQuestion,
      goToSection,
      finishQuiz
    }
  }
}
</script>

<style scoped>
.quiz-view {
  min-height: 100vh;
  background-color: #f7fafc;
}
</style>
